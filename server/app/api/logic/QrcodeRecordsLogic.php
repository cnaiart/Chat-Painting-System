<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------

namespace app\api\logic;

use app\common\logic\BaseLogic;
use app\common\model\qrcode\QrcodeRecords;


/**
 * 艺术二维码记录
 * Class QrcodeRecordsLogic
 * @package app\api\logic
 */
class QrcodeRecordsLogic extends BaseLogic
{
    /**
     * @notes 删除记录
     * @param $userId
     * @param $ids
     * @author mjf
     * @date 2023/10/17 18:03
     */
    public static function delete($userId, $ids)
    {
        QrcodeRecords::destroy(function ($query) use ($userId, $ids) {
            $query->where('user_id', $userId)
                ->whereIn('id', $ids);
        });
    }

    /**
     * @notes 详情
     * @param $params
     * @param $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author mjf
     * @date 2023/10/18 16:42
     */
    public static function getQrcodeDetail($params, $userId)
    {
        if (empty($params['records_id'])) {
            return [];
        }

        if (!is_array($params['records_id'])) {
            $params['records_id'] = [$params['records_id']];
        }

        $lists = QrcodeRecords::where('id', 'in', $params['records_id'])
            ->where('user_id', $userId)
            ->hidden(['notify_snap', 'update_time', 'delete_time'])
            ->select()->toArray();

        foreach ($lists as &$item) {
            $item['loading'] = 1;
            if (time() > strtotime($item['create_time']) + 10 * 60) {
                $item['loading'] = 0;
            }
        }

        return $lists;
    }

}