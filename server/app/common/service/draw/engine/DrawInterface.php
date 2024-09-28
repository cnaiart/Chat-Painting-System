<?php

namespace app\common\service\draw\engine;

interface DrawInterface
{
    // 文生图，图生图
    public function imagine(array $params);

    // 图片放大变换
    public function imagineUv(array $params);
}