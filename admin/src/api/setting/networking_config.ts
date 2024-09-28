import request from '@/utils/request'

export function getNetworkingConfig() {
    return request.get({ url: '/setting.ai_setting/getNetworkConfig' })
}

export function setNetworkingConfig(params: any) {
    return request.post({ url: '/setting.ai_setting/setNetworkConfig', params })
}
