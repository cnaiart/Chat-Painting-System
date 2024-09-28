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

namespace app\common\model\draw;


use app\common\enum\DrawSquareEnum;
use app\common\model\BaseModel;
use app\common\model\user\User;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\storage\Driver as StorageDriver;
use think\facade\Log;
use think\model\concern\SoftDelete;

class DrawSquare extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    /**
     * @notes 审核状态
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2023/8/1 6:04 下午
     */
    public function getVerifyStatusDescAttr($value,$data)
    {
        return DrawSquareEnum::getVerifyStatusDesc($data['verify_status']);
    }

    /**
     * @notes 用户信息
     * @param $value
     * @param $data
     * @return array
     * @author ljj
     * @date 2023/8/1 6:10 下午
     */
    public function getUserInfoAttr($value,$data)
    {
        $result = [];
        if ($data['source'] == DrawSquareEnum::SOURCE_ADMIN) {
            $image = empty($data['avatar']) ? ConfigService::get('website', 'pc_logo') : $data['avatar'];
            $name = empty($data['nickname']) ? ConfigService::get('website', 'pc_title') : $data['nickname'];
            $result = [
                'image' => FileService::getFileUrl($image),
                'name' => $name,
            ];
        }
        if ($data['source'] == DrawSquareEnum::SOURCE_USER) {
            $user = User::where('id',$data['operate_id'])->findOrEmpty()->toArray();
            $result = [
                'image' => $user['avatar'] ?? '',
                'name' => $user['nickname'] ?? '',
            ];
        }

        return $result;
    }

    /**
     * @notes 分类名称
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2023/8/31 11:55 上午
     */
    public function getCategoryNameAttr($value,$data)
    {
        return DrawSquareCategory::where('id',$data['category_id'])->value('name') ?? '-';
    }

    /**
     * @notes 来源
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2023/8/31 12:12 下午
     */
    public function getSourceDescAttr($value,$data)
    {
        return DrawSquareEnum::getSourceDesc($data['source']);
    }

    /**
     * @notes 缩略图
     * @param $value
     * @param $data
     * @return string
     * @author ljj
     * @date 2023/9/4 3:24 下午
     */
    public function getThumbnailAttr($value,$data)
    {
        return empty($value) ? '' : FileService::getFileUrl($value);
    }

    /**
     * @notes 生成缩略图
     * @param $originalImagePath
     * @return string
     * @author 段誉
     * @date 2023/8/4 17:00
     */
    public function getThumbnail($originalImagePath)
    {
        try {
            // 保存路径
            $saveDir = 'uploads/thumbnail/' . date('Ymd') . '/';
            $fileName = basename($originalImagePath);

            // 缩略图保存路径
            $thumbnailImagePath = public_path() . $saveDir;

            if (!is_dir($thumbnailImagePath)) {
                mkdir($thumbnailImagePath, 0755, true);
            }

            $thumbnailWidth = 400;//缩略图宽度

            // 创建原始图像资源
            $originalImage = imagecreatefrompng($originalImagePath);

            // 获取原始图像的宽度和高度
            $originalWidth = imagesx($originalImage);
            $originalHeight = imagesy($originalImage);

//            // 计算缩略图的宽度和高度
//            if ($originalWidth > $originalHeight) {
//                $thumbnailHeight = intval($originalHeight * $thumbnailWidth / $originalWidth);
//            } else {
//                $thumbnailWidth = intval($originalWidth * $thumbnailHeight / $originalHeight);
//            }
            //计算缩略图高度
            $thumbnailHeight = intval($originalHeight * $thumbnailWidth / $originalWidth);

            // 创建缩略图资源
            $thumbnailImage = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);

            // 将原始图像复制到缩略图中，并进行缩放
            imagecopyresampled($thumbnailImage, $originalImage, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $originalWidth, $originalHeight);

            // 保存缩略图到文件
            imagepng($thumbnailImage, $thumbnailImagePath . $fileName);

            // 释放资源
            imagedestroy($originalImage);
            imagedestroy($thumbnailImage);

            // 第三方存储的情况
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine' => ConfigService::get('storage')
            ];
            if ($config['default'] != 'local') {
                // 第三方存储
                $filePath = $saveDir . $fileName;
                $localPath = $thumbnailImagePath . $fileName;
                $StorageDriver = new StorageDriver($config);
                if (!$StorageDriver->fetch($localPath, $filePath)) {
                    throw new \Exception('绘图缩略图保存失败:' . $StorageDriver->getError());
                }
            }

            return $saveDir . $fileName;
        } catch (\Exception $e) {
            Log::write('缩略图生成失败:' . $e->getMessage() . $e->getLine());
            return "";
        }
    }

    /**
     * @notes 原始输入词
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2023/11/9 4:42 下午
     */
    public function getOriginalPromptsAttr($value,$data)
    {
        $result = DrawRecords::where('id',$data['draw_records_id'])->field('prompt,prompt_en')->findOrEmpty()->toArray();
        if (empty($result)) {
            $result['prompt'] = $data['prompts_cn'];
            $result['prompt_en'] = $data['prompts'];
        }
        return $result;
    }


    /**
     * @notes 头像
     * @param $value
     * @return string
     * @author ljj
     * @date 2023/12/5 10:41 上午
     */
    public function getAvatarAttr($value)
    {
        return trim($value) ? FileService::getFileUrl($value) : '';
    }

    /**
     * @notes 头像
     * @param $value
     * @return mixed|string
     * @author ljj
     * @date 2023/12/5 10:41 上午
     */
    public function setAvatarAttr($value)
    {
        return trim($value) ? FileService::setFileUrl($value) : '';
    }
}