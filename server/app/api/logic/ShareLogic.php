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
use app\common\model\draw\DrawSquare;
use app\common\model\task\TaskInvite;
use app\common\model\task\TaskShare;
use app\common\model\user\User;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\wechat\WeChatMnpService;
use think\facade\Config;
use think\facade\Db;

class ShareLogic extends BaseLogic
{
    /**
     * @notes 任务
     * @param $user_id
     * @return array
     * @author ljj
     * @date 2023/4/19 4:51 下午
     */
    public function task($user_id)
    {
        $result = [];
        //分享
        $task_share = [
            'status' => ConfigService::get('task_share','status',1),
            'rewards' => ConfigService::get('task_share','rewards',1),
            'draw_rewards' => ConfigService::get('task_share','draw_rewards',1),
            'max_share' => ConfigService::get('task_share','max_share',5),
        ];
        if ($task_share['status'] == 1) {
            $share_num = TaskShare::where(['user_id'=>$user_id])->whereDay('create_time')->where('click_num','>',0)->count();
            if ($share_num > $task_share['max_share']) {
                $share_num = $task_share['max_share'];
            }
            $result[] = [
                'image' => FileService::getFileUrl(Config::get('project.default_image.share')),
                'title' => '分享给好友',
                'rewards' => $task_share['rewards'],
                'draw_rewards' => $task_share['draw_rewards'],
                'max' => $task_share['max_share'],
                'num' => $share_num,
                'type' => 3,
            ];
        }
        //邀请
        $task_invite = [
            'status' => ConfigService::get('task_invite','status',1),
            'rewards' => ConfigService::get('task_invite','rewards',1),
            'draw_rewards' => ConfigService::get('task_invite','draw_rewards',1),
            'max_invite' => ConfigService::get('task_invite','max_invite',5),
        ];
        if ($task_invite['status'] == 1) {
            $invite_num = TaskInvite::where(['user_id'=>$user_id])->whereDay('create_time')->count();
            if ($invite_num > $task_invite['max_invite']) {
                $invite_num = $task_invite['max_invite'];
            }
            $result[] = [
                'image' => FileService::getFileUrl(Config::get('project.default_image.invite')),
                'title' => '邀请新用户',
                'rewards' => $task_invite['rewards'],
                'draw_rewards' => $task_invite['draw_rewards'],
                'max' => $task_invite['max_invite'],
                'num' => $invite_num,
                'type' => 2,
            ];
        }
        //绘画分享
        $task_draw_share = [
            'is_allow_share' => ConfigService::get('draw_square_config', 'is_allow_share', config('project.draw_square_config.is_allow_share')),
            'chat_rewards' => ConfigService::get('draw_square_config','chat_rewards', config('project.draw_square_config.chat_rewards')),
            'draw_rewards' => ConfigService::get('draw_square_config','draw_rewards', config('project.draw_square_config.draw_rewards')),
            'max_share' => ConfigService::get('draw_square_config','max_share', config('project.draw_square_config.max_share')),
        ];
        if ($task_draw_share['is_allow_share'] == 1) {
            $invite_num = DrawSquare::where(['operate_id'=>$user_id,'source'=>DrawSquareEnum::SOURCE_USER])->whereDay('create_time')->group('draw_records_id')->count();
            if ($invite_num > $task_draw_share['max_share']) {
                $invite_num = $task_draw_share['max_share'];
            }
            $result[] = [
                'image' => FileService::getFileUrl(Config::get('project.default_image.share')),
                'title' => '分享绘画至广场',
                'rewards' => $task_draw_share['chat_rewards'],
                'draw_rewards' => $task_draw_share['draw_rewards'],
                'max' => $task_draw_share['max_share'],
                'num' => $invite_num,
                'type' => 4,
            ];
        }

        return $result;
    }

