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
namespace app\api\logic;
use app\common\model\question\QuestionCategory;
use app\common\model\question\QuestionSample;
use think\db\Query;

/**
 * 问题逻辑类
 * Class QuestionLogic
 * @package app\api\logic
 */
class QuestionLogic
{

    /**
     * @notes 问题示例列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2023/4/18 17:56
     */
    public function samplesLists()
    {
        $lists = QuestionCategory::with(['sample'	=> function(Query $query) {
            $query->field('id,content,category_id')
                ->where(['status'=>1])
                ->order('sort desc,id desc')
                ->select();
        }])
            ->order('sort desc,id desc')
            ->where(['status'=>1])
            ->field('id,name,image')
            ->select()
            ->toArray();
        return $lists;


    }

}