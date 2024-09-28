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
use app\common\model\ChatCategory;
use app\common\model\ChatRecordsCollect;

class ChatCategoryLists extends BaseApiDataLists
{

    /**
     * @notes 实现数据列表
     * @return array
     * @author 令狐冲
     * @date 2021/7/6 00:33
     */
    public function lists(): array
    {
        $this->initCategory();
        $lists = ChatCategory::where($this->setSearchWehre())
            ->field('id,name')
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id desc')
            ->select();

        return $lists->toArray();


    }

    /**
     * @notes 实现数据列表记录数
     * @return int
     * @author 令狐冲
     * @date 2021/7/6 00:34
     */
    public function count(): int
    {
        return  ChatCategory::where($this->setSearchWehre())->count();
    }


    /**
     * @notes 设置搜索条件
     * @return array
     * @author cjhao
     * @date 2023/5/30 16:23
     */
    public function setSearchWehre()
    {
        $where = [];
        $where[] = ['user_id','=',$this->userId];
        return $where;

    }

    /**
     * @notes 初始化一个会话
     * @author cjhao
     * @date 2023/6/12 16:27
     */
    public function initCategory()
    {
        $count = ChatCategory::where($this->setSearchWehre())->count();
        if(0 == $count){
            //创建一个默认会话
            $chatCategory = new ChatCategory();
            $chatCategory->save(['name' => '新的会话', 'user_id' => $this->userId]);
//            ChatRecordsCollect::where(['user_id'=>$this->userId,'type'=>ChatRecordEnum::CHAT_QUESTION])->update(['category_id'=>$chatCategory->id]);
        }
    }
}