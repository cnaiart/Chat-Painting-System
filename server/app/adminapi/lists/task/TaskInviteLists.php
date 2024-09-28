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

namespace app\adminapi\lists\task;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsExtendInterface;
use app\common\model\task\TaskInvite;
use app\common\model\user\User;

class TaskInviteLists extends BaseAdminDataLists implements ListsExtendInterface,ListsExcelInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/4/17 10:10 上午
     */
    public function where()
    {
        $where = [];
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $user_ids = User::where('sn|nickname|mobile','like','%'.$this->params['user_info'].'%')->column('id');
            $where[] = ['user_id','in',$user_ids];
        }
        if (isset($this->params['new_user_info']) && $this->params['new_user_info'] != '') {
            $user_ids = User::where('sn|nickname|mobile','like','%'.$this->params['new_user_info'].'%')->column('id');
            $where[] = ['new_user_id','in',$user_ids];
        }
        if (isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $user_ids = User::where('create_time','>=',strtotime($this->params['start_time']))->column('id');
            $where[] = ['new_user_id','in',$user_ids];
        }
        if (isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $user_ids = User::where('create_time','<=',strtotime($this->params['end_time']))->column('id');
            $where[] = ['new_user_id','in',$user_ids];
        }

        return $where;
    }

    /**
     * @notes 邀请记录列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/4/17 10:22 上午
     */
    public function lists(): array
    {
        $lists = TaskInvite::field('id,user_id,new_user_id,rewards,draw_rewards')
            ->where($this->where())
            ->with(['user','new_user'])
            ->order('id','desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();
        foreach ($lists as &$list) {
            $list['invite_nickname'] = $list['user']['nickname'] ?? '';
            $list['new_nickname'] = $list['new_user']['nickname'] ?? '';
            $list['new_channel_desc'] = $list['new_user']['channel_desc'] ?? '';
            $list['new_create_time'] = $list['new_user']['create_time'] ?? '';
        }

        return $lists;
    }

    /**
     * @notes 邀请记录数量
     * @return int
     * @author ljj
     * @date 2023/4/17 10:22 上午
     */
    public function count(): int
    {
        return TaskInvite::where($this->where())->count();
    }

    /**
     * @notes 统计数据
     * @return array
     * @author ljj
     * @date 2023/4/17 10:24 上午
     */
    public function extend()
    {
        return [
            'today_invite_num' => TaskInvite::where($this->where())->whereDay('create_time')->count(),
            'today_chat_rewards' => TaskInvite::where($this->where())->whereDay('create_time')->sum('rewards'),
            'today_draw_rewards' => TaskInvite::where($this->where())->whereDay('create_time')->sum('draw_rewards'),
            'invite_num' => TaskInvite::where($this->where())->count(),
            'total_rewards' => TaskInvite::where($this->where())->sum('rewards') + TaskInvite::where($this->where())->sum('draw_rewards'),
        ];
    }

    /**
     * @notes 导出文件名
     * @return string
     * @author ljj
     * @date 2023/8/24 2:49 下午
     */
    public function setFileName(): string
    {
        return '邀请记录列表';
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
            'invite_nickname' => '邀请人',
            'new_nickname' => '新用户',
            'new_channel_desc' => '注册来源',
            'new_create_time' => '注册时间',
            'rewards' => '邀请奖励',
        ];
    }
}