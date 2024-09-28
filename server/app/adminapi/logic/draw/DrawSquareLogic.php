<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\adminapi\logic\draw;

use app\common\enum\DrawSquareEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\model\draw\DrawSquare;
use app\common\model\user\User;
use app\common\service\ConfigService;

class DrawSquareLogic extends BaseLogic
{
    /**
     * @notes 添加
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/8/31 12:14 下午
     */
    public static function add($params)
    {
        $thumbnail = (new DrawSquare())->getThumbnail($params['image']);
        DrawSquare::create([
            'source' => DrawSquareEnum::SOURCE_ADMIN,
            'operate_id' => $params['admin_id'],
            'category_id' => $params['category_id'] ?? 0,
            'prompts' => $params['prompts'],
            'prompts_cn' => $params['prompts_cn'] ?? null,
            'image' => $params['image'],
            'thumbnail' => $thumbnail,
            'is_show' => $params['is_show'],
            'verify_status' => DrawSquareEnum::VERIFY_STATUS_SUCCESS,
            'avatar' => $params['avatar'] ?? null,
            'nickname' => $params['nickname'] ?? null,
        ]);

        return true;
    }

    /**
     * @notes 编辑
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/8/31 12:16 下午
     */
    public static function edit($params)
    {
        $draw_prompts = DrawSquare::where('id',$params['id'])->findOrEmpty()->toArray();
        if ($draw_prompts['source'] == DrawSquareEnum::SOURCE_ADMIN) {
            $thumbnail = (new DrawSquare())->getThumbnail($params['image']);
            DrawSquare::update([
                'category_id' => $params['category_id'] ?? 0,
                'prompts' => $params['prompts'],
                'prompts_cn' => $params['prompts_cn'] ?? null,
                'image' => $params['image'],
                'thumbnail' => $thumbnail,
                'is_show' => $params['is_show'],
                'avatar' => $params['avatar'] ?? null,
                'nickname' => $params['nickname'] ?? null,
            ],['id'=>$params['id']]);
        }
        if ($draw_prompts['source'] == DrawSquareEnum::SOURCE_USER) {
            DrawSquare::update([
                'category_id' => $params['category_id'] ?? 0,
                'is_show' => $params['is_show'],
            ],['id'=>$params['id']]);
        }

        return true;
    }

    /**
     * @notes 详情
     * @param $params
     * @return array
     * @author ljj
     * @date 2023/8/31 12:16 下午
     */
    public static function detail($params)
    {
        $result = DrawSquare::field('*')->append(['category_name','verify_status_desc','user_info','source_desc','original_prompts'])->findOrEmpty($params['id'])->toArray();
        if ($result['category_id'] == 0) {
            $result['category_id'] = '';
        }
        if (empty($result['original_prompts']['prompt_en'])) {
            $result['original_prompts']['prompt_en'] = '-';
        }

        return $result;
    }

    /**
     * @notes 删除
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/8/31 12:17 下午
     */
    public static function del($params)
    {
        return DrawSquare::destroy($params['id']);
    }

    /**
     * @notes 显示状态
     * @param $id
     * @return bool
     * @author ljj
     * @date 2023/8/31 12:18 下午
     */
    public static function isShow($id)
    {
        $result = DrawSquare::where(['id' => $id])->findOrEmpty();
        if ($result->isEmpty()) {
            return true;
        }
        $result->is_show = $result->is_show ? 0 : 1;
        $result->save();
        return true;
    }

