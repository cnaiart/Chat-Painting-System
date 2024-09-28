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
use app\adminapi\lists\distribution\WithdrawLists;
use app\adminapi\logic\distribution\WithdrawLogic;
use app\adminapi\validate\distribution\WithdrawValidate;

class WithdrawController extends BaseAdminController
{
    /**
     * @notes 提现记录列表
     * @return mixed
     * @author ljj
     * @date 2023/5/24 2:45 下午
     */
    public function lists()
    {
        return $this->dataLists(new WithdrawLists());
    }

    /**
     * @notes 提现审核
     * @return mixed
     * @author ljj
     * @date 2023/5/24 3:18 下午
     */
    public function verify()
    {
        $params = (new WithdrawValidate())->post()->goCheck('verify');
        $result = (new WithdrawLogic())->verify($params);
        if(true !== $result) {
            return $this->fail($result);
        }
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 转账
     * @return mixed
     * @author ljj
     * @date 2023/5/24 3:36 下午
     */
    public function transfer()
    {
        $params = (new WithdrawValidate())->post()->goCheck('transfer');
        $result = (new WithdrawLogic())->transfer($params);
        if(true !== $result) {
            return $this->fail($result);
        }
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 转账查询
     * @return mixed
     * @author ljj
     * @date 2023/6/20 5:14 下午
     */
    public function search()
    {
        $params = (new WithdrawValidate())->goCheck('search');
        $result = (new WithdrawLogic())->search($params);
        if($result === false) {
            return $this->fail(WithdrawLogic::getError());
        }
        return $this->success($result,[],1,1);
    }


    /**
     * @notes 提现详情
     * @return mixed
     * @author ljj
     * @date 2023/5/24 3:52 下午
     */
    public function detail()
    {
        $params = (new WithdrawValidate())->get()->goCheck('detail');
        $result = (new WithdrawLogic())->detail($params);
        return $this->success('',$result);
    }

    /**
     * @notes 获取提现配置
     * @return mixed
     * @author ljj
     * @date 2023/5/24 4:16 下午
     */
    public function getConfig()
    {
        $config = (new WithdrawLogic())->getConfig();
        return $this->success('',$config);
    }

    /**
     * @notes 设置提现配置
     * @return mixed
     * @author ljj
     * @date 2023/5/24 4:31 下午
     */
    public function setConfig()
    {
        $params = (new WithdrawValidate())->post()->goCheck('setConfig');
        (new WithdrawLogic())->setConfig($params);
        return $this->success('设置成功',[],1,1);
    }
}