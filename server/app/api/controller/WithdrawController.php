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

namespace app\api\controller;


use app\api\lists\WithdrawLists;
use app\api\logic\WithdrawLogic;
use app\api\validate\WithdrawValidate;

class WithdrawController extends BaseApiController
{
    /**
     * @notes 提现记录列表
     * @return mixed
     * @author ljj
     * @date 2023/5/24 8:12 下午
     */
    public function lists()
    {
        return $this->dataLists(new WithdrawLists());
    }

    /**
     * @notes 提现申请
     * @return \think\response\Json
     * @author ljj
     * @date 2023/5/24 8:38 下午
     */
    public function apply()
    {
        $params = (new WithdrawValidate())->post()->goCheck('apply',['user_id'=>$this->userId]);
        $result = (new WithdrawLogic())->apply($params);
        if(false === $result) {
            return $this->fail(WithdrawLogic::getError());
        }
        return $this->success('申请成功',$result);
    }

    /**
     * @notes 提现详情
     * @return mixed
     * @author ljj
     * @date 2023/5/24 8:57 下午
     */
    public function detail()
    {
        $params = (new WithdrawValidate())->get()->goCheck('detail');
        $result = (new WithdrawLogic())->detail($params);
        return $this->success('',$result);
    }

    /**
     * @notes 提现方式
     * @return mixed
     * @author ljj
     * @date 2023/6/20 7:39 下午
     */
    public function withdrawType()
    {
        $result = (new WithdrawLogic())->withdrawType();
        return $this->success('',$result);
    }
}