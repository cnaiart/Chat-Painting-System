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

namespace app\adminapi\lists\draw;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\model\draw\DrawCategory;
use app\common\model\draw\DrawPrompt;

/**
 * 绘图关键词
 * Class DrawPromptLists
 * @package app\adminapi\lists\draw
 */
class DrawPromptLists extends BaseAdminDataLists implements ListsExcelInterface
{

    public function queryWhere()
    {
        $where1[] = ['id', '>', 0];

        if (isset($this->params['status']) && $this->params['status'] != '') {
            $where1[] = ['status', '=', $this->params['status']];
        }

        $where2 = $where1;

        if (isset($this->params['category_id']) && $this->params['category_id'] != '') {
            $where1[] = ['category_id', '=', $this->params['category_id']];
            $partnerCate = DrawCategory::where(['pid' => $this->params['category_id']])->column('id');
            $partnerCate = !empty($partnerCate) ? $partnerCate : [];
            $where2[] = ['category_id', 'in', $partnerCate];
        }

        if (isset($this->params['prompt']) && $this->params['prompt'] != '') {
            $where1[] = ['prompt', 'like', '%' . $this->params['prompt'] . '%'];
            $where2[] = ['prompt_en', 'like', '%' . $this->params['prompt'] . '%'];
        }

        return [$where1, $where2];
    }


    /**
     * @notes 获取列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/27 18:51
     */
    public function lists(): array
    {
        $lists = DrawPrompt::whereOr($this->queryWhere())
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();

        $cateIds = array_column($lists, 'category_id');
        $cate = DrawCategory::whereIn('id', $cateIds)
            ->column('id,pid,name', 'id');

        foreach ($cate as $key => $value) {
            if (!empty($value['pid'])) {
                $partner = DrawCategory::findOrEmpty($value['pid']);
                if (!$partner->isEmpty()) {
                    $cate[$key]['name'] = $partner['name'] . '/' . $value['name'];
                }
            }
        }

        foreach ($lists as &$item) {
            $item['cate_name'] = "";
            if (isset($cate[$item['category_id']])) {
                $item['cate_name'] = $cate[$item['category_id']]['name'];
            }
            $item['status_desc'] = $item['status'] == 1 ? '开启' : '关闭';
        }

        return $lists;
    }

    /**
     * @notes 获取数量
     * @return int
     * @author 段誉
     * @date 2023/6/27 18:51
     */
    public function count(): int
    {
        return DrawPrompt::whereOr($this->queryWhere())
            ->count();
    }

    /**
     * @notes 导出文件名
     * @return string
     * @author ljj
     * @date 2023/8/24 2:49 下午
     */
    public function setFileName(): string
    {
        return '绘画词库列表';
    }

    /**
     * @notes 导出字段
     * @return string[]
     * @author ljj
     * @date 2023/8/24 2:49 下午
     */
    public function setExcelFields(): array
    {
        return [
            'prompt_en' => '关键词英文',
            'prompt' => '关键词中文',
            'cate_name' => '所属类别',
            'status_desc' => '状态',
            'sort' => '排序',
        ];
    }


}