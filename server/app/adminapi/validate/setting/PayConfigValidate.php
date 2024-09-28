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

namespace app\adminapi\validate\setting;


use app\common\enum\PayEnum;
use app\common\model\pay\PayConfig;
use app\common\validate\BaseValidate;


class PayConfigValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
        'name' => 'require|checkName',
        'icon' => 'require',
        'sort' => 'require|number|max:5',
        'config' => 'require|checkConfig',
        'epay_url' => 'require',
        'epay_pid' => 'require',
        'epay_key' => 'require',
    ];

    protected $message = [
        'id.require' => 'id不能为空',
        'name.require' => '支付名称不能为空',
        'icon.require' => '支付图标不能为空',
        'sort.require' => '排序不能为空',
        'sort,number' => '排序必须是纯数字',
        'sort.max' => '排序最大不能超过五位数',
        'config.require' => '支付参数缺失',
        'epay_url.require' => '请输入接口地址',
        'epay_pid.require' => '请输入商户ID',
        'epay_key.require' => '请输入商户秘钥',
    ];

    public function sceneGet()
    {
        return $this->only(['id']);
    }

    public function sceneSet()
    {
        return $this->only(['id','name','icon','sort','config']);
    }

    public function sceneSetEpayConfig()
    {
        return $this->only(['epay_url','epay_pid','epay_key']);
    }


    /**
     * @notes 校验支付配置记录
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/2/23 16:19
     */
    public function checkConfig($config, $rule, $data)
    {
        $result = PayConfig::where('id', $data['id'])->find();
        if (empty($result)) {
            return '支付方式不存在';
        }

        if ($result['pay_way'] == PayEnum::WECHAT_PAY) {
            if (empty($config['interface_version'])) {
                return '微信支付接口版本不能为空';
            }
            if (empty($config['merchant_type'])) {
                return '商户类型不能为空';
            }
            if (empty($config['mch_id'])) {
                return '微信支付商户号不能为空';
            }
            if (empty($config['pay_sign_key'])) {
                return '商户API密钥不能为空';
            }
            if (empty($config['apiclient_cert'])) {
                return '微信支付证书不能为空';
            }
            if (empty($config['apiclient_key'])) {
                return '微信支付证书密钥不能为空';
            }
        }
        if ($result['pay_way'] == PayEnum::ALI_PAY) {
            if (empty($config['mode'])) {
                return '模式不能为空';
            }
            if (!in_array($config['mode'],['secret_key','certificate'])) {
                return '模式值错误';
            }
            if (empty($config['merchant_type'])) {
                return '商户类型不能为空';
            }
            if (empty($config['app_id'])) {
                return '应用ID不能为空';
            }
            if (empty($config['private_key'])) {
                return '应用私钥不能为空';
            }
            //秘钥模式
            if ($config['mode'] == 'secret_key') {
                if (empty($config['ali_public_key'])) {
                    return '支付宝公钥不能为空';
                }
            }
            //证书模式
            if ($config['mode'] == 'certificate') {
                if (empty($config['public_cert'])) {
                    return '应用公钥证书不能为空';
                }
                if (empty($config['ali_public_cert'])) {
                    return '支付宝公钥证书不能为空';
                }
                if (empty($config['ali_root_cert'])) {
                    return '支付宝根证书不能为空';
                }
            }
        }
        return true;
    }


    /**
     * @notes 校验支付名
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author 段誉
     * @date 2023/2/23 16:19
     */
    public function checkName($value, $rule, $data)
    {
        $result = PayConfig::where('name', $value)
            ->where('id', '<>', $data['id'])
            ->findOrEmpty();
        if (!$result->isEmpty()) {
            return '支付名称已存在';
        }
        return true;
    }
}