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

namespace app\api\validate;


use app\common\model\draw\DrawSquare;
use app\common\validate\BaseValidate;

class DrawSquareValidate extends BaseValidate
{
    protected $rule = [
//        'category_id' => 'require',
//        'prompts' => 'require',
//        'image' => 'require',
        'draw_records_id' => 'require',
        'id' => 'require',
        'praise' => 'require|in:0,1'
    ];

    protected $message = [
//        'category_id.require' => '分类缺失',
//        'image.require' => '图片缺失',
//        'prompts.require' => '提示词缺失',
        'draw_records_id.require' => '来源缺失',
        'id.require' => '参数缺失',
        'praise.require' => '参数错误',
        'praise.in' => '参数值错误',
    ];


    public function sceneAdd()
    {
        return $this->only(['draw_records_id'])
            ->append('draw_records_id','checkAdd');
    }

    public function scenePraise()
    {
        return $this->only(['id','praise']);
    }


    /**
     * @notes 校验分享
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/9/11 11:49 上午
     */
    public function checkAdd($value,$rule,$data)
    {
//        $draw_square = DrawSquare::where(['draw_records_id'=>$data['draw_records_id']])->findOrEmpty();
//        if (!$draw_square->isEmpty()) {
//            return '该绘画已分享，无法重复分享！';
//        }

        return true;
    }
}