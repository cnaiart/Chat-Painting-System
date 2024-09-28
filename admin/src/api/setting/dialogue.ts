import request from '@/utils/request'

export function getChatConfig(params?: any) {
    return request.get({ url: '/setting.ai_setting/getChatConfig', params })
}
export function setChatConfig(data: any) {
    return request.post({ url: '//setting.ai_setting/setChatConfig', data })
}
