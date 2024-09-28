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
namespace app\adminapi\validate\setting;

use app\common\model\SensitiveWord;
use app\common\validate\BaseValidate;

/**
 * 敏感词过滤
 * Class SensitiveWordValidate
 * @package app\adminapi\validate\setting
 */
class SensitiveWordValidate extends BaseValidate
{
    protected $rule = [
        'id'    => 'require',
        'word'  => 'require|array',
        'status'=> 'require|in:0,1',
        'is_sensitive'      => 'require|in:0,1',
    ];

    protected $message = [
        'id.require'    => '请选择敏感词',
        'word.require'  => '请输入敏感词',
        'word.array'    => '敏感词值错误',
        'status.require'=> '请选择状态',
        'status.in'     => '状态值错误',
        'is_sensitive.require'  => '请选择是否开启敏感词库',
        'is_sensitive.in'       => '敏感词库值错误',
    ];

    public function sceneAdd()
    {
        return $this->only(['word','status']);
    }

    public function sceneId()
    {
        return $this->only(['id']);
    }

    public function sceneSetConfig()
    {
        return $this->only(['is_sensitive']);
    }

    public function sceneEdit()
    {
        return $this->only(['id','word','status']);
    }
}