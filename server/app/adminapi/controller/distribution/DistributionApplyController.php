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
namespace app\adminapi\controller\distribution;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\validate\distribution\DistributionApplyValidate;
use app\adminapi\logic\distribution\DistributionApplyLogic;

/**
 * 分销申请控制器类
 * Class DistributionApplyController
 * @package app\adminapi\controller\distribution
 */
class DistributionApplyController extends BaseAdminController
{

    /**
     * @notes 申请列表
     * @return mixed
     * @author cjhao
     * @date 2023/5/23 12:05
     */
    public function lists()
    {
        return $this->dataLists();
    }

    /**
     * @notes 详情
     * @return mixed
     * @author cjhao
     * @date 2023/5/23 15:03
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $detail = (new DistributionApplyLogic())->detail($id);
        return $this->success('',$detail);
    }

    /**
     * @notes 申请审核
     * @return mixed
     * @author cjhao
     * @date 2023/5/23 14:59
     */
    public function audit()
    {
        $post = (new DistributionApplyValidate())->post()->goCheck();
        $result = (new DistributionApplyLogic())->audit($post);
        if(true === $result){
            return $this->success('审核成功');
        }
        return $this->fail($result);
    }

}