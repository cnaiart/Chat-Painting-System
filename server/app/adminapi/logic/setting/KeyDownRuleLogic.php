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
namespace app\adminapi\logic\setting;
use app\common\logic\BaseLogic;
use app\common\model\KeyDownRule;


class KeyDownRuleLogic extends BaseLogic
{
    /**
     * @notes 详情
     * @param int $id
     * @return array
     * @author ljj
     * @date 2023/9/15 3:58 下午
     */
    public function detail(int $id)
    {
        return KeyDownRule::findOrEmpty($id)->toArray();

    }

    /**
     * @notes 新增
     * @param array $params
     * @author ljj
     * @date 2023/9/15 4:04 下午
     */
    public function add(array $params)
    {
        KeyDownRule::create([
            'type' => $params['type'],
            'ai_key' => $params['ai_key'],
            'rule' => $params['rule'],
            'prompt' => $params['prompt'],
            'status' => $params['status'],
        ]);

        return true;
    }

    /**
     * @notes 编辑
     * @param array $params
     * @return bool
     * @author ljj
     * @date 2023/9/15 4:05 下午
     */
    public function edit(array $params)
    {
        KeyDownRule::update([
            'type' => $params['type'],
            'ai_key' => $params['ai_key'],
            'rule' => $params['rule'],
            'prompt' => $params['prompt'],
            'status' => $params['status'],
        ],['id'=>$params['id']]);

        return true;
    }

    /**
     * @notes 删除
     * @param int $id
     * @return bool
     * @author ljj
     * @date 2023/9/15 4:07 下午
     */
    public function del(int $id)
    {
        KeyDownRule::destroy($id);

        return true;
    }

    /**
     * @notes 修改状态
     * @param int $id
     * @return bool
     * @author ljj
     * @date 2023/9/15 4:08 下午
     */
    public function status(int $id)
    {
        $result = KeyDownRule::findOrEmpty($id);
        $result->status = $result->status ? 0 : 1;
        $result->save();
        return true;
    }
}