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
namespace app\adminapi\controller\setting;
use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\setting\ChatSettingLogic;
use app\adminapi\validate\setting\ChatSettingValidate;

/**
 * 对话配置控制器类
 * Class ChatSettingController
 * @package app\adminapi\lists\setting
 */
class ChatSettingController extends BaseAdminController
{

    /**
     * @notes 获取对话配置
     * @return mixed
     * @author cjhao
     * @date 2023/12/20 16:55
     */
    public function getChatConfig()
    {
        $lists = (new ChatSettingLogic())->getChatConfig();
        return $this->success('',$lists);
    }

    /**
     * @notes 设置对话配置
     * @author cjhao
     * @date 2023/4/21 12:16
     */
    public function setChatConfig()
    {
        $params = (new ChatSettingValidate())->post()->goCheck();
        (new ChatSettingLogic())->setChatConfig($params);
        return $this->success('设置成功',[],1,1);
    }

    /**
     * @notes 获取模型计费配置
     * @return mixed
     * @author cjhao
     * @date 2023/7/18 18:12
     */
    public function getChatBillingConfig()
    {
        $config = (new ChatSettingLogic())->getChatBillingConfig();
        return $this->success('',$config);
    }


    /**
     * @notes 设置模型计费配置
     * @return mixed
     * @author cjhao
     * @date 2023/7/18 18:28
     *
     */
    public function setChatBillingConfig()
    {
        $params = $this->request->post();
        $result = (new ChatSettingLogic())->setChtBillingConfig($params);
        if ($result !== true) {
            return $this->fail($result);
        }
        return $this->success('设置成功', [], 1, 1);
    }


}