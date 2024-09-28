import request from '@/utils/request'

export type QrcodeFormType = {
    model: string // 是 绘画模型
    type: number // 是 生成模式 1-文本模式 2-图片模式
    way: number // 是 生成模式 1-自定义(模型) 2-模板
    qr_content: string // 否 二维码内容 （文本模式时必填）
    qr_image: string // 否 二维码图片 （图片模式时必填）
    prompt: string // 否 关键词
    prompt_params: PromptParams | string // 否 其他参数
    model_id: string | number // 否 模型id
    template_id: string | number // 否 模板id
    aspect_ratio: string | number // 否 比例 （知数云）
    pixel_style: string | number // 否 码点形状（知数云）
    marker_shape: string | number // 否 码眼选择（知数云）
}

export type PromptParams = {
    v: string // 版本取值枚举 2 1.1 1) 示例：--v 2 --v 1.1
    iw: number // (明显程序取值范围 0 - 1, 保留两位小数) 示例： --iw 0.45
    seed: string //  (取值范围1 - 999999999 ) 示例: --seed 123
    shape: string // (码眼选择范围) ["square", "circle", "plus", "box", "octagon", "random", "tiny-plus"], 示例 --shape random ，默认为 random
    ar: string // (尺寸选择) 范围 ["1:1", "9:16", "16:9", "3:4","4:3"] 示例 --ar 1:1 ，默认为 1:1
}

// 获取艺术二维码配置
export function qrcodeConfig() {
    return request.get({ url: '/qrcode/config' })
}

// 生成艺术二维码
export function qrcodeImagine(data: QrcodeFormType) {
    return request.post({ url: '/qrcode/imagine', data, method: 'POST' })
}

// 生成艺术二维码详情
export function qrcodeDetail(data: { records_id: number[] }) {
    return request.post({ url: '/qrcode_records/detail', data })
}

// 生成艺术二维码记录
export function qrcodeRecord(data: any) {
    return request.get(
        {
            url: '/qrcode_records/records',
            data
        },
        { ignoreCancel: true }
    )
}

// 删除
export function qrcodeDelete(data: { ids: number[] }) {
    return request.post({
        url: '/qrcode_records/delete',
        data,
        method: 'POST'
    })
}
