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
use app\api\lists\DistributionFansLists;
use app\api\lists\DistributionOrderLists;
use app\api\logic\DistributionLogic;
use app\api\validate\DistributionApplyValidate;

/**
 * 分销控制器类
 * Class DistributionController
 * @package app\api\controller
 */
class DistributionController extends BaseApiController
{


    /**
     * @notes 分销中心
     * @return mixed
     * @author cjhao
     * @date 2023/5/19 18:53
     */
    public function index()
    {
        $result = (new DistributionLogic())->index($this->userId);
        return $this->success('',$result);
    }


    /**
     * @notes 分销申请
     * @return \think\response\Json
     * @author cjhao
     * @date 2023/5/19 18:52
     */
    public function distributionApply()
    {
        $post = (new DistributionApplyValidate())->post()->goCheck();
        $result = (new DistributionLogic())->distributionApply($post,$this->userId);
        if(true === $result){
            return $this->success('申请成功',[],1,1);
        }
        return $this->fail($result);
    }


    /**
     * @notes 分销订单列表
     * @return mixed
     * @author ljj
     * @date 2023/5/24 7:40 下午
     */
    public function distributionOrder()
    {
        return $this->dataLists(new DistributionOrderLists());
    }

    /**
     * @notes 粉丝列表
     * @return mixed
     * @author ljj
     * @date 2023/5/25 9:59 上午
     */
    public function distributionFans()
    {
        return $this->dataLists(new DistributionFansLists());
    }
}
