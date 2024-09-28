import request from '@/utils/request'

//设置绘画计费模型
export function setDrawBillingConfig(data?: any) {
    return request.post({ url: '/setting.drawSetting/setDrawBillingConfig', data })
}

//获取绘画配置参数
export function getDrawBillingConfig(params?: any) {
    return request.get({ url: '/setting.drawSetting/getDrawBillingConfig', params })
}

// 设置会话配置参数
export function getChatBillingConfig(params?: any) {
    return request.get({ url: '/setting.chatSetting/getChatBillingConfig', params })
}

// 设置会话配置参数
export function setChatBillingConfig(data?: any) {
    return request.post({ url: '/setting.chatSetting/setChatBillingConfig', data })
}
// 获取模型计费
export function getQrcodeBillingConfig(params?: any) {
    return request.get({ url: '/setting.drawSetting/getQrcodeBillingConfig', params })
}

// 设置模型计费
export function setQrcodeBillingConfig(data?: any) {
    return request.post({ url: '/setting.drawSetting/setQrcodeBillingConfig', data })
}
