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
namespace app\api\controller;
use app\api\logic\ChatLogic;
use app\api\service\ChatService;
use app\common\enum\voice\VoiceEnum;
use think\facade\Log;


/**
 * 对话控制器类
 * Class ChatController
 * @package app\api\controller
 */
class ChatController extends BaseApiController
{

    /**
     * @notes 获取对话模型
     * @author cjhao
     * @date 2023/7/19 10:22
     */
    public function getModel()
    {
        $lists = (new ChatLogic())->getModel($this->userId);
        return $this->success('',$lists);
    }

    /**
     * @notes 聊天接口
     * @return \think\response\Json
     * @author ljj
     * @date 2023/4/25 11:34 上午
     */
    public function chat()
    {
        $params = $this->request->post();
        (new ChatService($this->userId,$params))->chat();
    }


    /**
     * @notes 语音合成
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/2/1 9:58
     */
    public function voiceGenerate()
    {
        $params = $this->request->post();
        $result = (new ChatLogic())->voiceGenerate($this->userId,$params);
        if(false === $result) {
            return $this->fail(ChatLogic::getError());
        }
        return $this->success('',$result);
    }


    /**
     * @notes 语音识别
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/1/26 15:53
     */
    public function voiceTransfer()
    {
        $type = $this->request->post('type',VoiceEnum::VOICE_INPUT);
        $file = $this->request->file('file');
        $result = (new ChatLogic())->voiceTransfer($this->userId,$type,$file);
        if(false === $result){
            return $this->fail(ChatLogic::getError());
        }
        return $this->success('',$result);
    }

}