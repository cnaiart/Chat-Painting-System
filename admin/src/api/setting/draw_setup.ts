import request from '@/utils/request'

export function getDrawConfig(params?: any) {
    return request.get({ url: '/setting.draw_setting/getDrawSetting', params })
}
export function setDrawConfig(data: any) {
    return request.post({ url: '/setting.draw_setting/setDrawSetting', data })
}
