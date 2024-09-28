import request from '@/utils/request'

export function getAiSetting(params?: any) {
    return request.get({ url: '/setting.chatSetting/getChatConfig', params })
}
// 获取参数
export function getConfig(params?: any) {
    return request.get({ url: '/setting.aiSetting/getconfig', params })
}

// 获取参数
export function setConfig(params: any) {
    return request.post({ url: '/setting.chatSetting/setChatConfig', params })
}

//查询余额
export function checkBalance(data?: any) {
    return request.post({ url: '/setting.aiSetting/queryBalance', data })
}

//获取绘画配置参数
export function getDrawConfig(params?: any) {
    return request.get({ url: '/setting.drawSetting/getDrawConfig', params })
}

//设置会话配置参数
export function setDrawConfig(data?: any) {
    return request.post({ url: '/setting.drawSetting/setDrawConfig', data })
}

//设置会话配置参数
export function getAiChatConfig(data?: any) {
    return request.get({ url: '/AiConfig/getAiChatConfig', data })
}

//获取ai模型配置
export function getAIModelConfig(params?: any) {
    return request.get({ url: '/setting.drawSetting/getQrcodeConfig', params })
}

//设置ai模型配置
export function setAIModelConfig(params?: any) {
    return request.post({ url: '/setting.drawSetting/setQrcodeConfig', params })
}
