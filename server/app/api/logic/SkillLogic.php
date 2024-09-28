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
use app\common\model\skill\Skill;
use app\common\model\skill\SkillCategory;

/**
 * 技能逻辑类
 * Class SkillController
 * @package app\api\logic
 */
class SkillLogic
{


    /**
     * @notes 列表
     * @author cjhao
     * @date 2023/4/18 10:03
     */
    public function lists(array $params)
    {
        $lists = SkillCategory::where(['status' => 1])
            ->with(['skill' => function ($query) use ($params) {
                $where[] =['status','=',1];
                if(isset($params['keyword']) && $params['keyword']){
                    $where[] =['name','like','%'.$params['keyword'].'%'];
                }
                $query->where($where)->field('id,category_id,name,describe,image,content')->order('sort desc');
            }])
            ->field('id,name')
            ->order('sort desc')
            ->select()->toArray();

        return $lists;

    }

    /**
     * @notes 技能详情
     * @param int $id
     * @return array
     * @author ljj
     * @date 2023/4/25 5:14 下午
     */
    public function detail(int $id):array
    {
        $detail = Skill::withoutField('update_time,delete_time')->where(['id'=>$id])->findOrEmpty()->toArray();
        return $detail;
    }
}