<?php

namespace app\common\service\draw;

use app\common\cache\KeyPoolCache;
use app\common\enum\DrawEnum;
use app\common\enum\KeyPoolEnum;
use app\common\model\KeyLog;
use app\common\service\ConfigService;
use app\common\service\draw\engine\DrawDalle3;
use app\common\service\draw\engine\DrawMdd;
use app\common\service\draw\engine\DrawSd;
use app\common\service\draw\engine\DrawYj;
use app\common\service\draw\engine\DrawZsy;
use think\Exception;

class DrawDriver
{
    protected $apiType;

    protected $engine;

    protected $apiToken = '';

    protected $apiSecret = '';

    public function __construct(string $model = '', array $extra = [])
    {
        // 绘画开关
        $openConfig = ConfigService::get('draw_config', 'is_open', 0);
        if (!$openConfig) {
            throw new \Exception("绘画功能已关闭");
        }

        if (!empty($model)) {
            $this->apiType = $model;
        } else {
            $this->apiType = ConfigService::get('draw_config', 'type', DrawEnum::API_ZHISHUYUN_FAST);
        }

        // 获取绘画秘钥
        $tokenData = (new KeyPoolCache($this->apiType))->getKey();
        if ($model != DrawEnum::API_SD && empty($tokenData)) {
            throw new \Exception('请在后台设置绘画配置');
        }

        switch ($this->apiType) {
            // 知数云
            case DrawEnum::API_ZHISHUYUN_FAST:
            case DrawEnum::API_ZHISHUYUN_RELAX:
            case DrawEnum::API_ZHISHUYUN_TURBO:
                $this->apiToken = $tokenData;
                $this->engine = new DrawZsy($this->apiType, $this->apiToken);
                break;
            // 官方直连-MJ
            case DrawEnum::API_MDDAI_MJ:
                // 自建直连地址
                $this->apiToken = $tokenData;
                $this->engine = new DrawMdd($this->apiToken);
                break;
            // 意间SD
            case DrawEnum::API_YIJIAN_SD:
                if (empty($tokenData['key']) || empty($tokenData['secret'])) {
                    throw new \Exception('请在后台设置绘画配置');
                }
                $this->engine = new DrawYj($tokenData['key'], $tokenData['secret'], $extra);
                $this->apiToken = $tokenData['key'];
                $this->apiSecret = $tokenData['secret'];
                break;
            // Dalle-3
            case DrawEnum::API_DALLE3:
                $this->apiToken = $tokenData;
                $apiConfig = ConfigService::get('draw_config', DrawEnum::API_DALLE3, []);
                if (!empty($apiConfig['proxy_url'])) {
                    $this->engine = new DrawDalle3($this->apiToken, $apiConfig['proxy_url']);
                } else {
                    $this->engine = new DrawDalle3($this->apiToken);
                }
                break;
            // SD
            case DrawEnum::API_SD:
                $apiConfig = ConfigService::get('draw_config', DrawEnum::API_SD, []);
                if (empty($apiConfig['proxy_url'])) {
                    throw new \Exception('请联系管理员完善绘画配置');
                } else {
                    $this->engine = new DrawSd($apiConfig['proxy_url']);
                }
                break;
            default:
                throw new \Exception('绘画服务异常');
        }
    }

    public function setKeyLog($recordId)
    {
        // 增加key使用记录
        KeyLog::create([
            'type' => KeyPoolEnum::TYPE_DRAW,
            'ai_key' => $this->apiType,
            'key' => $this->apiToken,
            'secret' => $this->apiSecret,
            'record_id' => $recordId,
        ]);
    }

    // 文生图，图生图
    public function imagine(array $params)
    {
        return $this->engine->imagine($params);
    }

    // 变大，变换
    public function imagineUv(array $params)
    {
        return $this->engine->imagineUv($params);
    }

}