import request from '@/utils/request'

// 获取内容审核配置
export function getConfig() {
    return request.get({ url: '/setting.content_censor/getConfig' })
}

// 设置内容审核配置
export function setConfig(params: any) {
    return request.post({ url: '/setting.content_censor/setConfig', params })
}
