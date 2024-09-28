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

use app\common\model\draw\DrawCategory;
use app\common\logic\BaseLogic;


/**
 * DrawCategory逻辑
 * Class DrawCategoryLogic
 * @package app\adminapi\logic\draw
 */
class DrawCategoryLogic extends BaseLogic
{
    /**
     * @notes 列表
     * @param $params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/14 17:01
     */
    public static function lists($params)
    {
        $where = [];
        if (!empty($params['name'])) {
            $where[] = ['name', 'like', '%' . $params['name'] . '%'];
        }
        if (isset($params['status']) && $params['status'] != '') {
            $where[] = ['status', '=', $params['status']];
        }
        $lists = DrawCategory::where($where)
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();

        $pid = 0;
        if (!empty($lists)) {
            $pid = min(array_column($lists, 'pid'));
        }
        return linear_to_tree($lists, "children", "id", "pid", $pid);
    }


    /**
     * @notes
     * @param array $params
     * @return DrawCategory|\think\Model
     * @author 段誉
     * @date 2023/6/14 16:20
     */
    public static function add(array $params)
    {
        $level = 0;
        if ($params['pid']) {
            $level = DrawCategory::where(['id' => $params['pid']])->value('level');
        }

        return DrawCategory::create([
            'pid' => $params['pid'],
            'name' => $params['name'],
            'sort' => $params['sort'] ?? 0,
            'level' => $level + 1,
            'status' => $params['status']
        ]);
    }

    /**
     * @notes 编辑
     * @param array $params
     * @return DrawCategory
     * @author 段誉
     * @date 2023/6/14 16:20
     */
    public static function edit(array $params)
    {
        $level = 0;
        if ($params['pid']) {
            $level = DrawCategory::where(['id' => $params['pid']])->value('level');
        }

        return DrawCategory::where('id', $params['id'])->update([
            'pid' => $params['pid'],
            'name' => $params['name'],
            'sort' => $params['sort'] ?? 0,
            'level' => $level + 1,
            'status' => $params['status']
        ]);
    }

    /**
     * @notes 删除
     * @param array $params
     * @return bool
     * @author 段誉
     * @date 2023/6/14 16:21
     */
    public static function delete(array $params): bool
    {
        return DrawCategory::destroy($params['id']);
    }

    /**
     * @notes 详情
     * @param $params
     * @return array
     * @author 段誉
     * @date 2023/6/14 16:21
     */
    public static function detail($params): array
    {
        return DrawCategory::findOrEmpty($params['id'])->toArray();
    }

    /**
     * @notes 状态切换
     * @param int $id
     * @return bool
     * @author 段誉
     * @date 2023/6/15 10:56
     */
    public static function status(int $id)
    {
        $questionCategory = DrawCategory::where(['id' => $id])->findOrEmpty();
        if ($questionCategory->isEmpty()) {
            return true;
        }
        $questionCategory->status = $questionCategory->status ? 0 : 1;
        $questionCategory->save();
        return true;
    }


    /**
     * @notes 全部列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/28 10:15
     */
    public static function all()
    {
        return DrawCategory::where(['status' => 1])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();
    }

}