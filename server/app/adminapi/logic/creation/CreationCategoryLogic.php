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
namespace app\adminapi\logic\creation;
use app\common\model\creation\CreationCategory;
use app\common\model\creation\CreationModel;

/**
 * 创作类别逻辑类
 * Class CreationModelLogic
 * @package app\adminapi\logic\reation
 */
class CreationCategoryLogic
{

    /**
     * @notes 添加分类
     * @param array $post
     * @return bool
     * @author cjhao
     * @date 2023/4/14 14:57
     */
    public function add(array $post): bool
    {
        CreationCategory::create($post);
        return true;
    }


    /**
     * @notes 修改分类
     * @param array $post
     * @return bool
     * @author cjhao
     * @date 2023/4/14 15:05
     */
    public function edit(array $post): bool
    {
        CreationCategory::update($post);
        return true;
    }

    /**
     * @notes 获取详情
     * @param int $id
     * @return bool
     * @author cjhao
     * @date 2023/4/17 10:05
     */
    public function detail(int $id):array
    {
        $detail = CreationCategory::withoutField('delete_time,update_time')->findOrEmpty($id)->toArray();
        return $detail;
    }

    /**
     * @notes 删除
     * @param int $id
     * @return bool|string
     * @author cjhao
     * @date 2023/4/14 15:23
     */
    public function del(int $id)
    {
        $creationSample = CreationModel::where(['category_id' => $id])->findOrEmpty();
        if(!$creationSample->isEmpty()){
            return '该分类已经被使用，不能删除';
        }
        CreationCategory::where(['id'=>$id])->delete();
        return true;
    }


    /**
     * @notes 修改状态
     * @param int $id
     * @return bool
     * @author cjhao
     * @date 2023/4/14 15:33
     */
    public function status(int $id){
        $creationCategory = CreationCategory::where(['id' => $id])->findOrEmpty();
        if($creationCategory->isEmpty()){
            return true;
        }
        $creationCategory->status = $creationCategory->status ? 0 : 1;
        $creationCategory->save();
        return true;
    }



}