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
namespace app\api\lists;
use app\common\enum\ChatRecordEnum;
use app\common\model\ChatRecords;
use app\common\model\creation\CreationModel;
use app\common\model\creation\CreationModelCollect;
use think\facade\Db;

/**
 *
 * 创作模型列表类
 * Class CreationModelLists
 * @package app\api\lists
 */
class CreationModelLists  extends BaseApiDataLists
{

    /**
     * @notes 设置搜索条件
     * @return array
     * @author cjhao
     * @date 2023/11/1 17:59
     */
    public function setSearchWhere()
    {
        $keyword = $this->params['keyword'] ?? '';
        $categoryId = $this->params['category_id'] ?? '';
        $where[] =['status','=',1];
        if($keyword && $keyword){
            $where[] =['name','like','%'.$keyword.'%'];
        }
        if($categoryId){
            //收藏
            if(-1 == $categoryId){
                $creationIds = CreationModelCollect::where(['user_id'=>$this->userId])
                    ->column('creation_id');
                $where[] = ['id','in',$creationIds];
            }else{
                $where[] =['category_id','=',$categoryId];
            }
        }
        return $where;
    }

    /**
     * @notes 模型列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2023/11/1 18:01
     */
    public function lists(): array
    {
        $lists = CreationModel::where($this->setSearchWhere())
            ->field('id,category_id,name,image,tips,virtual_use_num')
            ->order('sort desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();
        //获取收藏的模型
        $collectCreation = [];
        if($this->userId) {
            $collectCreation = CreationModelCollect::where(['user_id'=>$this->userId])->column('creation_id');
        }
        $creationIds = array_column($lists,'id');
        $countLists  = ChatRecords::where(['other_id'=>$creationIds,'type'=> ChatRecordEnum::CHAT_CREATION])
            ->group('other_id')
            ->column('COUNT(DISTINCT user_id) AS user_count','other_id');

        //收藏标识
        foreach ($lists as $item => $value)
        {
            $lists[$item]['is_collect'] =  in_array($value['id'],$collectCreation) ? 1 : 0;
            $lists[$item]['use_num'] = ($countLists[$value['id']] ?? 0) + (empty($value['virtual_use_num']) ? 0 : $value['virtual_use_num']);
        }

        return $lists;
    }

    /**
     * @notes 获取数量
     * @return int
     * @author cjhao
     * @date 2023/11/1 18:02
     */
    public function count(): int
    {
        return CreationModel::where($this->setSearchWhere())->count();
    }
}