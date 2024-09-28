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

namespace app\api\lists;

use app\common\enum\DrawSquareEnum;
use app\common\model\draw\DrawSquare;


class DrawSquareLists extends BaseApiDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/8/31 4:07 下午
     */
    public function where()
    {
        $where[] = ['ds.is_show','=',1];
        $where[] = ['ds.verify_status','=',DrawSquareEnum::VERIFY_STATUS_SUCCESS];
        if (isset($this->params['keyword']) && $this->params['keyword'] != '') {
            $where[] = ['dr.prompt|dr.prompt_en|ds.prompts_cn|ds.prompts', 'like', '%'.$this->params['keyword'].'%'];
        }
        if (isset($this->params['category_id']) && $this->params['category_id'] != '') {
            if ($this->params['category_id'] == 0) {
//                $where[] = ['dsp.square_id', '=', 'ds.id'];
                $where[] = ['dsp.user_id', '=', $this->userId];
            } else {
                $where[] = ['ds.category_id', '=', $this->params['category_id']];
            }
        }

        return $where;
    }

    /**
     * @notes 绘画广场列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/8/31 4:09 下午
     */
    public function lists(): array
    {
        if (isset($this->params['category_id']) && $this->params['category_id'] == 0) {
            //喜欢列表重新处理排序
            $lists = DrawSquare::alias('ds')
                ->leftjoin('draw_records dr', 'dr.id = ds.draw_records_id')
                ->leftjoin('draw_square_praise dsp', 'dsp.square_id = ds.id and dsp.user_id = '.$this->userId)
                ->field('ds.id,ds.source,ds.operate_id,ds.category_id,ds.prompts,ds.prompts_cn,ds.image,ds.create_time,ds.thumbnail,ds.draw_records_id,ds.avatar,ds.nickname,IF(dsp.id,1,0) as is_praise')
                ->append(['category_name','user_info','source_desc','original_prompts'])
                ->where($this->where())
                ->limit($this->limitOffset, $this->limitLength)
                ->order(['dsp.id' => 'desc'])
                ->select()
                ->toArray();
        } else {
            $lists = DrawSquare::alias('ds')
                ->leftjoin('draw_records dr', 'dr.id = ds.draw_records_id')
                ->leftjoin('draw_square_praise dsp', 'dsp.square_id = ds.id and dsp.user_id = '.$this->userId)
                ->field('ds.id,ds.source,ds.operate_id,ds.category_id,ds.prompts,ds.prompts_cn,ds.image,ds.create_time,ds.thumbnail,ds.draw_records_id,ds.avatar,ds.nickname,IF(dsp.id,1,0) as is_praise')
                ->append(['category_name','user_info','source_desc','original_prompts'])
                ->where($this->where())
                ->limit($this->limitOffset, $this->limitLength)
                ->order(['ds.id' => 'desc'])
                ->select()
                ->toArray();
        }

        return $lists;
    }

    /**
     * @notes 绘画广场数量
     * @return int
     * @author ljj
     * @date 2023/8/31 4:09 下午
     */
    public function count(): int
    {
        return DrawSquare::alias('ds')
            ->leftjoin('draw_records dr', 'dr.id = ds.draw_records_id')
            ->leftjoin('draw_square_praise dsp', 'dsp.square_id = ds.id and dsp.user_id = '.$this->userId)
            ->where($this->where())
            ->count();
    }
}