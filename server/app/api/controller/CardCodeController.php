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
use app\api\logic\CardCodeLogic;

/**
 * 卡密控制器类
 * Class CardCodeController
 * @package app\api\controller
 */
class CardCodeController extends BaseApiController
{

    /**
     * @notes 验证卡密
     * @return mixed
     * @author cjhao
     * @date 2023/7/11 16:30
     */
    public function checkCard()
    {
        $sn = $this->request->get('sn','');
        $result = (new CardCodeLogic())->checkCard($sn,$this->userId);
        if(is_array($result)) {
            return $this->success('',$result);
        }
        return $this->fail($result);
    }


    /**
     * @notes 使用卡密
     * @return mixed
     * @author cjhao
     * @date 2023/7/11 16:50
     */
    public function useCard()
    {
        $sn = $this->request->post('sn','');
        $result = (new CardCodeLogic())->useCard($sn,$this->userId);
        if(true === $result){
            return $this->success('兑换成功');
        }
        return $this->fail($result);

    }

}