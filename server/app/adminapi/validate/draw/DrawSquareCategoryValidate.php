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

namespace app\adminapi\validate\draw;

use app\common\model\draw\DrawSquare;
use app\common\model\draw\DrawSquareCategory;
use app\common\validate\BaseValidate;


class DrawSquareCategoryValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
        'name' => 'require|checkName',
        'sort' => 'number',
        'status' => 'require',
    ];

    protected $message = [
        'id.require' => '参数缺失',
        'name.require' => '请输入分类名称',
        'sort.require' => '排序值错误',
        'status.require' => '请选择状态',
    ];


    public function sceneAdd()
    {
        return $this->only(['name', 'status', 'sort']);
    }

    public function sceneEdit()
    {
        return $this->only(['id', 'name', 'status', 'sort']);
    }

    public function sceneDel()
    {
        return $this->only(['id'])->append("id", "checkDel");
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneStatus()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 校验分类名称
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/8/31 11:01 上午
     */
    public function checkName($value, $rule, $data)
    {
        $where[] = ['name','=',$value];
        if (isset($data['id'])) {
            $where[] = ['id','<>',$data['id']];
        }
        $cate = DrawSquareCategory::where($where)->findOrEmpty();
        if (!$cate->isEmpty()) {
            return "分类名称已被使用";
        }

        return true;
    }

    /**
     * @notes 校验删除
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/8/31 11:02 上午
     */
    public function checkDel($value, $rule, $data)
    {
        $prompt = DrawSquare::where('category_id', $value)->findOrEmpty();
        if (!$prompt->isEmpty()) {
            return "分类已使用， 无法删除";
        }

        return true;
    }
}