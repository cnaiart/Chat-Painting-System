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

namespace app\api\lists;


use app\common\enum\ChatRecordEnum;
use app\common\model\ChatRecords;
use app\common\model\ChatRecordsCollect;
use app\common\model\skill\Skill;
use app\common\service\FileService;

class ChatLogLists extends BaseApiDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/6/20 10:38 上午
     */
    public function where()
    {
        $where[] = ['is_show','=',1];
        $where[] = ['user_id','=',$this->userId];
        $where[] = ['type','=',$this->params['type']];
        $where[] = ['category_id','=',$this->params['category_id'] ?? 0];
        $where[] = ['censor_status','in',[ChatRecordEnum::CENSOR_STATUS_WAIT,ChatRecordEnum::CENSOR_STATUS_COMPLIANCE,ChatRecordEnum::CENSOR_STATUS_FAIL]];
        if (isset($this->params['other_id']) && $this->params['other_id'] != '') {
            $where[] = ['other_id','=',$this->params['other_id']];
        }

        return $where;
    }

    /**
     * @notes 聊天记录列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/6/20 10:37 上午
     */
    public function lists(): array
    {
//        $sort = 'asc';
//        if ($this->params['type'] == ChatRecordEnum::CHAT_CREATION || $this->params['type'] == ChatRecordEnum::CHAT_MIND) {
//            $sort = 'desc';
//        }
        $sort = 'desc';
        $lists = ChatRecords::field('id,type,ask,other_id,reply,extra,network_plugin,voice_plugin,create_time')
            ->where($this->where())
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['id'=>$sort])
            ->select()
            ->toArray();

        $id = array_column($lists,'id');
        $collectId = ChatRecordsCollect::where(['records_id'=>$id])->column('id','records_id');
        $data = [];
        $otherIds = array_column($lists,'other_id');
        $otherLists = [];
        if(ChatRecordEnum::CHAT_SKILL == $this->params['type']){
            $otherLists = Skill::where(['id'=>$otherIds])->column('image','id');
        }
        //如果是首页对话记录，需要将数组反转
        if($this->params['type'] == ChatRecordEnum::CHAT_QUESTION || $this->params['type'] == ChatRecordEnum::CHAT_SKILL){
            $lists = array_reverse($lists);
        }
        foreach ($lists as $list) {
            $voicePlugin = $list['voice_plugin'] ?? [];
            if ($list['type'] == ChatRecordEnum::CHAT_CREATION || ChatRecordEnum::CHAT_MIND == $list['type']) {
                $data[] = [
                    'id' => $list['id'],
                    'ask' => $list['ask'],
                    'reply' => $list['reply'],
                    'create_time' => $list['create_time'],
                    'extra' => $list['extra'],
                    'is_collect' => $collectId[$list['id']] ?? 0,
                ];
            } else {
                $voiceInput  = $voicePlugin['voice_input'] ?? '';
                $voiceOutput = $voicePlugin['voice_output'] ?? '';
                $voiceInput && $voiceInput = FileService::getFileUrl($voiceInput);
                $voiceOutput && $voiceOutput = FileService::getFileUrl($voiceOutput);
                $data[] = [
                    'id' => $list['id'],
                    'type' => 1,//用户
                    'content' => (string)$list['ask'],
                    'create_time' => $list['create_time'],
                    'is_collect' => $collectId[$list['id']] ?? 0,
                    'voice_file'=> $voiceInput
                ];
                $data[] = [
                    'id' => $list['id'],
                    'type' => 2,//AI
                    'content' => $list['reply'],
                    'create_time' => $list['create_time'],
                    'is_collect' => $collectId[$list['id']] ?? 0,
                    'avatar'        => FileService::getFileUrl($otherLists[$list['other_id']] ?? ''),
                    'voice_file' =>  $voiceOutput
                ];
            }
        }
//        if ($this->params['type'] == ChatRecordEnum::CHAT_CREATION) {
//            krsort($data);
//            $data = array_values($data);
//        }

        return $data;
    }

    /**
     * @notes 聊天记录数量
     * @return int
     * @author ljj
     * @date 2023/6/20 10:37 上午
     */
    public function count(): int
    {
        return ChatRecords::where($this->where())->count();
    }
}