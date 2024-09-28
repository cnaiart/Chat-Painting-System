<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\api\logic;


use app\common\enum\DrawSquareEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\model\draw\DrawRecords;
use app\common\model\draw\DrawSquare;
use app\common\model\draw\DrawSquareCategory;
use app\common\model\draw\DrawSquarePraise;
use app\common\model\user\User;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\storage\Driver as StorageDriver;

class DrawSquareLogic extends BaseLogic
{
    /**
     * @notes 分类列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/8/31 4:22 下午
     */
    public function categoryLists($user_id)
    {
        $lists = DrawSquareCategory::field('id,name,image')
            ->where(['status'=>1])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();
        if ($user_id) {
            array_unshift($lists,['id'=>0,'name'=>'喜欢','image'=>FileService::getFileUrl('resource/image/adminapi/default/nav02.png')]);
        }

        return $lists;
    }

    /**
     * @notes 分享至绘画广场
     * @param $params
     * @return bool|string
     * @author ljj
     * @date 2023/8/31 4:59 下午
     */
    public function add($params)
    {
        try {
            $is_allow_share = ConfigService::get('draw_square_config', 'is_allow_share', config('project.draw_square_config.is_allow_share'));
            if (!$is_allow_share) {
                throw new \Exception('绘画广场分享未开启，请联系管理员');
            }
            $is_auto_pass = ConfigService::get('draw_square_config', 'is_auto_pass', config('project.draw_square_config.is_auto_pass'));
            $draw_records = DrawRecords::where(['id'=>$params['draw_records_id']])->findOrEmpty()->toArray();
            $image =  $draw_records['image'] ?? '';
            $thumbnail =  $draw_records['thumbnail'] ?? '';
            $prompts =  $draw_records['prompt_desc'] ?? '';

            if (isset($params['is_base64']) && $params['is_base64']) {
                if (!isset($params['base64']) || empty($params['base64'])) {
                    throw new \Exception('参数缺失');
                }
                $base64 = $params['base64'];
                if (strstr($base64,",")){
                    $base64 = explode(',',$base64);
                    $base64 = $base64[1];
                }

                // 存储引擎
                $config = [
                    'default' => ConfigService::get('storage', 'default', 'local'),
                    'engine' => ConfigService::get('storage'),
                ];
                $StorageDriver = new StorageDriver($config);
                //保存图片
                $saveDir = 'uploads/draw_square/';
                $filename = md5($base64).'.png';
                $fileUrl = $saveDir.$filename;
                //获取文件，如果之前生成过，不用重新生成
                if (($config['default'] == 'local' && !file_exists($fileUrl)) || ($config['default'] != 'local' && !getRemoteFileExists(FileService::getFileUrl($fileUrl)))) {
                    if (!file_exists($saveDir)) {
                        mkdir($saveDir, 0775, true);
                    }
                    //保存到本地
                    file_put_contents($fileUrl, base64_decode($base64));
                    //上传到oss
                    if('local' != $config['default']){
                        $localFileUrl = request()->domain(true).'/'.$fileUrl;
                        if (!$StorageDriver->fetch($localFileUrl,$fileUrl)) {
                            throw new \Exception('保存失败:' . $StorageDriver->getError());
                        }
                        //删除本地文件
                        unlink($fileUrl);
                    }
                }

                $image =  $fileUrl;
                $thumbnail =  (new DrawSquare())->getThumbnail(FileService::getFileUrl($fileUrl));
            }


            //分享奖励，同一条绘画记录已分享过的不再奖励   通过审核在发放奖励
            $share_num = DrawSquare::where(['operate_id'=>$params['user_id'],'source'=>DrawSquareEnum::SOURCE_USER,'draw_records_id'=>$params['draw_records_id'],'verify_status'=>1])->count();
            if ($share_num == 0 && $is_auto_pass == 1) {
                $rewardsConfig = [
                    'chat_rewards' => ConfigService::get('draw_square_config','chat_rewards', config('project.draw_square_config.chat_rewards')),
                    'draw_rewards' => ConfigService::get('draw_square_config','draw_rewards', config('project.draw_square_config.draw_rewards')),
                    'max_share' => ConfigService::get('draw_square_config','max_share', config('project.draw_square_config.max_share')),
                ];
                $share_num = DrawSquare::where(['operate_id'=>$params['user_id'],'source'=>DrawSquareEnum::SOURCE_USER,'verify_status'=>1])->whereDay('create_time')->group('draw_records_id')->count();
                if ($share_num < $rewardsConfig['max_share']) {
                    if (!empty($rewardsConfig['chat_rewards']) && $rewardsConfig['chat_rewards'] > 0) {
                        User::update(['balance'=>['inc',$rewardsConfig['chat_rewards']]],['id'=>$params['user_id']]);
                        // 记录账户流水
                        AccountLogLogic::add(
                            $params['user_id'],
                            AccountLogEnum::UM_INC_DRAW_SHARE_GIVE,
                            AccountLogEnum::INC,
                            $rewardsConfig['chat_rewards']
                        );
                    }
                    if (!empty($rewardsConfig['draw_rewards']) && $rewardsConfig['draw_rewards'] > 0) {
                        User::update(['balance_draw'=>['inc',$rewardsConfig['draw_rewards']]],['id'=>$params['user_id']]);
                        // 记录账户流水
                        AccountLogLogic::add(
                            $params['user_id'],
                            AccountLogEnum::DRAW_INC_DRAW_SHARE_GIVE,
                            AccountLogEnum::INC,
                            $rewardsConfig['draw_rewards']
                        );
                    }
                }
            }
            

            DrawSquare::create([
                'source' => DrawSquareEnum::SOURCE_USER,
                'operate_id' => $params['user_id'],
                'category_id' => $params['category_id'] ?? 0,
                'prompts' => $prompts,
                'image' => $image,
                'thumbnail' => $thumbnail,
                'verify_status' => $is_auto_pass,
                'is_show' => $is_auto_pass,
                'draw_records_id' => $params['draw_records_id'],
            ]);

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @notes 点赞操作
     * @param $params
     * @return bool
     * @author ljj
     * @date 2024/1/25 6:14 下午
     */
    public function praise($params)
    {
        if ($params['praise'] == 1) {
            DrawSquarePraise::create([
                'square_id' => $params['id'],
                'user_id' => $params['user_id'],
            ]);
        } else {
            DrawSquarePraise::where(['square_id'=>$params['id'],'user_id'=>$params['user_id']])->delete();
        }

        return true;
    }
}