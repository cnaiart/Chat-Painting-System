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

use app\common\validate\BaseValidate;

class DrawImageValidate extends BaseValidate
{
    protected $rule = [
        'prompt' => 'require|max:1024',
        'action' => 'require|checkAction',
    ];

    protected $message = [
        'prompt.require' => '请填写绘画描述',
        'prompt.max' => '绘画描述需在1024字符内',
        'action.require' => '操作参数缺失',
    ];

    public function sceneImagine()
    {
        return $this->only(['prompt', 'action']);
    }


    /**
     * @notes 绘图动作校验
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author 段誉
     * @date 2023/6/20 11:06
     */
    protected function checkAction($value, $rule, $data)
    {
        $actionArr = [
            "generate",
            "upsample1",
            "upsample2",
            "upsample3",
            "upsample4",
            "variation1",
            "variation2",
            "variation3",
            "variation4"
        ];

        if (!in_array($value, $actionArr)) {
            return '操作异常';
        }

        if ($value != 'generate') {
            if (empty($data['image_id'])) {
                return '请选择图片进行操作';
            }
        }

        return true;
    }

}