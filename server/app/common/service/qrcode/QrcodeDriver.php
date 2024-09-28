<?php

namespace app\common\service\qrcode;

use app\common\cache\KeyPoolCache;
use app\common\enum\KeyPoolEnum;
use app\common\enum\qrcode\QrcodeEnum;
use app\common\model\KeyLog;
use app\common\service\ConfigService;
use app\common\service\qrcode\engine\QrcodeMewx;
use app\common\service\qrcode\engine\QrcodeZsy;

class QrcodeDriver
{
    protected $apiType;

    protected $engine;

    protected $apiToken = '';

    public function __construct(string $model)
    {
        // 绘画开关
        $openConfig = ConfigService::get('art_qrcode_config', 'is_open', 0);
        if (!$openConfig) {
            throw new \Exception("绘画功能已关闭");
        }

        if (!empty($model)) {
            $this->apiType = $model;
        } else {
            $this->apiType = ConfigService::get('art_qrcode_config', 'type', QrcodeEnum::API_MEWX);
        }

        // 获取绘画秘钥
        $this->apiToken = (new KeyPoolCache($this->apiType))->getKey();
        if (empty($this->apiToken)) {
            throw new \Exception('请在后台设置艺术二维码配置');
        }

        switch ($this->apiType) {
            // 星月熊
            case QrcodeEnum::API_MEWX:
                $apiConfig = ConfigService::get('art_qrcode_config', QrcodeEnum::API_MEWX);
                if (!empty($apiConfig['proxy_url'])) {
                    $this->engine = new QrcodeMewx($this->apiToken, $apiConfig['proxy_url']);
                } else {
                    $this->engine = new QrcodeMewx($this->apiToken);
                }
                break;
            // 知数云
            case QrcodeEnum::API_ZHISHUYUN:
                $this->engine = new QrcodeZsy($this->apiToken);
                break;
            default:
                throw new \Exception('绘画服务异常');
        }
    }

    public function setKeyLog($recordId)
    {
        // 增加key使用记录
        KeyLog::create([
            'type' => KeyPoolEnum::TYPE_QRCODE,
            'ai_key' => $this->apiType,
            'key' => $this->apiToken,
            'record_id' => $recordId,
        ]);
    }

    // 生图
    public function imagine(array $params)
    {
        return $this->engine->imagine($params);
    }

    // 详情
    public function detail($imgId)
    {
        return $this->engine->detail($imgId);
    }

}