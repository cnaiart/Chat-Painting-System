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
namespace app\common\command;

use app\api\logic\DrawLogic;
use app\common\enum\DrawEnum;
use app\common\model\draw\DrawRecords;
use app\common\service\ConfigService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

/**
 * 绘画缩略图
 * Class DrawThumbnail
 * @package app\common\command
 */
class DrawThumbnail extends Command
{
    protected function configure()
    {
        $this->setName('draw_thumbnail')
            ->setDescription('处理绘画记录缩略图');
    }

    protected function execute(Input $input, Output $output)
    {
        try {
            $drawLists = DrawRecords::where(['status' => DrawEnum::STATUS_SUCCESS])
                ->select()->toArray();

            if (empty($drawLists)) {
                echo "无需更新数据";
                return;
            }

            $count = 0;

            foreach ($drawLists as $item) {
                if ($item['model'] == DrawEnum::API_YIJIAN_SD) {
                    continue;
                }

                if (!empty($item['thumbnail']) || empty($item['image_url'])) {
                    continue;
                }

                $needHandleImage = $item['image_url'];
                if ($item['model'] == DrawEnum::API_MDDAI_MJ) {
                    $apiConfig = ConfigService::get('draw_config', DrawEnum::API_MDDAI_MJ, []);
                    if (!empty($apiConfig['proxy_url'])) {
                        $needHandleImage = str_replace("https://cdn.discordapp.com", $apiConfig['proxy_url'], $item['image_url']);
                    }
                }

                $itemThumbnail = DrawLogic::getThumbnail($needHandleImage);
                if (!empty($itemThumbnail)) {
                    // 更新缩略图字段
                    DrawRecords::where('id', $item['id'])
                        ->update(['thumbnail' => $itemThumbnail]);

                    $count += 1;
                }
            }

            echo "绘图缩略图更新" . $count . "记录";

        } catch (\Exception $e) {
            echo "绘图缩略图更新失败:" . $e->getMessage() . $e->getLine();
            Log::write('绘图缩略图更新失败:' . $e->getMessage() . $e->getLine());
        }
    }


}