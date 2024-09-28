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
use app\common\lists\ListsExcelInterface;
use app\common\model\KeyDownRule;


class KeyDownRuleLists extends BaseAdminDataLists implements ListsExcelInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/9/15 3:14 下午
     */
    public function where()
    {
        $where[] = ['type','=',$this->params['type'] ?? KeyPoolEnum::TYPE_CHAT];
        if(isset($this->params['ai_key']) && $this->params['ai_key']){
            $where[] = ['ai_key','=',$this->params['ai_key']];
        }
        if(isset($this->params['status']) && $this->params['status'] != ''){
            $where[] = ['status','=',$this->params['status']];
        }
        return $where;

    }

    /**
     * @notes key下架规则列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/9/15 3:16 下午
     */
    public function lists(): array
    {
        $type = $this->params['type'] ?? KeyPoolEnum::TYPE_CHAT;

        $lists = KeyDownRule::where($this->where())
            ->field('id,type,ai_key,rule,prompt,status,create_time,update_time')
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id desc')
            ->select();

        foreach ($lists as $key =>  $list)
        {
            if(KeyPoolEnum::TYPE_CHAT == $type){
                $list['ai_key_desc'] = ChatEnum::getChatName($list['ai_key']);
            }else{
                $list['ai_key_desc'] = DrawEnum::getDrawDefaultConfig($list['ai_key'])['name'] ?? '';
            }
            $lists[$key]['status_desc'] = $list['status'] == 1 ? '开启' : '关闭';

        }
        return $lists->toArray();

    }

    /**
     * @notes key下架规则数量
     * @return int
     * @author ljj
     * @date 2023/9/15 3:17 下午
     */
    public function count(): int
    {
        return KeyDownRule::where($this->where())->count();
    }

    /**
     * @notes 导出文件名
     * @return string
     * @author ljj
     * @date 2023/9/15 3:17 下午
     */
    public function setFileName(): string
    {
        return 'key下架规则列表';
    }

    /**
     * @notes 导出字段
     * @return string[]
     * @author ljj
     * @date 2023/9/15 3:17 下午
     */
    public function setExcelFields(): array
    {
        return [
            'ai_key_desc' => '接口类型',
            'rule' => '停用规则',
            'prompt' => '通用提示',
            'status_desc' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}