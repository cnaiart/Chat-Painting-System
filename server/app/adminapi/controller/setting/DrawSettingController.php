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
use app\adminapi\logic\setting\DrawSettingLogic;

/**
 * 绘画配置控制器类
 * Class DrawSettingController
 * @package app\adminapi\lists\setting
 */
class DrawSettingController extends BaseAdminController
{

    /**
     * @notes 绘画配置
     * @return mixed
     * @author 段誉
     * @date 2023/6/21 9:53
     */
    public function getDrawConfig()
    {
        $config = (new DrawSettingLogic())->getDrawConfig();
        return $this->success('',$config);
    }

    /**
     * @notes 设置绘画配置
     * @return mixed
     * @author 段誉
     * @date 2023/6/21 9:53
     */
    public function setDrawConfig()
    {
        $params = $this->request->post();
        $result = (new DrawSettingLogic())->setDrawConfig($params);
        if ($result !== true) {
            return $this->fail($result);
        }
        return $this->success('设置成功', [], 1, 1);
    }
    /**
     * @notes 获取对话模型配置
     * @return mixed
     * @author cjhao
     * @date 2023/7/18 18:12
     */
    public function getDrawBillingConfig()
    {
        $config = (new DrawSettingLogic())->getDrawBillingConfig();
        return $this->success('',$config);
    }


    /**
     * @notes 设置对话模型配置
     * @return mixed
     * @author cjhao
     * @date 2023/7/18 18:28
     */
    public function setDrawBillingConfig()
    {
        $params = $this->request->post();
        $result = (new DrawSettingLogic())->setDrawBillingConfig($params);
        if ($result !== true) {
            return $this->fail($result);
        }
        return $this->success('设置成功', [], 1, 1);
    }


    /**
     * @notes 艺术二维码配置
     * @return mixed
     * @author mjf
     * @date 2023/10/16 15:43
     */
    public function getQrcodeConfig()
    {
        $config = (new DrawSettingLogic())->getQrcodeConfig();
        return $this->success('', $config);
    }

    /**
     * @notes 艺术二维码配置
     * @return mixed
     * @author mjf
     * @date 2023/10/16 15:43
     */
    public function setQrcodeConfig()
    {
        $params = $this->request->post();
        $result = (new DrawSettingLogic())->setQrcodeConfig($params);
        if ($result !== true) {
            return $this->fail($result);
        }
        return $this->success('设置成功', [], 1, 1);
    }

    /**
     * @notes 艺术二维码-模型计费
     * @return mixed
     * @author mjf
     * @date 2023/10/16 17:03
     */
    public function getQrcodeBillingConfig()
    {
        $config = (new DrawSettingLogic())->getQrcodeBillingConfig();
        return $this->success('',$config);
    }

    /**
     * @notes 艺术二维码-模型计费
     * @return mixed
     * @author mjf
     * @date 2023/10/16 17:04
     */
    public function setQrcodeBillingConfig()
    {
        $params = $this->request->post();
        $result = (new DrawSettingLogic())->setQrcodeBillingConfig($params);
        if ($result !== true) {
            return $this->fail($result);
        }
        return $this->success('设置成功', [], 1, 1);
    }

    /**
     * @notes 获取绘画设置
     * @return mixed
     * @author ljj
     * @date 2023/12/26 2:46 下午
     */
    public function getDrawSetting()
    {
        $config = (new DrawSettingLogic())->getDrawSetting();
        return $this->success('',$config);
    }

    /**
     * @notes 设置绘画设置
     * @return mixed
     * @author ljj
     * @date 2023/12/26 2:46 下午
     */
    public function setDrawSetting()
    {
        $params = $this->request->post();
        $result = (new DrawSettingLogic())->setDrawSetting($params);
        if ($result !== true) {
            return $this->fail($result);
        }
        return $this->success('设置成功', [], 1, 1);
    }
}