    /**
     * @notes 分享接口
     * @param $userInfo
     * @return false|int[]
     * @author ljj
     * @date 2023/4/18 4:58 下午
     */
    public function share($userInfo)
    {
        Db::startTrans();
        try {
            $user_id = $userInfo['user_id'] ?? 0;
            if ($user_id) {
                $share_status = ConfigService::get('task_share','status',1);
                $rewards = 0;
                $drawRewards = 0;
                if ($share_status == 1) {
                    $max_share = ConfigService::get('task_share','max_share',5);
                    $share_num = TaskShare::where(['user_id'=>$user_id])->whereDay('create_time')->count();
                    if ($max_share > $share_num) {
                        $rewards = ConfigService::get('task_share','rewards',1);
                        $drawRewards = ConfigService::get('task_share','draw_rewards',1);
                    } else {
                        $share = TaskShare::where(['user_id'=>$user_id,'click_num'=>0])->whereDay('create_time')->order('id','desc')->findOrEmpty();
                        if ($share->isEmpty()) {
                            $share = TaskShare::where(['user_id'=>$user_id])->whereDay('create_time')->order('id','desc')->findOrEmpty();
                        }

                        return ['share_id'=>$share->id ?? 0];
                    }
                }

                //添加分享记录
                //获取用户客户端
                $share = TaskShare::create([
                    'user_id'       => $user_id,
                    'channel'       => $userInfo['terminal'] ?? 0,
                    'rewards'       => $rewards,
                    'draw_rewards'  => $drawRewards,
                ]);
            }

            // 提交事务
            Db::commit();
            return ['share_id'=>$share->id ?? 0];
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 点击分享链接
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/4/18 5:13 下午
     */
    public function click($params)
    {
        Db::startTrans();
        try {
            $task_share = TaskShare::findOrEmpty($params['share_id'] ?? 0);
            if (!$task_share->isEmpty()) {
                //第一次点击，增加用户余额
                if ($task_share->click_num == 0) {
                    if($task_share->rewards > 0){
                        User::update(['balance'=>['inc',$task_share->rewards]],['id'=>$task_share->user_id]);
                        // 记录账户流水
                        AccountLogLogic::add($task_share->user_id,AccountLogEnum::UM_INC_SHARE,AccountLogEnum::INC,$task_share->rewards);
                    }
                    if($task_share->draw_rewards > 0){
                        User::update(['balance_draw'=>['inc',$task_share->draw_rewards]],['id'=>$task_share->user_id]);
                        // 记录账户流水
                        AccountLogLogic::add($task_share->user_id,AccountLogEnum::DRAW_INC_SHARE,AccountLogEnum::INC,$task_share->draw_rewards);
                    }

                }

                $task_share->click_num = $task_share->click_num + 1;
                $task_share->save();
            }

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 邀请新用户
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/4/18 5:35 下午
     */
    public function invite($params)
    {
        if ((!isset($params['share_id']) || $params['share_id'] == '') && (!isset($params['user_sn']) || $params['user_sn'] == '')) {
            return true;
        }
        $new_user = User::where(['id'=>$params['new_user_id']])->findOrEmpty()->toArray();
//        if ((time() - strtotime($new_user['create_time'])) >= 300) {
//            return true;//当前时间与注册时间差大于5分钟，认定为非新用户
//        }
        if ($new_user['is_new_user'] != 1) {
            return true;//不是非新用户
        }
        $invite = TaskInvite::where(['new_user_id'=>$params['new_user_id']])->findOrEmpty();
        if (!$invite->isEmpty()) {
            return true;//已存在邀请记录，认定为非新用户
        }
        if (isset($params['share_id'])) {
            $invite_id = TaskShare::where(['id'=>$params['share_id']])->value('user_id');
        } else {
            $invite_id = User::where(['sn'=>$params['user_sn']])->value('id');
        }

        if ($invite_id == $params['new_user_id']) {
            return true;//不能成为自己邀请人
        }

        Db::startTrans();
        try {
            $invite_status = ConfigService::get('task_invite','status',1);
            $rewards = 0;
            $drawRewards = 0;
            if ($invite_status == 1) {
                $max_invite = ConfigService::get('task_invite','max_invite',5);
                $invite_num = TaskInvite::where(['user_id'=>$invite_id])->whereDay('create_time')->count();
                if ($max_invite > $invite_num) {
                    $rewards = ConfigService::get('task_invite','rewards',1);
                    $drawRewards = ConfigService::get('task_invite','draw_rewards',1);
                }
            }

            //添加邀请记录
            TaskInvite::create([
                'user_id' => $invite_id,
                'new_user_id' => $params['new_user_id'],
                'task_share_id' => $params['share_id'] ?? 0,
                'rewards' => $rewards,
                'draw_rewards' => $drawRewards,
            ]);

            if (isset($params['share_id'])) {
                //更新分享记录
                TaskShare::update([
                    'invite_num' => ['inc',1],
                ],['id'=>$params['share_id']]);
            }

            //绑定新用户上级关系
            $first_leader = User::where(['id'=>$invite_id])->findOrEmpty()->toArray();
            User::update(['inviter_id'=>$invite_id,'first_leader'=>$invite_id,'second_leader'=>$first_leader['first_leader']],['id'=>$params['new_user_id']]);

            //增加用户余额
            if ($rewards > 0) {
                User::update(['balance'=>['inc',$rewards]],['id'=>$invite_id]);
                // 记录账户流水
                AccountLogLogic::add($invite_id,AccountLogEnum::UM_INC_INVITE,AccountLogEnum::INC,$rewards);
            }
            if($drawRewards > 0){
                User::update(['balance_draw'=>['inc',$drawRewards]],['id'=>$invite_id]);
                // 记录账户流水
                AccountLogLogic::add($invite_id,AccountLogEnum::DRAW_INC_INVITE,AccountLogEnum::INC,$drawRewards);
            }


            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 获取小程序码
     * @param $params
     * @return bool|string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @author ljj
     * @date 2023/5/23 11:50 上午
     */
    public function getMnpQrCode($params)
    {
        try {
            if (!isset($params['user_sn']) || $params['user_sn'] == '') {
                throw new \think\Exception('参数缺失');
            }
            if (!isset($params['page']) || $params['page'] == '') {
                throw new \think\Exception('路径缺失');
            }

            $data['page'] = $params['page'];
            $data['scene'] = 'user_sn='.$params['user_sn'];

            $result = (new WeChatMnpService())->createMpQrCode($data,'base64');
            return $result;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }
}