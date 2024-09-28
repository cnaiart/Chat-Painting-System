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


use app\common\logic\BaseLogic;
use app\common\model\ChatRecords;
use app\common\model\ChatRecordsCollect;
use app\common\service\chatgpt\OpenGptService;

class ChatRecordsLogic extends BaseLogic
{

    /**
     * @notes 清除会话
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/4/25 11:37 上午
     */
    public static function cleanChat($params)
    {
        //如果有id，则删掉单个对话记录，如果按类型和会话分类id删除
        if (isset($params['id']) && $params['id'] != '') {
            ChatRecords::update(['is_show' => 0],['id'=>$params['id']]);
        } else {
            ChatRecords::update(
                ['is_show' => 0],
                [
                    'user_id'=>$params['user_id'],
                    'category_id'   => $params['category_id'] ?? 0,
                    'is_show'=>1,
                    'type'=>$params['type'],
                    'other_id'=>$params['other_id'] ?? 0
                ]
            );
        }

        return true;
    }

    /**
     * @notes 收藏会话
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/4/25 11:44 上午
     */
    public static function collect($params)
    {
        ChatRecordsCollect::create([
            'user_id' => $params['user_id'],
            'records_id' => $params['records_id'],
        ]);

        return true;
    }

    /**
     * @notes 取消收藏
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/4/25 11:46 上午
     */
    public static function cancelCollect($params)
    {
        ChatRecordsCollect::where(['id'=>$params['collect_id']])->delete();

        return true;
    }


}