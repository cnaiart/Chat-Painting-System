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
namespace app\adminapi\logic\distribution;
use app\common\service\ConfigService;

/**
 * 配置逻辑类
 * Class ConfigLogic
 * @package app\adminapi\logic\distribution
 */
class ConfigLogic
{


    /**
     * @notes 获取分销配置
     * @return array
     * @author cjhao
     * @date 2023/5/19 10:29
     */
    public function getConfig():array
    {
        return [
            'is_open'           => ConfigService::get('distribution','is_open'),
            'condition'         => ConfigService::get('distribution','condition',1),
            'auto_audit'        => ConfigService::get('distribution','auto_audit',0),
            'level'             => ConfigService::get('distribution','level',1),
            'first_ratio'       => ConfigService::get('distribution','first_ratio',0),
            'second_ratio'      => ConfigService::get('distribution','second_ratio',0),
        ];

    }


    /**
     * @notes 设置分销配置
     * @param array $post
     * @return array
     * @author cjhao
     * @date 2023/5/19 11:16
     */
    public function setConfig(array $post):bool
    {
        ConfigService::set('distribution','is_open',$post['is_open']);
        ConfigService::set('distribution','condition',$post['condition']);
        ConfigService::set('distribution','auto_audit',$post['auto_audit'] ?? 0);
        ConfigService::set('distribution','level',$post['level']);
        ConfigService::set('distribution','first_ratio',round($post['first_ratio'],2));
        ConfigService::set('distribution','second_ratio',round($post['second_ratio'],2));
        return true;
    }

}
