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

use app\api\logic\DrawPromptLogic;

/**
 * 绘画关键词
 * Class DrawPromptController
 * @package app\api\controller
 */
class DrawPromptController extends BaseApiController
{

    public array $notNeedLogin = ['category', 'prompt', 'example'];

    /**
     * @notes 分类
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/28 11:07
     */
    public function category()
    {
        $result = DrawPromptLogic::category();
        return $this->data($result);
    }

    /**
     * @notes 关键词
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/28 11:17
     */
    public function prompt()
    {
        $params = $this->request->get('id/d', 0);
        $result = DrawPromptLogic::prompt($params);
        return $this->data($result);
    }

    /**
     * @notes 示例列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/7/17 16:29\
     */
    public function example()
    {
        $result = DrawPromptLogic::example();
        return $this->data($result);
    }

    /**
     * @notes 手动翻译
     * @return mixed
     * @author 段誉
     * @date 2023/7/18 9:50
     */
    public function translate()
    {
        $prompt = $this->request->get('prompt/s', '');
        $result = DrawPromptLogic::translate($prompt);
        if (false === $result) {
            return $this->fail(DrawPromptLogic::getError());
        }
        return $this->success('', $result);
    }

}