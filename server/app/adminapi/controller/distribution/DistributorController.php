<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\adminapi\controller\distribution;


use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\distribution\DistributorBelowLists;
use app\adminapi\lists\distribution\DistributorLists;
use app\adminapi\logic\distribution\DistributorLogic;
use app\adminapi\validate\distribution\DistributorValidate;

class DistributorController extends BaseAdminController
{
    /**
     * @notes 分销商列表
     * @return mixed
     * @author ljj
     * @date 2023/5/23 5:10 下午
     */
    public function lists()
    {
        return $this->dataLists(new DistributorLists());
    }

    /**
     * @notes 开通分销商
     * @return mixed
     * @author ljj
     * @date 2023/5/23 5:35 下午
     */
    public function add()
    {
        $params = (new DistributorValidate())->post()->goCheck('add');
        (new DistributorLogic())->add($params);
        return $this->success('操作成功');
    }

    /**
     * @notes 分销商详情
     * @return mixed
     * @author ljj
     * @date 2023/5/23 6:03 下午
     */
    public function detail()
    {
        $params = (new DistributorValidate())->get()->goCheck('detail');
        $result = (new DistributorLogic())->detail($params);
        return $this->success('',$result);
    }

    /**
     * @notes 修改分销状态
     * @return mixed
     * @author ljj
     * @date 2023/5/23 6:16 下午
     */
    public function status()
    {
        $params = (new DistributorValidate())->post()->goCheck('status');
        (new DistributorLogic())->status($params);
        return $this->success('操作成功');
    }

    /**
     * @notes 下级列表
     * @return mixed
     * @author ljj
     * @date 2023/5/23 6:45 下午
     */
    public function belowLists()
    {
        return $this->dataLists(new DistributorBelowLists());
    }
}