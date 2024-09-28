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
use app\common\logic\BaseLogic;
use app\common\model\creation\CreationCategory;
use app\common\model\creation\CreationModel;
use app\common\model\creation\CreationModelCollect;

/**
 * 创作逻辑类
 * Class CreationLogic
 * @package app\api\logic
 */
class CreationLogic extends BaseLogic
{
    /**
     * @notes 获取创作分类
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author caijianhao
     * @date 2023/9/1 18:58
     */
    public  function categoryLists(int $userId)
    {
        $lists = CreationCategory::where(['status' => 1])
            ->field('id,name')
            ->withCount('model')
            ->order('sort desc')
            ->select()
            ->toArray();

        //全部模型数量
        $allModelCount = array_sum(array_column($lists,'model_count'));
        //收藏模型数量
        $collectModelCount = 0;
        if($userId){
            $collectModelCount = CreationModelCollect::where(['user_id'=>$userId])->count();
        }
        $shift = [
            [
                'id'            => 0,
                'name'          => '全部',
                'model_count'   => $allModelCount,
            ],[
                'id'            => -1,
                'name'          => '我的收藏',
                'model_count'   => $collectModelCount,
            ]
        ];

        array_unshift($lists,...$shift);
        return $lists;
    }

    /**
     * @notes 创作列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2023/4/18 10:36
     */
    public function lists(array $params,int $userId)
    {
        //分类列表
        $categoryList = CreationCategory::where(['status'=>1])
            ->field('id,name')
            ->order('sort desc,id desc')
            ->column('id,name','id');
        //查询条件
        $where[] = ['status','=',1];
        if(isset($params['keyword']) && $params['keyword']){
            $where[] =['name','like','%'.$params['keyword'].'%'];
        }
        $creationLists = CreationModel::where($where)
            ->field('id,category_id,name,image,tips,content')
            ->order('sort desc,id desc')
            ->select()->toArray();

        $collectCreationLists = [];
        $collectCreationIds = [];
        //收藏的
        if($userId){
            $where[] = ['user_id','=',$userId];
            $collectCreationLists = CreationModel::alias('CM')
                ->where($where)
                ->join('creation_model_collect CMC','CM.id = CMC.creation_id')
                ->field('CM.id,CM.category_id,CM.name,CM.image,CM.tips,CM.content,1 as is_collect')
                ->order('CMC.id desc')
                ->select()->toArray();
            $collectCreationIds = array_column($collectCreationLists,'id');
        }
        foreach ($creationLists as $key => $creation){
            $creation['is_collect'] = 0;
            //收藏标识
            if(in_array($creation['id'],$collectCreationIds)){
                $creation['is_collect'] = 1;
            }
            if(!isset($categoryList[$creation['category_id']])){
                continue;
            }
            $model = $categoryList[$creation['category_id']]['model'] ??  [];
            $model[] = $creation;

            $categoryList[$creation['category_id']]['model'] = $model;

        }
        array_unshift($categoryList,[
            'id'            => -1,
            'name'          => '我的收藏',
            'model'         => $collectCreationLists,
        ]);
        foreach ($categoryList as $key  => $category){
            if(!isset($category['model'])){
                $categoryList[$key]['model'] = [];
            }
        }
        return array_values($categoryList);
    }


    /**
     * @notes 创作模型详情
     * @param int $id
     * @return array
     * @author ljj
     * @date 2023/4/25 5:07 下午
     */
    public function detail(int $id,int $userId):array
    {
        $detail = CreationModel::withoutField('update_time,delete_time')->where(['id'=>$id])->findOrEmpty()->toArray();
        $detail['is_collect'] = 0;
        if($userId){
            $detail['is_collect'] = CreationModelCollect::where(['creation_id'=>$id,'user_id'=>$userId])->findOrEmpty()->isEmpty() ? 0 : 1;
        }
        return $detail;
    }


    /**
     * @notes 收藏
     * @param int $id
     * @param int $userId
     * @return string|true
     * @author cjhao
     * @date 2023/9/13 12:09
     */
    public function collect(int $id,int $userId)
    {
        if(empty($id)){
            self::$error = '请选择创作';
            return false;
        }
        $collect = (new CreationModelCollect())
            ->where(['user_id'=>$userId,'creation_id'=>$id])
            ->findOrEmpty();

        if($collect->isEmpty()){
            $creation = CreationModel::where(['id' => $id])->findOrEmpty();
            if($creation->isEmpty()){
                self::$error = '创作不存在';
                return false;
            }
            $collect = new CreationModelCollect();
            $collect->creation_id = $id;
            $collect->user_id = $userId;
            $collect->save();
            return '收藏成功';
        }else{
            $collect->delete();
            return '取消成功';
        }
    }
}