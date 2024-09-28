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
use app\adminapi\logic\setting\KeyPoolLogic;
use app\adminapi\validate\setting\KeyPoolValidate;
use app\common\enum\KeyPoolEnum;
use app\common\model\KeyPool;
use app\common\service\ConfigService;

/**
 * key池控制器类
 * Class KeyPollController
 * @package app\adminapi\controller\setting
 */
class KeyPoolController extends BaseAdminController
{

    /**
     * @notes 获取key池规则
     * @return mixed
     * @author cjhao
     * @date 2023/8/16 16:36
     */
    public function getConfig()
    {
        $config = (new KeyPoolLogic())->getConfig();
        return $this->success('',$config);
    }


    /**
     * @notes 设置key池规则
     * @return mixed
     * @author cjhao
     * @date 2023/8/16 16:36
     */
    public function setConfig()
    {
        $params = $this->request->post();
        (new KeyPoolLogic())->setConfig($params);
        return $this->success('设置成功',[],1,1);
    }

    /**
     * @notes 列表
     * @return mixed
     * @author cjhao
     * @date 2023/7/25 14:44
     */
    public function lists()
    {
        return $this->dataLists();
    }


    /**
     * @notes ai模型列表
     * @return mixed
     * @author cjhao
     * @date 2023/7/25 14:51
     */
    public function getAiModel()
    {
        $type = $this->request->get('type');
        $list = (new KeyPoolLogic())->getAiModel($type);
        return $this->data($list);
    }

    /**
     * @notes 详情
     * @return mixed
     * @author cjhao
     * @date 2023/8/3 12:15
     */
    public function detail()
    {
        $params = (new KeyPoolValidate())->goCheck('id');
        $detail = (new KeyPoolLogic())->detail($params['id']);
        return $this->success('',$detail);
    }

    /**
     * @notes 添加key
     * @return mixed
     * @author cjhao
     * @date 2023/7/25 15:01
     */
    public function add()
    {
        $params = (new KeyPoolValidate())->post()->goCheck('add');
        (new KeyPoolLogic())->add($params);
        return $this->success('添加成功');
    }


    /**
     * @notes 更新key
     * @return mixed
     * @author cjhao
     * @date 2023/7/25 15:03
     */
    public function edit()
    {
        $params = (new KeyPoolValidate())->post()->goCheck('edit');
        (new KeyPoolLogic())->edit($params);
        return $this->success('修改成功');
    }

    /**
     * @notes 删除key
     * @return mixed
     * @author cjhao
     * @date 2023/7/25 15:04
     */
    public function del()
    {
        $params = (new KeyPoolValidate())->post()->goCheck('del');
        (new KeyPoolLogic())->del($params);
        return $this->success('删除成功');
    }

    /**
     * @notes 修改状态
     * @return mixed
     * @author cjhao
     * @date 2023/7/25 15:08
     */
    public function status()
    {
        $params = (new KeyPoolValidate())->post()->goCheck('id');
        (new KeyPoolLogic())->status($params['id']);
        return $this->success('修改成功');
    }

    /**
     * @notes 查询余额
     * @author cjhao
     * @date 2023/4/21 18:36
     */
    public function queryBalance()
    {
        $params = (new KeyPoolValidate())->post()->goCheck('id');
        $result = (new KeyPoolLogic())->queryBalance($params['id']);
        if(false === $result){
            return $this->fail(KeyPoolLogic::getError());
        }
        return $this->success('',$result);
    }

}