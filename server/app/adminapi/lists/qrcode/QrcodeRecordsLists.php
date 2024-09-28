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

namespace app\adminapi\lists\qrcode;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\qrcode\QrcodeEnum;
use app\common\enum\qrcode\MewxEnum;
use app\common\lists\ListsExcelInterface;
use app\common\model\qrcode\QrcodeRecords;
use app\common\service\FileService;

/**
 * 绘图记录
 * Class DrawRecordsLists
 * @package app\adminapi\lists\draw
 */
class QrcodeRecordsLists extends BaseAdminDataLists implements ListsExcelInterface
{

    /**
     * @notes 查询
     * @return array
     * @author mjf
     * @date 2023/10/17 14:34
     */
    public function queryWhere()
    {
        $where = [];
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['u.sn|u.nickname', 'like', '%' . $this->params['user_info'] . '%'];
        }
        if (isset($this->params['prompt']) && $this->params['prompt'] != '') {
            $where[] = ['r.prompt', 'like', '%' . $this->params['prompt'] . '%'];
        }
        if (isset($this->params['model_id']) && $this->params['model_id'] != '') {
            $where[] = ['r.model_id', '=', $this->params['model_id']];
        }
        if (isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $where[] = ['r.create_time', '>=', strtotime($this->params['start_time'])];
        }
        if (isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['r.create_time', '<=', strtotime($this->params['end_time'])];
        }
        if (isset($this->params['status']) && $this->params['status'] != '') {
            $where[] = ['r.status', '=', $this->params['status']];
        }
        if (isset($this->params['model']) && $this->params['model'] != '') {
            $where[] = ['r.model', '=', $this->params['model']];
        }
        return $where;
    }


    /**
     * @notes 列表
     * @return array
     * @author mjf
     * @date 2023/10/17 14:34
     */
    public function lists(): array
    {
        $lists = QrcodeRecords::alias('r')
            ->where($this->queryWhere())
            ->join('user u', 'u.id = r.user_id')
            ->field([ 'r.*', 'u.avatar', 'u.nickname'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['id' => 'desc'])
            ->hidden(['notify_snap'])
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['avatar'] = FileService::getFileUrl($item['avatar']);
            $item['image'] = !empty($item['image']) ? FileService::getFileUrl($item['image']) : "";
            $item['content'] = $item['qr_content'];
            if ($item['type'] == QrcodeEnum::TYPE_IMAGE) {
                $item['content'] = FileService::getFileUrl($item['qr_image']);
            }
            $item['model_text'] = QrcodeEnum::getAiModelName($item['model']);
        }

        return $lists;
    }


    /**
     * @notes 数量
     * @return int
     * @author mjf
     * @date 2023/10/17 14:35
     */
    public function count(): int
    {
        return QrcodeRecords::alias('r')
            ->where($this->queryWhere())
            ->join('user u', 'u.id = r.user_id')
            ->count();
    }

    /**
     * @notes 导出文件名
     * @return string
     * @author mjf
     * @date 2023/10/17 14:35
     */
    public function setFileName(): string
    {
        return '艺术二维码生成记录';
    }

    /**
     * @notes 导出信息
     * @return string[]
     * @author mjf
     * @date 2023/10/17 14:36
     */
    public function setExcelFields(): array
    {
        return [
            'nickname' => '用户昵称',
            'prompt' => '用户输入',
            'create_time' => '生成时间',
        ];
    }


}