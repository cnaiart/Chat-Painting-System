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

namespace app\api\controller;

use app\common\enum\FileEnum;
use app\common\service\UploadService;
use Exception;
use think\response\Json;


/** 上传文件
 * Class UploadController
 * @package app\api\controller
 */
class UploadController extends BaseApiController
{

    /**
     * @notes 上传图片
     * @return Json
     * @author 段誉
     * @date 2022/9/20 18:11
     */
    public function image()
    {
        try {
            $saveDir = 'uploads/images';

            // 绘图上传参考图时校验
            $drawUpload = $this->request->post('type', '');
            if (!empty($drawUpload) && $drawUpload == 'draw') {
                $file = $this->request->file('file');
                if (empty($file)) {
                    throw new Exception('未找到上传文件的信息');
                }

                // 限制上传2m
                $sizeLimit = 2 * 1024 * 1024;
                if ($file->getSize() > $sizeLimit) {
                    throw new Exception('上传文件不可大于2m');
                }

                // 保存路径
                $saveDir = 'uploads/draw_refer';
            }

            $result = UploadService::image(0, $this->userId, FileEnum::SOURCE_USER, $saveDir);
            return $this->success('上传成功', $result);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }


}