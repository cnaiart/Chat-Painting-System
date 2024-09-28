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

use app\common\enum\qrcode\QrcodeEnum;
use app\common\validate\BaseValidate;

class QrcodeValidate extends BaseValidate
{
    protected $rule = [
        'model' => 'require',
        'type' => 'require|in:' . QrcodeEnum::TYPE_TEXT . ',' . QrcodeEnum::TYPE_IMAGE . '|checkType',
        'way' => 'require|checkWay',
        'prompt' => 'max:1000',
        'qr_content' => 'max:100',
    ];

    protected $message = [
        'model.require' => '请选择绘画模型',
        'type.require' => '请选择二维码生成模式',
        'way.require' => '请选择生成模式',
        'prompt.max' => '提示词需在1000字符内',
        'qr_content.max' => '二维码内容请控制在100字符内',
    ];

    public function sceneImagine()
    {
        return $this->only(['model', 'type', 'way', 'prompt', 'qr_content']);
    }

    /**
     * @notes 生成模式校验
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author mjf
     * @date 2023/10/17 17:50
     */
    protected function checkType($value, $rule, $data)
    {
        // 文本模式
        if ($value == QrcodeEnum::TYPE_TEXT && empty($data['qr_content'])) {
            return '请输入二维码内容';
        }

        // 图片模式
        if ($value == QrcodeEnum::TYPE_IMAGE && empty($data['qr_image'])) {
            return '请上传图片';
        }

        return true;
    }

    /**
     * @notes 校验生成模式
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author mjf
     * @date 2023/10/23 11:49
     */
    protected function checkWay($value, $rule, $data)
    {
        //非模板模式，提示词必填
        if ($value == QrcodeEnum::WAY_SELF && empty($data['prompt'])) {
            return '请填写关键词';
        }
        return true;
    }

}