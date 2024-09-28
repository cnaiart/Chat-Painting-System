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
namespace app\common\enum;
use app\common\model\creation\CreationModel;
use app\common\model\question\QuestionSample;
use app\common\model\skill\Skill;

/**
 * 对话记录枚举类
 * Class ChatRecordEnum
 * @package app\common\enum
 */
class ChatRecordEnum
{

    const CHAT_QUESTION         = 1;            //问答
    const CHAT_CREATION         = 2;            //创作
    const CHAT_SKILL            = 3;            //技能
    const CHAT_MIND             = 4;            //思维导图


    const REPLYTYPE_MODEL      = 1;        //模型对话
    const REPLYTYPE_SYSTEM     = 2;        //系统

    //审核状态
    const CENSOR_STATUS_WAIT = 0;//未审核
    const CENSOR_STATUS_COMPLIANCE = 1;//合规
    const CENSOR_STATUS_NON_COMPLIANCE = 2;//不合规
    const CENSOR_STATUS_SUSPECTED = 3;//疑似
    const CENSOR_STATUS_FAIL = 4;//审核失败


    /**
     * @notes 获取语音播报的保存路径
     * @param $type
     * @return string
     * @author cjhao
     * @date 2023/10/10 11:13
     */
    public static function getChatVoiceDir($type)
    {
        $desc =  [
            self::CHAT_QUESTION     => 'question',
            self::CHAT_CREATION     => 'creation',
            self::CHAT_SKILL        => 'skill',
        ];
        return $desc[$type] ?? '';
    }
    /**
     * @notes 获取对应的模型
     * @return string
     * @author cjhao
     * @date 2023/4/25 17:24
     */
    public static function getModel($type)
    {

        $desc =  [
            self::CHAT_QUESTION     => QuestionSample::class,
            self::CHAT_CREATION     => CreationModel::class,
            self::CHAT_SKILL        => Skill::class,
        ];
        return $desc[$type] ?? '';
    }


    /**
     * @notes 审核状态
     * @param $type
     * @return string
     * @author ljj
     * @date 2023/6/19 8:06 下午
     */
    public static function getCensorStatusDesc($type)
    {

        $desc =  [
            self::CENSOR_STATUS_WAIT => '未审核',
            self::CENSOR_STATUS_COMPLIANCE => '合规',
            self::CENSOR_STATUS_NON_COMPLIANCE => '不合规',
            self::CENSOR_STATUS_SUSPECTED => '疑似',
            self::CENSOR_STATUS_FAIL => '审核失败',
        ];
        return $desc[$type] ?? '';
    }



}