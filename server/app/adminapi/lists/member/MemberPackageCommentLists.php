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

namespace app\adminapi\lists\member;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\model\member\MemberPackageComment;

class MemberPackageCommentLists extends BaseAdminDataLists implements ListsExcelInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/4/14 2:53 下午
     */
    public function where()
    {
        $where = [];
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['name','like','%'.$this->params['user_info'].'%'];
        }
        if (isset($this->params['member_package_id']) && $this->params['member_package_id'] != '') {
            $where[] = ['member_package_id','=',$this->params['member_package_id']];
        }
        if (isset($this->params['comment_level']) && $this->params['comment_level'] != '') {
            switch ($this->params['comment_level']) {
                case 1://好评
                    $where[] = ['comment_level','>',3];
                    break;
                case 2://中评
                    $where[] = ['comment_level','=',3];
                    break;
                case 3://差评
                    $where[] = ['comment_level','<',3];
                    break;
            }
        }
        if (isset($this->params['type']) && $this->params['type'] != '') {
            $where[] = ['type','=',$this->params['type']];
        }
        if (isset($this->params['status']) && $this->params['status'] != '') {
            $where[] = ['status','=',$this->params['status']];
        }

        return $where;
    }

    /**
     * @notes 购买评价列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/4/14 2:56 下午
     */
    public function lists(): array
    {
        $lists = MemberPackageComment::field('id,type,member_package_id,image,name,comment_content,comment_level,status,create_time')
            ->where($this->where())
            ->append(['type_desc','member_package','comment_level_desc','status_desc'])
            ->order(['id'=>'desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * @notes 购买评价数量
     * @return int
     * @author ljj
     * @date 2023/4/14 2:56 下午
     */
    public function count(): int
    {
        return MemberPackageComment::where($this->where())->count();
    }

    /**
     * @notes 导出文件名
     * @return string
     * @author ljj
     * @date 2023/8/24 2:49 下午
     */
    public function setFileName(): string
    {
        return '购买评价列表';
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
            'name' => '评价用户',
            'member_package' => '评价套餐',
            'comment_level_desc' => '评价等级',
            'comment_content' => '评价内容',
            'type_desc' => '评价类型',
            'status_desc' => '是否显示',
            'create_time' => '评价时间',
        ];
    }
}