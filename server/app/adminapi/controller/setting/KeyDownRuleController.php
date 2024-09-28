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
use app\adminapi\lists\setting\KeyDownRuleLists;
use app\adminapi\logic\setting\KeyDownRuleLogic;
use app\adminapi\validate\setting\KeyDownRuleValidate;


class KeyDownRuleController extends BaseAdminController
{
    /**
     * @notes 列表
     * @return mixed
     * @author ljj
     * @date 2023/9/15 3:35 下午
     */
    public function lists()
    {
        return $this->dataLists(new KeyDownRuleLists());
    }

    /**
     * @notes 详情
     * @return mixed
     * @author ljj
     * @date 2023/9/15 3:58 下午
     */
    public function detail()
    {
        $params = (new KeyDownRuleValidate())->goCheck('id');
        $detail = (new KeyDownRuleLogic())->detail($params['id']);
        return $this->success('',$detail);
    }

    /**
     * @notes 新增
     * @return mixed
     * @author ljj
     * @date 2023/9/15 4:03 下午
     */
    public function add()
    {
        $params = (new KeyDownRuleValidate())->post()->goCheck('add');
        (new KeyDownRuleLogic())->add($params);
        return $this->success('添加成功');
    }

    /**
     * @notes 编辑
     * @return mixed
     * @author ljj
     * @date 2023/9/15 4:06 下午
     */
    public function edit()
    {
        $params = (new KeyDownRuleValidate())->post()->goCheck();
        (new KeyDownRuleLogic())->edit($params);
        return $this->success('修改成功');
    }

    /**
     * @notes 删除
     * @return mixed
     * @author ljj
     * @date 2023/9/15 4:07 下午
     */
    public function del()
    {
        $params = (new KeyDownRuleValidate())->post()->goCheck('id');
        (new KeyDownRuleLogic())->del($params['id']);
        return $this->success('删除成功');
    }

    /**
     * @notes 修改状态
     * @return mixed
     * @author ljj
     * @date 2023/9/15 4:08 下午
     */
    public function status()
    {
        $params = (new KeyDownRuleValidate())->post()->goCheck('id');
        (new KeyDownRuleLogic())->status($params['id']);
        return $this->success('修改成功');
    }
}