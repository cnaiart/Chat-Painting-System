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

use app\common\model\draw\DrawPrompt;
use app\common\logic\BaseLogic;
use think\facade\Db;

/**
 * 绘画关键词
 * Class DrawPromptLogic
 * @package app\adminapi\logic\draw
 */
class DrawPromptLogic extends BaseLogic
{

    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author 段誉
     * @date 2023/6/27 18:26
     */
    public static function add(array $params)
    {
        Db::startTrans();
        try {
            $data = [
                'category_id' => $params['category_id'],
                'sort' => $params['sort'] ?? 0,
                'status' => $params['status'],
            ];

            foreach ($params['prompt'] as $item) {
                $index = strpos($item, '&');
                if ($index !== false) {
                    $data['prompt_en'] = mb_substr($item, 0, $index);
                    $data['prompt'] = mb_substr($item, $index + 1, mb_strlen($item) - 1);
                    DrawPrompt::create($data);
                }
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 编辑
     * @param array $params
     * @author 段誉
     * @date 2023/6/28 9:42
     */
    public static function edit(array $params)
    {
        DrawPrompt::where('id', $params['id'])->update([
            'category_id' => $params['category_id'],
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
        return DrawPrompt::destroy($params['id']);
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
        return DrawPrompt::findOrEmpty($params['id'])->toArray();
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
        $promptRecords = DrawPrompt::where(['id' => $id])->findOrEmpty();
        if ($promptRecords->isEmpty()) {
            return true;
        }
        $promptRecords->status = $promptRecords->status ? 0 : 1;
        $promptRecords->save();
        return true;
    }


}