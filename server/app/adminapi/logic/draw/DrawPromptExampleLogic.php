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
use app\common\model\draw\DrawPromptExample;

/**
 * 绘画关键词示例
 * Class DrawPromptExampleLogic
 * @package app\adminapi\logic\draw
 */
class DrawPromptExampleLogic extends BaseLogic
{

    /**
     * @notes 添加
     * @param array $params
     * @return DrawPromptExample|\think\Model
     * @author 段誉
     * @date 2023/7/17 15:53
     */
    public static function add(array $params)
    {
        return DrawPromptExample::create([
            'prompt' => $params['prompt'],
            'prompt_en' => $params['prompt_en'],
            'sort' => $params['sort'] ?? 0,
            'status' => $params['status'],
        ]);
    }

    /**
     * @notes 编辑
     * @param array $params
     * @author 段誉
     * @date 2023/6/28 9:42
     */
    public static function edit(array $params)
    {
        DrawPromptExample::where('id', $params['id'])->update([
            'prompt' => $params['prompt'],
            'prompt_en' => $params['prompt_en'],
            'sort' => $params['sort'] ?? 0,
            'status' => $params['status'],
            'update_time' => time()
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
        return DrawPromptExample::destroy($params['id']);
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
        return DrawPromptExample::findOrEmpty($params['id'])->toArray();
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
        $promptRecords = DrawPromptExample::where(['id' => $id])->findOrEmpty();
        if ($promptRecords->isEmpty()) {
            return true;
        }
        $promptRecords->status = $promptRecords->status ? 0 : 1;
        $promptRecords->save();
        return true;
    }


}