<?php

namespace app\common\service\qrcode\engine;

interface QrcodeInterface
{
    // 生图
    public function imagine(array $params);
}