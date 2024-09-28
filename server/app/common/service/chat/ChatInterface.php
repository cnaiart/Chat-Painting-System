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
namespace app\common\service\chat;
interface ChatInterface
{

    /**
     * @notes 发起对话
     * @param array $messages
     * @return mixed
     * @author cjhao
     * @date 2023/10/23 17:59
     */
    public function chatStreamRequest(array $messages):self;

    /**
     * @notes 获取回复内容
     * @return mixed
     * @author cjhao
     * @date 2023/10/23 17:59
     */
    public function getReplyContent():string|array;

    /**
     * @notes 设置上下文
     * @return mixed
     * @author cjhao
     * @date 2023/10/23 17:59
     */
    public function setContextNum($contextNum):self;

    /**
     * @notes 获取上下文
     * @return mixed
     * @author cjhao
     * @date 2023/10/23 18:13
     */
    public function getContextNum():int;

    /**
     * @notes 设置温度
     * @param $temperature
     * @return mixed
     * @author cjhao
     * @date 2023/10/23 18:11
     */
    public function setTemperature($temperature):self;

    /**
     * @notes 获取对话模型
     * @return string
     * @author cjhao
     * @date 2023/10/23 18:51
     */
    public function getModel():string;

}