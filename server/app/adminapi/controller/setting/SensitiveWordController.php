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
use app\adminapi\logic\setting\SensitiveWordLogic;
use app\adminapi\validate\setting\SensitiveWordValidate;

/**
 * 敏感词库控制器类
 * Class SensitiveWordController
 * @package app\adminapi\controller\setting
 */
class SensitiveWordController extends BaseAdminController
{
    /**
     * @notes 敏感词列表
     * @return mixed
     * @author cjhao
     * @date 2023/5/26 14:50
     */
    public function lists()
    {
        return $this->dataLists();
    }


    /**
     * @notes 添加
     * @return mixed
     * @author cjhao
     * @date 2023/5/26 15:13
     */
    public function add()
    {
        $post = (new SensitiveWordValidate())->post()->goCheck('add');
        (new SensitiveWordLogic())->add($post);
        return $this->success('新增成功',[],1,1);
    }

    /**
     * @notes 编辑
     * @return mixed
     * @author cjhao
     * @date 2023/5/26 15:13
     */
    public function edit()
    {
        $post = (new SensitiveWordValidate())->post()->goCheck('edit');
        (new SensitiveWordLogic())->edit($post);
        return $this->success('编辑成功',[],1,1);
    }

    /**
     * @notes 删除
     * @return mixed
     * @author cjhao
     * @date 2023/5/26 15:13
     */
    public function del()
    {
        $post = (new SensitiveWordValidate())->post()->goCheck('id');
        (new SensitiveWordLogic())->del($post['id']);
        return $this->success('删除成功',[],1,1);
    }

    /**
     * @notes 状态
     * @return mixed
     * @author cjhao
     * @date 2023/5/26 15:13
     */
    public function status()
    {
        $post = (new SensitiveWordValidate())->post()->goCheck('id');
        (new SensitiveWordLogic())->status($post['id']);
        return $this->success('修改成功',[],1,1);
    }


    /**
     * @notes 获取配置接口
     * @return mixed
     * @author ljj
     * @date 2023/6/19 5:15 下午
     */
    public function getConfig()
    {
        $config = (new SensitiveWordLogic())->getConfig();
        return $this->success('',$config);
    }

    /**
     * @notes 设置配置接口
     * @return mixed
     * @author ljj
     * @date 2023/6/19 5:20 下午
     */
    public function setConfig()
    {
        $params = (new SensitiveWordValidate())->post()->goCheck('setConfig');
        (new SensitiveWordLogic())->setConfig($params);
        return $this->success('设置成功',[],1,1);
    }
}