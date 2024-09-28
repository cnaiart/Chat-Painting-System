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

use app\common\logic\BaseLogic;
use app\common\model\draw\DrawSquareCategory;


class DrawSquareCategoryLogic extends BaseLogic
{
    /**
     * @notes 添加分类
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/8/31 11:03 上午
     */
    public static function add($params)
    {
        DrawSquareCategory::create([
            'name' => $params['name'],
            'image' => $params['image'],
            'sort' => $params['sort'] ?? 0,
            'status' => $params['status']
        ]);
        return true;
    }

    /**
     * @notes 编辑分类
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/8/31 11:04 上午
     */
    public static function edit($params)
    {
        DrawSquareCategory::update([
            'name' => $params['name'],
            'image' => $params['image'],
            'sort' => $params['sort'] ?? 0,
            'status' => $params['status']
        ],['id'=>$params['id']]);
        return true;
    }

    /**
     * @notes 删除分类
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/8/31 11:05 上午
     */
    public static function del($params)
    {
        return DrawSquareCategory::destroy($params['id']);
    }

    /**
     * @notes 分类详情
     * @param $params
     * @return array
     * @author ljj
     * @date 2023/8/31 11:08 上午
     */
    public static function detail($params)
    {
        return DrawSquareCategory::findOrEmpty($params['id'])->toArray();
    }

    /**
     * @notes 状态切换
     * @param $id
     * @return bool
     * @author ljj
     * @date 2023/8/31 11:09 上午
     */
    public static function status($id)
    {
        $category = DrawSquareCategory::where(['id' => $id])->findOrEmpty();
        if ($category->isEmpty()) {
            return true;
        }
        $category->status = $category->status ? 0 : 1;
        $category->save();
        return true;
    }
}