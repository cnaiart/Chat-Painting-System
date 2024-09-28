import { reactive, provide } from 'vue'
import { qrcodeConfig } from '@/api/qrcode'
import './type.d'

interface Options {
    dataTransform(data: any): void
}

const useConfigEffect = (options?: Options) => {
    const { dataTransform } = options || {}

    const config: QrConfigResType = reactive({
        is_open: -1,
        // 绘画模型
        draw_model: [],
        // 示例
        example: {
            status: 1,
            content: ''
        },
        // 星月熊
        mewx: {
            version: {}, // 版本
            model: [], // 模型
            template: [] // 模版
        },
        // 知数云
        zhishuyun_qrcode: {
            pixel_style: [],
            template: []
        }
    })

    const getConfig = async () => {
        const data: QrConfigResType = await qrcodeConfig()
        for (const key in data) {
            // @ts-ignore
            config[key] = data[key]
        }
        if (config.draw_model.length !== 0) {
            // @ts-ignore
            const result: DrawModelType = config.draw_model.find(
                (item: DrawModelType) => {
                    return item.default
                }
            )
            // @ts-ignore
            dataTransform(result)
        }
    }

    provide('config', config)
    provide('getConfig', getConfig)

    return {
        config,
        getConfig
    }
}

export default useConfigEffect
