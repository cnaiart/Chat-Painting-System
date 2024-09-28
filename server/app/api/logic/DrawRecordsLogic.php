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

use app\common\enum\DrawSquareEnum;
use app\common\logic\BaseLogic;
use app\common\model\draw\DrawRecords;
use app\common\model\draw\DrawRecordsCollect;
use app\common\model\draw\DrawSquare;
use app\common\service\FileService;


/**
 * 绘画记录逻辑
 * Class DrawRecordsLogic
 * @package app\api\logic
 */
class DrawRecordsLogic extends BaseLogic
{
    /**
     * @notes 绘图记录
     * @param $userId
     * @return DrawRecords[]|array|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/20 20:01
     */
    public static function records($userId)
    {
        $field = [
            'id', 'task_id', 'prompt', 'prompt_en', 'prompt_desc', 'prompt_other', 'status', 'image',
            'image_base', 'thumbnail', 'model', 'image_url', 'image_id', 'scale', 'able_actions',
            'fail_reason', 'no_content', 'version', 'style', 'engine', 'quality', 'create_time'
        ];
        $records = DrawRecords::field($field)
            ->where(['user_id' => $userId])
            ->order('id desc')
            ->select()
            ->toArray();

        $square = DrawSquare::where(['source'=>DrawSquareEnum::SOURCE_USER,'operate_id'=>$userId])->column('draw_records_id');

        foreach ($records as &$item) {
            if (empty($item['thumbnail'])) {
                $item['thumbnail'] = $item['image_url'];
            }
            $item['thumbnail'] = !empty($item['thumbnail']) ? FileService::getFileUrl($item['thumbnail']) : "";
            $item['image'] = !empty($item['image']) ? FileService::getFileUrl($item['image']) : "";
            $item['actions'] = json_decode($item['able_actions'], true);
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
            if (in_array($item['id'],$square)) {
                $item['is_square'] = 1;
            }

            unset($item['create_time'], $item['able_actions']);
        }
        return $records;
    }

    /**
     * @notes 收藏
     * @param $userId
     * @param $params
     * @author 段誉
     * @date 2023/6/27 11:33
     */
    public static function collect($userId, $params)
    {
        if ($params['status']) {
            // 收藏
            DrawRecordsCollect::create([
                'user_id' => $userId,
                'records_id' => $params['records_id'],
            ]);
        } else {
            // 取消收藏
            DrawRecordsCollect::where([
                'user_id' => $userId,
                'records_id' => $params['records_id'],
            ])->delete();
        }
    }

    /**
     * @notes 删除绘画记录
     * @param $userId
     * @param $ids
     * @author 段誉
     * @date 2023/6/25 11:27
     */
    public static function delete($userId, $ids)
    {
        DrawRecords::destroy(function ($query) use ($userId, $ids) {
            $query->where('user_id', $userId)
                ->whereIn('id', $ids);
        });
    }

    /**
     * @notes 任务详情
     * @param $params
     * @param $userId
     * @return array
     * @author 段誉
     * @date 2023/7/24 15:11
     */
    public static function getDrawDetail($params, $userId)
    {
        if (empty($params['records_id'])) {
            return [];
        }

        if (!is_array($params['records_id'])) {
            $params['records_id'] = [$params['records_id']];
        }

        $field = [
            'id', 'task_id', 'prompt', 'prompt_en', 'status', 'image', 'image_base', 'thumbnail',
            'image_url', 'image_id', 'scale', 'able_actions', 'fail_reason', 'create_time'
        ];

        $lists = DrawRecords::field($field)->where('id', 'in', $params['records_id'])
            ->where('user_id', $userId)
            ->select()->toArray();

        foreach ($lists as &$item) {
            if (!empty($item['thumbnail'])) {
                $item['thumbnail'] = FileService::getFileUrl($item['thumbnail']);
            } else {
                $item['thumbnail'] = $item['image_url'];
            }

            $item['loading'] = 1;
            if (time() > strtotime($item['create_time']) + 10 * 60) {
                $item['loading'] = 0;
            }
        }

        return $lists;
    }

}