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

namespace app\adminapi\logic\setting;


use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

class ContentCensorLogic extends BaseLogic
{
    /**
     * @notes 获取内容审核配置
     * @return array
     * @author ljj
     * @date 2023/6/19 5:42 下午
     */
    public function getConfig()
    {
        $result = [
            'is_open' => ConfigService::get('content_censor','is_open',0),//内容审核
            'app_id' => ConfigService::get('content_censor','app_id'),
            'api_key' => ConfigService::get('content_censor','api_key'),
            'secret_key' => ConfigService::get('content_censor','secret_key'),
            'ask_open' => ConfigService::get('content_censor','ask_open',0),//问题审核
            'prompt_open' => ConfigService::get('content_censor','prompt_open',0),//绘画提示词审核
            'image_open' => ConfigService::get('content_censor','image_open',0),//绘画图片审核
        ];

        return $result;
    }

    /**
     * @notes 设置内容审核配置
     * @param array $params
     * @return bool
     * @author ljj
     * @date 2023/6/19 5:43 下午
     */
    public function setConfig(array $params)
    {
        ConfigService::set('content_censor','is_open',$params['is_open']);
        ConfigService::set('content_censor','app_id',$params['app_id']);
        ConfigService::set('content_censor','api_key',$params['api_key']);
        ConfigService::set('content_censor','secret_key',$params['secret_key']);
        ConfigService::set('content_censor','ask_open',$params['ask_open']);
        ConfigService::set('content_censor','prompt_open',$params['prompt_open'] ?? 0);
        ConfigService::set('content_censor','image_open',$params['image_open'] ?? 0);
        return true;
    }
}