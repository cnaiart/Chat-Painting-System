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
use app\common\model\task\TaskShare;

class TaskShareLists extends BaseAdminDataLists implements ListsExtendInterface,ListsExcelInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/4/17 5:55 下午
     */
    public function where()
    {
        $where = [];
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['u.sn|u.nickname|u.mobile','like','%'.$this->params['user_info'].'%'];
        }
        if (isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $where[] = ['ts.create_time','>=',strtotime($this->params['start_time'])];
        }
        if (isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['ts.create_time','<=',strtotime($this->params['end_time'])];
        }

        return $where;
    }

    /**
     * @notes 分享记录列表
     * @return array
     * @author ljj
     * @date 2023/4/17 6:03 下午
     */
    public function lists(): array
    {
        $lists = TaskShare::alias('ts')
            ->join('user u', 'u.id = ts.user_id')
            ->field('ts.id,ts.user_id,ts.channel,ts.click_num,ts.invite_num,ts.rewards,ts.create_time,u.nickname,ts.draw_rewards')
            ->where($this->where())
            ->with(['user'])
            ->append(['channel_desc'])
            ->order('id','desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * @notes 分享记录数量
     * @return int
     * @author ljj
     * @date 2023/4/17 6:03 下午
     */
    public function count(): int
    {
        return TaskShare::alias('ts')
            ->join('user u', 'u.id = ts.user_id')
            ->where($this->where())
            ->count();
    }

    /**
     * @notes 统计数据
     * @return array
     * @author ljj
     * @date 2023/4/17 6:02 下午
     */
    public function extend()
    {
        return [
            'today_share_num' => TaskShare::alias('ts')
                ->join('user u', 'u.id = ts.user_id')
                ->whereDay('ts.create_time')
                ->where($this->where())
                ->count(),
            'invite_num' => TaskShare::alias('ts')
                ->join('user u', 'u.id = ts.user_id')
                ->where($this->where())
                ->sum('invite_num'),
            'today_chat_rewards' => TaskShare::alias('ts')
                ->join('user u', 'u.id = ts.user_id')
                ->whereDay('ts.create_time')
                ->where($this->where())
                ->sum('rewards'),
            'today_draw_rewards' => TaskShare::alias('ts')
                ->join('user u', 'u.id = ts.user_id')
                ->whereDay('ts.create_time')
                ->where($this->where())
                ->sum('draw_rewards'),
            'total_rewards' => TaskShare::alias('ts')
                ->join('user u', 'u.id = ts.user_id')
                ->where($this->where())
                ->sum('rewards') + TaskShare::alias('ts')
                    ->join('user u', 'u.id = ts.user_id')
                    ->where($this->where())
                    ->sum('draw_rewards'),
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
        return '分享记录列表';
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
            'channel_desc' => '分享渠道',
            'create_time' => '分享时间',
            'click_num' => '点击量',
            'invite_num' => '成功邀请',
            'rewards' => '分享奖励',
        ];
    }
}