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
namespace app\common\enum\chat;
/**
 * 腾讯混元枚举类
 */
class HunYuanEnum
{
    const HUNYUAN_STD                  = 'ChatStd';//标准版
    const HUNYUAN_PRO                  = 'ChatPro';//高级版

    //默认配置
    const DAFAULT_CONFIG = [
        'hunyuan_model'         => self::HUNYUAN_STD,
        'top_p'                 => 1.0,//影响输出文本的多样性，取值越大，生成文本的多样性越强 取值范围：[0.0, 1.0]
        'temperature'           => 1.0,//较高的数值会使输出更加随机，而较低的数值会使其更加集中和确定 取值范围：[0.0，2.0]
        'context_num'           => 3,//上下文总数
    ];


    /**
     * @notes 获取模型列表
     * @param bool $from
     * @return array|string|string[]
     * @author ljj
     * @date 2023/12/21 4:45 下午
     */
    public static function getModelLists($from = true)
    {
        $desc = [
            self::HUNYUAN_STD,
            self::HUNYUAN_PRO,
        ];
        if(true === $from){
            return $desc;
        }
        return $desc[$from] ?? [];
    }

}