    /**
     * @notes 审核状态
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/8/31 12:19 下午
     */
    public static function verifyStatus($params)
    {
        foreach ($params['id'] as $id) {
            $draw_square = DrawSquare::where(['id'=>$id])->findOrEmpty()->toArray();

            //分享奖励，同一条绘画记录已分享过的不再奖励   通过审核在发放奖励
            $share_num = DrawSquare::where(['operate_id'=>$draw_square['operate_id'],'source'=>DrawSquareEnum::SOURCE_USER,'draw_records_id'=>$draw_square['draw_records_id'],'verify_status'=>1])->count();
            if ($share_num == 0 && $params['verify_status'] == 1) {
                $rewardsConfig = [
                    'chat_rewards' => ConfigService::get('draw_square_config','chat_rewards', config('project.draw_square_config.chat_rewards')),
                    'draw_rewards' => ConfigService::get('draw_square_config','draw_rewards', config('project.draw_square_config.draw_rewards')),
                    'max_share' => ConfigService::get('draw_square_config','max_share', config('project.draw_square_config.max_share')),
                ];
                $share_num = DrawSquare::where(['operate_id'=>$draw_square['operate_id'],'source'=>DrawSquareEnum::SOURCE_USER,'verify_status'=>1])->whereDay('create_time',$draw_square['create_time'])->group('draw_records_id')->count();
                if ($share_num < $rewardsConfig['max_share']) {
                    if (!empty($rewardsConfig['chat_rewards']) && $rewardsConfig['chat_rewards'] > 0) {
                        User::update(['balance'=>['inc',$rewardsConfig['chat_rewards']]],['id'=>$draw_square['operate_id']]);
                        // 记录账户流水
                        AccountLogLogic::add(
                            $draw_square['operate_id'],
                            AccountLogEnum::UM_INC_DRAW_SHARE_GIVE,
                            AccountLogEnum::INC,
                            $rewardsConfig['chat_rewards']
                        );
                    }
                    if (!empty($rewardsConfig['draw_rewards']) && $rewardsConfig['draw_rewards'] > 0) {
                        User::update(['balance_draw'=>['inc',$rewardsConfig['draw_rewards']]],['id'=>$draw_square['operate_id']]);
                        // 记录账户流水
                        AccountLogLogic::add(
                            $draw_square['operate_id'],
                            AccountLogEnum::DRAW_INC_DRAW_SHARE_GIVE,
                            AccountLogEnum::INC,
                            $rewardsConfig['draw_rewards']
                        );
                    }
                }
            }

            DrawSquare::update([
                'verify_status' => $params['verify_status'],
                'verify_result' => $params['verify_result'],
                'is_show' => $params['verify_status'] == 1 ? 1 : 0,
            ],['id'=>$id]);
        }

        return true;
    }

    /**
     * @notes 获取绘画广场配置
     * @return array
     * @author ljj
     * @date 2023/8/31 2:49 下午
     */
    public static function getConfig()
    {
        return [
//            // 允许用户分享：1-开启；0-关闭；
//            'is_allow_share' => ConfigService::get('draw_square_config', 'is_allow_share', config('project.draw_square_config.is_allow_share')),
//            // 自动通过审核：1-开启；0-关闭；
//            'is_auto_pass' => ConfigService::get('draw_square_config', 'is_auto_pass', config('project.draw_square_config.is_auto_pass')),
            // 显示用户信息：1-开启；0-关闭；
            'is_show_user' => ConfigService::get('draw_square_config', 'is_show_user', config('project.draw_square_config.is_show_user')),
//            //分享奖励对话次数
//            'chat_rewards' => ConfigService::get('draw_square_config','chat_rewards', config('project.draw_square_config.chat_rewards')),
//            //分享奖励绘画次数
//            'draw_rewards' => ConfigService::get('draw_square_config','draw_rewards', config('project.draw_square_config.draw_rewards')),
//            //每天最多分享次数
//            'max_share' => ConfigService::get('draw_square_config','max_share', config('project.draw_square_config.max_share')),
        ];
    }

    /**
     * @notes 设置绘画广场配置
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/8/31 2:50 下午
     */
    public static function setConfig($params)
    {
//        ConfigService::set('draw_square_config', 'is_allow_share', $params['is_allow_share']);
//        ConfigService::set('draw_square_config', 'is_auto_pass', $params['is_auto_pass']);
        ConfigService::set('draw_square_config', 'is_show_user', $params['is_show_user']);
//        ConfigService::set('draw_square_config', 'chat_rewards', $params['chat_rewards']);
//        ConfigService::set('draw_square_config', 'draw_rewards', $params['draw_rewards']);
//        ConfigService::set('draw_square_config', 'max_share', $params['max_share']);
        return true;
    }
}
