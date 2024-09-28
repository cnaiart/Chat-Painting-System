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
namespace app\adminapi\lists\setting;
use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\chat\ChatEnum;
use app\common\enum\DrawEnum;
use app\common\enum\KeyPoolEnum;
use app\common\enum\qrcode\QrcodeEnum;
use app\common\enum\voice\VoiceEnum;
use app\common\lists\ListsExcelInterface;
use app\common\model\KeyPool;

/**
 * key池列表类
 * Class KeyPoolLists
 * @package app\adminapi\lists
 */
class KeyPoolLists extends BaseAdminDataLists implements ListsExcelInterface
{

    public function setSearch()
    {
        $where[] = ['type','=',$this->params['type'] ?? KeyPoolEnum::TYPE_CHAT];
        if(isset($this->params['ai_key']) && $this->params['ai_key']){
            $where[] = ['ai_key','=',$this->params['ai_key']];
        }
        if(isset($this->params['keyword']) && $this->params['keyword']){
            $where[] = ['key','like','%'.$this->params['keyword'].'%'];
        }
        if(isset($this->params['start_time']) && $this->params['start_time']){
            $where[] = ['create_time','>=',strtotime($this->params['start_time'])];
        }
        if(isset($this->params['end_time']) && $this->params['end_time']){
            $where[] = ['create_time','<=',strtotime($this->params['end_time'])];
        }
        if(isset($this->params['status']) && '' != $this->params['status']){
            $where[] = ['status','=',$this->params['status']];
        }
        return $where;

    }

    /**
     * @notes 实现数据列表
     * @return array
     * @author 令狐冲
     * @date 2021/7/6 00:33
     */
    public function lists(): array
    {
        $type = $this->params['type'] ?? KeyPoolEnum::TYPE_CHAT;

        $lists = KeyPool::where($this->setSearch())
            ->field('id,ai_key,status,key,notice,use_time,create_time,update_time')
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id desc')
            ->select();

        foreach ($lists as $key =>  $list)
        {
            switch ($type){
                case KeyPoolEnum::TYPE_CHAT:
                    $lists[$key]['ai_key_desc'] = ChatEnum::getChatName($list['ai_key']);
                    break;
                case  KeyPoolEnum::TYPE_DRAW:
                    $lists[$key]['ai_key_desc'] = DrawEnum::getDrawDefaultConfig($list['ai_key'])['name'] ?? '';
                    break;
                case KeyPoolEnum::TYPE_VOICE:
                    $lists[$key]['ai_key_desc'] = VoiceEnum::getChannel($list['ai_key']) ?? '';
                    break;
                case KeyPoolEnum::TYPE_QRCODE:
                    $lists[$key]['ai_key_desc'] = QrcodeEnum::getAiModelName($list['ai_key']) ?? '';
                    break;
                case KeyPoolEnum::TYPE_VOICE_INPUT:
                    $lists[$key]['ai_key_desc'] = VoiceEnum::getChannel($list['ai_key']) ?? '';
                    break;
                case KeyPoolEnum::TYPE_VOICE_CHAT:
                    $lists[$key]['ai_key_desc'] = VoiceEnum::getChannel($list['ai_key']) ?? '';
                    break;
            }
            $queryBalance = false;
//            if(in_array($list['ai_key'],ChatEnum::OPEN_CHAT)){
//                $queryBalance = true;
//            }

            $list['query_balance'] = $queryBalance;

            $lists[$key]['api_domain'] = $list['notice']['api'] ?? '';
            $lists[$key]['stop_reason'] = $list['notice']['notice'] ?? '';
            $lists[$key]['status_desc'] = $list['status'] == 1 ? '开启' : '关闭';
            if($list['use_time']){
                $lists[$key]['use_time'] = date('Y-m-d H:i:s',$list['use_time']);
            }
            $changeBtn = false;
            if($list['notice']){
                $changeBtn = true;
            }
            $lists[$key]['change_btn'] = $changeBtn;


        }
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
        return KeyPool::where($this->setSearch())
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
        return 'key池列表';
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
            'ai_key_desc' => '接口类型',
            'key' => '密钥',
            'status_desc' => '状态',
            'api_domain' => '自定义api域名',
            'stop_reason' => '停用原因',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}