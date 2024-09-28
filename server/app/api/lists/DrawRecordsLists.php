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

use app\api\logic\DrawLogic;
use app\common\enum\ContentCensorEnum;
use app\common\enum\DrawEnum;
use app\common\enum\DrawSquareEnum;
use app\common\model\draw\DrawRecords;
use app\common\model\draw\DrawSquare;
use app\common\service\FileService;
use think\facade\Config;

/**
 * 绘图记录
 * Class DrawRecordsLists
 * @package app\api\lists
 */
class DrawRecordsLists extends BaseApiDataLists
{

    public function queryWhere(): array
    {
        $where = [];
        if (isset($this->params['status']) && $this->params['status'] > 0) {
            $where[] = ['status', '=', $this->params['status']];
        }
        if (isset($this->params['model']) && $this->params['model'] != '') {
            $where[] = ['model', '=', $this->params['model']];
        }
        return $where;
    }

    /**
     * @notes 列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/20 12:04
     */
    public function lists(): array
    {
        $field = [
            'id', 'task_id', 'prompt', 'prompt_en', 'prompt_desc', 'prompt_other', 'status', 'image',
            'image_base', 'thumbnail', 'model', 'image_url', 'image_id', 'scale', 'able_actions',
            'fail_reason', 'no_content', 'version', 'style', 'engine', 'quality', 'censor_status',
            'create_time'
        ];
        $records = DrawRecords::field($field)
            ->where($this->queryWhere())
            ->where(['user_id' => $this->userId])
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id desc')
            ->select()
            ->toArray();

        $square = DrawSquare::where(['source' => DrawSquareEnum::SOURCE_USER, 'operate_id' => $this->userId])
            ->column('draw_records_id');

        // 模型计费名称
        $drawBillConfig = DrawLogic::drawBillingConfig()['billing_config'] ?? [];

        // 不合规默认图片
        $defaultCensorImg = FileService::getFileUrl(Config::get('project.default_image.draw_error'));

        foreach ($records as &$item) {
            if (empty($item['thumbnail'])) {
                $item['thumbnail'] = $item['image_url'];
            }
            $item['thumbnail'] = !empty($item['thumbnail']) ? FileService::getFileUrl($item['thumbnail']) : "";
            $item['image'] = !empty($item['image']) ? FileService::getFileUrl($item['image']) : "";

            if ($item['model'] == DrawEnum::API_YIJIAN_SD) {
                $item['actions'] = [];
                $item['image_url'] = !empty($item['image_url']) ? FileService::getFileUrl($item['image_url']) : "";
            } else {
                $item['actions'] = json_decode($item['able_actions'], true);
            }

            $item['able_edit'] = 0;
            if (strtotime($item['create_time']) + 3600 > time() && !empty($item['actions'])) {
                $item['able_edit'] = 1;
            }

            $item['loading'] = 1;
            if (time() > strtotime($item['create_time']) + 10 * 60) {
                $item['loading'] = 0;
            }

            //判断是否已分享至广场
            $item['is_square'] = 0;
            if (in_array($item['id'], $square)) {
                $item['is_square'] = 1;
            }

            $item['image_base'] = !empty($item['image_base']) ? FileService::getFileUrl($item['image_base']) : '';

            $item['draw_model'] = $drawBillConfig[$item['model']]['alias'] ?? DrawEnum::getDefaultBillingConfig($item['model'])['name'];

            unset($item['able_actions']);

            // 图片审核不合规的记录
            if ($item['censor_status'] == ContentCensorEnum::CENSOR_STATUS_NON_COMPLIANCE) {
                $item['image'] = $defaultCensorImg;
                $item['image_url'] = $defaultCensorImg;
                $item['thumbnail'] = $defaultCensorImg;
            }
        }
        return $records;
    }

    /**
     * @notes 数量
     * @return int
     * @author 段誉
     * @date 2023/6/20 12:03
     */
    public function count(): int
    {
        return DrawRecords::where(['user_id' => $this->userId])
            ->where($this->queryWhere())
            ->count();
    }


}