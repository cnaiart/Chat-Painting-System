<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\adminapi\lists\finance;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\user\AccountLogEnum;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\user\UserAccountLog;
use app\common\service\FileService;


/**
 * 账记流水列表
 * Class AccountLogLists
 * @package app\adminapi\lists\finance
 */
class AccountLogLists extends BaseAdminDataLists implements ListsSearchInterface,ListsExcelInterface
{

    /**
     * @notes 搜索条件
     * @return array
     * @author 段誉
     * @date 2023/2/24 15:26
     */
    public function setSearch(): array
    {
        return [
            '=' => ['al.change_type'],
        ];
    }


    /**
     * @notes 搜索条件
     * @author 段誉
     * @date 2023/2/24 15:26
     */
    public function queryWhere()
    {
        $where = [];
        // 明细类型
        $type = (isset($this->params['type']) && $this->params['type'] != '') ? $this->params['type'] : 'um';
        switch ($type) {
            case 'money':
                $where[] = ['change_type', 'in', AccountLogEnum::getUserMoneyChangeType()];
                break;
            case 'draw':
                $where[] = ['change_type', 'in', AccountLogEnum::getBalanceDrawChangeType()];
                break;
            case 'um':
            default:
                $where[] = ['change_type', 'in', AccountLogEnum::getBalanceChangeType()];
                break;
        }

        if (!empty($this->params['user_info'])) {
            $where[] = ['u.sn|u.nickname|u.mobile', 'like', '%' . $this->params['user_info'] . '%'];
        }

        if (!empty($this->params['start_time'])) {
            $where[] = ['al.create_time', '>=', strtotime($this->params['start_time'])];
        }

        if (!empty($this->params['end_time'])) {
            $where[] = ['al.create_time', '<=', strtotime($this->params['end_time'])];
        }

        return $where;
    }


    /**
     * @notes 获取列表
     * @return array
     * @author 段誉
     * @date 2023/2/24 15:31
     */
    public function lists(): array
    {
        $field = 'u.nickname,u.sn,u.avatar,u.mobile,al.action,al.change_amount,al.left_amount,al.change_type,al.source_sn,al.create_time';
        $lists = UserAccountLog::alias('al')
            ->join('user u', 'u.id = al.user_id')
            ->field($field)
            ->where($this->searchWhere)
            ->where($this->queryWhere())
            ->order('al.id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['avatar'] = FileService::getFileUrl($item['avatar']);
            $item['change_type_desc'] = AccountLogEnum::getChangeTypeDesc($item['change_type']);
            $symbol = $item['action'] == AccountLogEnum::INC ? '+' : '-';
            $item['change_amount'] = $symbol . clearZero($item['change_amount']);
            $item['left_amount'] = clearZero($item['left_amount']);
        }

        return $lists;
    }


    /**
     * @notes 获取数量
     * @return int
     * @author 段誉
     * @date 2023/2/24 15:36
     */
    public function count(): int
    {
        return UserAccountLog::alias('al')
            ->join('user u', 'u.id = al.user_id')
            ->where($this->queryWhere())
            ->where($this->searchWhere)
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
        return '明细列表';
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
            'sn' => '用户编号',
            'nickname' => '用户昵称',
            'change_amount' => '变动数量',
            'left_amount' => '剩余数量',
            'change_type_desc' => '变动类型',
            'source_sn' => '记录来源',
            'create_time' => '记录时间',
        ];
    }
}