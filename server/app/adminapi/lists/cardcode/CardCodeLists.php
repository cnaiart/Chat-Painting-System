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
namespace app\adminapi\lists\cardcode;
use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\CardCodeEnum;
use app\common\enum\CardCodeRecordEnum;
use app\common\enum\MemberPackageEnum;
use app\common\lists\BaseDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\model\cardcode\CardCode;
use app\common\model\cardcode\CardCodeRecord;
use app\common\model\member\MemberPackage;
use app\common\model\recharge\RechargePackage;

/**
 * 卡密列表类
 * Class CardCodeLists
 * @package app\lists\cardcode
 */
class CardCodeLists extends BaseAdminDataLists implements ListsExcelInterface
{


    /**
     * @notes 实现数据列表
     * @return array
     * @author 令狐冲
     * @date 2021/7/6 00:33
     */
    public function lists(): array
    {
        $lists = (new CardCode())
            ->field('id,sn,type,draw_num,chat_num,card_num,relation_id,valid_start_time,valid_end_time,create_time,remark')
            ->limit($this->limitOffset, $this->limitLength)
            ->where($this->setSearch())
            ->order('id desc')
            ->select()
            ->toArray();

        $id = array_column($lists,'id');
        $relationId = array_column($lists,'relation_id');
        $recordList= CardCodeRecord::where(['card_id'=>$id])
            ->where(['status'=>CardCodeRecordEnum::STATYS_YES])
            ->group('card_id')
            ->field('count(id) as num,card_id')
            ->select()->toarray();
        $recordList = array_column($recordList,'num','card_id');
        $memberPackge = MemberPackage::where(['id'=>$relationId])->column('name,duration,duration_type,is_perpetual','id');
        $rechargePackge = RechargePackage::where(['id'=>$relationId])->column('name','id');

        foreach ($lists as $key => $list){
            $content = '';
            switch ($list['type']){
                case CardCodeEnum::TYPE_MEMBER:
                    $content = $memberPackge[$list['relation_id']]['name'] ?? '';
                    $duration = $memberPackge[$list['relation_id']]['duration'] ?? '';
                    $isPerpetual = $memberPackge[$list['relation_id']]['is_perpetual'] ?? '';
                    $durationType = $memberPackge[$list['relation_id']]['duration_type'] ?? 0;
                    if($isPerpetual){
                        $content.= '(永久)';
                    }else{
                        if ($durationType == MemberPackageEnum::DURATION_TYPE_DAY) {
                            $content.= '('.$duration.'天)';
                        } else {
                            $content.= '('.$duration.'个月)';
                        }
                    }
                    break;
                case CardCodeEnum::TYPE_RECHARGE:
                    $content = $rechargePackge[$list['relation_id']] ?? '';
                    break;
                case CardCodeEnum::TYPE_CHAT_NUM:
                    $content = $list['chat_num'].'条';
                    break;
                case CardCodeEnum::TYPE_DRAW_NUM:
                    $content = $list['draw_num'].'条';
                    break;
            }
            $lists[$key]['content'] = $content;
            $lists[$key]['type_desc'] = CardCodeEnum::getTypeDesc($list['type']);
            $useDesc = $recordList[$list['id']] ?? 0;
            $lists[$key]['num_use_desc'] = $useDesc.'/'.$list['card_num'];
            $lists[$key]['valid_start_time_desc'] = date('Y-m-d H:i:s',$list['valid_start_time']);
            $lists[$key]['valid_end_time_desc'] = date('Y-m-d H:i:s',$list['valid_end_time']);

        }
        return $lists;
    }

    /**
     * @notes 实现数据列表记录数
     * @return int
     * @author 令狐冲
     * @date 2021/7/6 00:34
     */
    public function count(): int
    {
        return (new CardCode())
            ->where($this->setSearch())
            ->count();
    }


    /**
     * @notes 设置搜索条件
     * @return array
     * @author cjhao
     * @date 2023/7/17 14:57
     */
    public function setSearch()
    {
        $where = [];
        if(isset($this->params['sn']) && $this->params['sn']){
            $where[] = ['sn','like','%'.$this->params['sn'].'%'];
        }
        if(isset($this->params['type']) && $this->params['type']){
            $where[] = ['type','=',$this->params['type']];
        }
        if(isset($this->params['start_time']) && $this->params['start_time']){
            $where[] = ['valid_start_time','<=',strtotime($this->params['start_time'])];
        }
        if(isset($this->params['end_time']) && $this->params['end_time']){
            $where[] = ['valid_end_time','>=',strtotime($this->params['end_time'])];
        }
        return $where;

    }

    /**
     * @notes 导出文件名
     * @return string
     * @author ljj
     * @date 2023/8/24 2:49 下午
     */
    public function setFileName(): string
    {
        return '卡密列表';
    }

    /**
     * @notes 导出字段
     * @return string[]
     * @author ljj
     * @date 2023/8/24 2:49 下午
     */
    public function setExcelFields(): array
    {
        return [
            'sn' => '批次编号',
            'type_desc' => '卡密类型',
            'content' => '卡密内容',
            'num_use_desc' => '已使用/数量',
            'valid_start_time_desc' => '生效时间',
            'valid_end_time_desc' => '失效时间',
            'create_time' => '创建时间',
            'remark' => '备注',
        ];
    }
}