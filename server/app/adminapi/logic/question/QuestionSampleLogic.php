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
namespace app\adminapi\logic\question;
use app\common\model\question\QuestionSample;

/**
 * 示例分类逻辑类
 * Class QuestionCategoryLogic
 * @package app\adminapi\logic\question
 */
class QuestionSampleLogic
{


    /**
     * @notes 添加示例分类
     * @param array $post
     * @return bool
     * @author cjhao
     * @date 2023/4/14 14:57
     */
    public function add(array $post): bool
    {
        QuestionSample::create($post);
        return true;
    }


    /**
     * @notes 修改示例分类
     * @param array $post
     * @return bool
     * @author cjhao
     * @date 2023/4/14 15:05
     */
    public function edit(array $post): bool
    {
        QuestionSample::update($post);
        return true;
    }


    /**
     * @notes 删除
     * @param $id
     * @return bool|string
     * @author cjhao
     * @date 2023/4/14 15:23
     */
    public function del($id)
    {
        QuestionSample::where(['id'=>$id])->delete();
        return true;
    }

    /**
     * @notes 获取详情
     * @param int $id
     * @return array
     * @author cjhao
     * @date 2023/4/17 10:14
     */
    public function detail(int $id):array
    {
        $detail = QuestionSample::withoutField('update_time,delete_time')->with(['category' => function ($query) {
            $query->bind(['name']);
        }])->where(['id'=>$id])->findOrEmpty()->toArray();
        return $detail;
    }


    /**
     * @notes 修改状态
     * @param int $id
     * @return bool
     * @author cjhao
     * @date 2023/4/14 15:33
     */
    public function status(int $id){
        $questionCategory = QuestionSample::where(['id' => $id])->findOrEmpty();
        if($questionCategory->isEmpty()){
            return true;
        }
        $questionCategory->status = $questionCategory->status ? 0 : 1;
        $questionCategory->save();
        return true;
    }

}