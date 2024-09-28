import request from '@/utils/request'

export function getVoiceConfig() {
    return request.get({ url: '/setting.VoiceSetting/getConfig' })
}

export function setVoiceConfig(params: any) {
    return request.post({ url: '/setting.VoiceSetting/setConfig', params })
}
