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
namespace app\adminapi\validate\skill;
use app\common\model\skill\SkillCategory;
use app\common\validate\BaseValidate;

/**
 * 技能类别验证器类
 * Class CreationCategoryValidate
 * @package app\adminapi\validate\creation
 */
class SkillCategoryValidate extends BaseValidate
{

    protected $rule = [
        'id'    => 'require',
        'name'  => 'require|max:64|unique:'.SkillCategory::class.',name',
        'sort'  => 'require|number',
        'status'=> 'require',
    ];


    protected $message = [
        'id.require'    => '请选择分类',
        'name.require'  => '请输入名称',
        'name.max'      => '名称不能超过64个字符',
        'name.unique'   => '名称重复',
        'sort.require'          => '请输入排序',
        'sort.number'          => '排序值错误',
        'status.require'        => '请选择状态',
    ];

    protected function sceneAdd()
    {
        return $this->remove('id',true);
    }

    protected function sceneId()
    {
        return $this->only(['id']);
    }
}