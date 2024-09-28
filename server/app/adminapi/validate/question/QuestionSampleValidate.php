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
namespace app\adminapi\validate\question;
use app\common\model\creation\CreationCategory;
use app\common\model\question\QuestionCategory;
use app\common\validate\BaseValidate;

/**
 * 问题示例验证类
 * Class QuestionCategoryValidate
 * @package app\adminapi\validate\question
 */
class QuestionSampleValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
        'category_id'   => 'require|checkCategory',
        'sort' => 'require|number',
        'content'   => 'require',
        'status' => 'require',
    ];


    protected $message = [
        'id.require' => '请选择问题示例',
        'category_id.require'   => '请选择分类',
        'content.require'   => '请输入内容',
        'sort.require' => '请输入排序',
        'sort.number'          => '排序值错误',
        'status.require' => '请选择状态',
    ];

    protected function sceneAdd()
    {
        return $this->remove('id', true);
    }

    protected function sceneId()
    {
        return $this->only(['id']);
    }




    protected function checkCategory($value,$rule,$data)
    {
        $category = QuestionCategory::where(['id'=>$value])->findOrEmpty();
        if($category->isEmpty()){
            return '分类不存在';
        }
        return true;

    }
}