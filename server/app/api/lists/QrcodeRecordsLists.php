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

namespace app\api\lists;

use app\common\enum\qrcode\MewxEnum;
use app\common\enum\qrcode\QrcodeEnum;
use app\common\model\qrcode\QrcodeRecords;
use app\common\service\FileService;

/**
 * 艺术二维码
 * Class QrcodeRecordsLists
 * @package app\api\lists
 */
class QrcodeRecordsLists extends BaseApiDataLists
{

    /**
     * @notes 查询条件
     * @return array
     * @author mjf
     * @date 2023/10/17 18:00
     */
    public function queryWhere(): array
    {
        $where = [];
        if (isset($this->params['status']) && $this->params['status'] > 0) {
            $where[] = ['status', '=', $this->params['status']];
        }
        return $where;
    }

    /**
     * @notes 列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author mjf
     * @date 2023/10/17 17:59
     */
    public function lists(): array
    {
        $records = QrcodeRecords::where($this->queryWhere())
            ->where(['user_id' => $this->userId])
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id desc')
            ->hidden(['notify_snap', 'use_tokens', 'update_time', 'delete_time'])
            ->select()
            ->toArray();

        foreach ($records as &$item) {
            $item['image'] = !empty($item['image']) ? FileService::getFileUrl($item['image']) : "";

            $item['loading'] = 1;
            if (time() > strtotime($item['create_time']) + 10 * 60) {
                $item['loading'] = 0;
            }

            $item['model_text'] = "";
            $item['template_text'] = "";
            if ($item['way'] == QrcodeEnum::WAY_TEMPLATE) {
                $item['template_text'] = QrcodeEnum::getTemplateName($item['model'], $item['template_id']);
            } else {
                $item['model_text'] = MewxEnum::getModel($item['model_id']);
            }

            if ($item['type'] == QrcodeEnum::TYPE_IMAGE && !empty($item['qr_image'])) {
                $item['qr_image'] = FileService::getFileUrl($item['qr_image']);
            }
        }
        return $records;
    }

    /**
     * @notes 数量
     * @return int
     * @author mjf
     * @date 2023/10/17 18:00
     */
    public function count(): int
    {
        return QrcodeRecords::where(['user_id' => $this->userId])
            ->where($this->queryWhere())
            ->count();
    }


}