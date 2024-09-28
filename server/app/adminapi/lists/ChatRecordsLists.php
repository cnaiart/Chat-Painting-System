<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\adminapi\lists;


use app\common\enum\chat\ChatEnum;
use app\common\enum\ChatRecordEnum;
use app\common\lists\ListsExcelInterface;
use app\common\model\ChatRecords;
use app\common\model\creation\CreationModel;
use app\common\model\skill\Skill;
use think\facade\Db;

class ChatRecordsLists extends BaseAdminDataLists implements ListsExcelInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/4/25 10:06 上午
     */
    public function where()
    {
        $where = [];
        $type = $this->params['type'] ?? 1;
        $where[] = ['cr.type','=',$type];
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['u.sn|u.nickname','like','%'.$this->params['user_info'].'%'];
        }
        if (isset($this->params['keyword']) && $this->params['keyword'] != '') {
            $where[] = ['cr.ask','like','%'.$this->params['keyword'].'%'];
        }
        if (isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $where[] = ['cr.create_time','>=',strtotime($this->params['start_time'])];
        }
        if (isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['cr.create_time','<=',strtotime($this->params['end_time'])];
        }
        if (isset($this->params['censor_status']) && $this->params['censor_status'] != '') {
            $where[] = ['cr.censor_status','=',$this->params['censor_status']];
        }
        if(isset($this->params['category_id']) && $this->params['category_id'] > 0 ){
//            if(ChatRecordEnum::CHAT_CREATION == $type){
//                $otherIds = CreationModel::where('category_id',$this->params['category_id'])->field('id')->select()->toArray();
//                $otherIds = array_column($otherIds,'id');
//                $where[] = ['cr.other_id','in',$otherIds];
//
//            }elseif(ChatRecordEnum::CHAT_SKILL == $type){
//                $otherIds = Skill::where('category_id',$this->params['category_id'])->column('id');
//                $where[] = ['cr.other_id','in',$otherIds];
//            }
            $where[] = ['cr.other_id','=',$this->params['category_id']];
        }
        //联网配置
        $network = $this->params['network'] ?? '';
        if($network){
            $where[] = ['network_plugin','exp',Db::raw('IS NOT NULL')];
        }elseif('0' === $network){
            $where[] = ['network_plugin','exp',Db::raw('IS NULL')];
        }
        //对话方式搜索
        $voice = $this->params['voice'] ?? '';
        if($voice){
            $where[] = ['voice_plugin','exp',Db::raw('IS NOT NULL')];
        }elseif('0' === $voice){
            $where[] = ['voice_plugin','exp',Db::raw('IS NULL')];
        }

        return $where;
    }

    /**
     * @notes AI对话记录列表
     * @return array
     * @author ljj
     * @date 2023/4/25 10:29 上午
     */
    public function lists(): array
    {
        $lists = ChatRecords::alias('cr')
            ->join('user u', 'u.id = cr.user_id')
            ->field('cr.id,u.avatar,u.nickname,cr.create_time,cr.ask,cr.reply,
            cr.use_tokens,cr.other_id,cr.type,cr.censor_status,cr.censor_result,cr.censor_num,cr.key,cr.model,cr.reply_type,cr.network_plugin')
            ->append(['censor_status_desc','censor_result_desc'])
            ->where($this->where())
            ->order('cr.id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        $type = $this->params['type'] ?? ChatRecordEnum::CHAT_QUESTION;
        $otherList = [];
        $otherIds = array_column($lists,'other_id');
        if(ChatRecordEnum::CHAT_CREATION == $type){
            $otherList = CreationModel::alias('CM')
                ->join('creation_category CC','CM.category_id = CC.id')
                ->where(['CM.id'=>$otherIds])
                ->column('CM.name,CM.id,CC.name as category_name','CM.id');

        }elseif (ChatRecordEnum::CHAT_SKILL == $type){
            $otherList = Skill::alias('S')
                ->join('skill_category SC','S.category_id = SC.id')
                ->where(['S.id'=>$otherIds])
                ->column('S.name,S.id,SC.name as category_name','S.id');

        }
        $dayUseCountList = [];
        $allUseCountList = [];
        if($otherIds && ChatRecordEnum::CHAT_QUESTION){
            $dayUseCountList = ChatRecords::where(['type'=>$type,'other_id'=>$otherIds])
                ->whereDay('create_time')
                ->group('other_id')
                ->column('count(id) as count','other_id');
            $allUseCountList = ChatRecords::where(['type'=>$type,'other_id'=>$otherIds])
                ->group('other_id')
                ->column('count(id) as count','other_id');
        }


        foreach ($lists as $key => $list) {
            if(ChatRecordEnum::REPLYTYPE_MODEL == $list['reply_type']){
                if(in_array($list['key'],ChatEnum::OPENAIMODEL)){
                    $modelDesc = $list['model']. '（'.$list['key'].'）';
                }else{
                    $modelDesc = ChatEnum::getChatModelLists($list['model']);
                }
            }else{
                $modelDesc = '系统回复';
            }
            $lists[$key]['model_desc'] = $modelDesc;
            //使用量
            $lists[$key]['day_use_count'] = $dayUseCountList[$list['other_id']] ?? [];
            $lists[$key]['all_use_count'] = $allUseCountList[$list['other_id']] ?? [];

            $lists[$key]['ask_str'] = json_encode($list['ask'],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            $lists[$key]['reply_str'] = json_encode($list['reply'],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            $other = $otherList[$list['other_id']] ?? [];
            if($other){
                $lists[$key]['other_desc'] =  $other['category_name'].'/'.$other['name'] ;
            }
            $network = false;
            if($list['network_plugin']){
                $network = true;
            }
            $lists[$key]['network'] = $network;
        }

        return $lists;
    }

    /**
     * @notes AI对话记录数量
     * @return int
     * @author ljj
     * @date 2023/4/25 10:29 上午
     */
    public function count(): int
    {
        return ChatRecords::alias('cr')
            ->join('user u', 'u.id = cr.user_id')
            ->where($this->where())
            ->count();
    }

    /**
     * @notes 导出文件名
     * @return string
     * @author ljj
     * @date 2023/8/24 2:49 下午
     */
    public function setFileName(): string
    {
        return '对话记录列表';
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
            'nickname' => '用户昵称',
            'create_time' => '提问时间',
            'model_desc' => '模型',
            'ask_str' => '提问',
            'reply_str' => '回复',
            'censor_status_desc' => '审核状态',
        ];
    }
}