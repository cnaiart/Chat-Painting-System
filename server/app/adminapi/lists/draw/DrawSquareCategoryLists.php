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

namespace app\adminapi\lists\draw;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\model\draw\DrawSquareCategory;

class DrawSquareCategoryLists extends BaseAdminDataLists implements ListsExcelInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/8/31 10:47 上午
     */
    public function where()
    {
        $where = [];
        if (!empty($this->params['name'])) {
            $where[] = ['name', 'like', '%' . $this->params['name'] . '%'];
        }
        if (isset($this->params['status']) && $this->params['status'] != '') {
            $where[] = ['status', '=', $this->params['status']];
        }

        return $where;
    }

    /**
     * @notes 绘画广场分类列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/8/31 10:51 上午
     */
    public function lists(): array
    {
        $lists = DrawSquareCategory::where($this->where())
            ->append(['status_desc'])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * @notes 绘画广场分类数量
     * @return int
     * @author ljj
     * @date 2023/8/31 10:51 上午
     */
    public function count(): int
    {
        return DrawSquareCategory::where($this->where())->count();
    }

    /**
     * @notes 导出文件名
     * @return string
     * @author ljj
     * @date 2023/8/24 2:49 下午
     */
    public function setFileName(): string
    {
        return '绘画广场分类列表';
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
            'name' => '分类名称',
            'status_desc' => '状态',
            'sort' => '排序',
            'create_time' => '创建时间',
        ];
    }
}