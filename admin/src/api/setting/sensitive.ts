import request from '@/utils/request'

// 敏感词列表
export function getlists(params: any) {
    return request.get(
        { url: '/setting.SensitiveWord/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}
export function addSensitive(data: any) {
    return request.post({ url: '/setting.SensitiveWord/add', data })
}
export function editSensitive(data: any) {
    return request.post({ url: '/setting.SensitiveWord/edit', data })
}
export function delSensitive(data: any) {
    return request.post({ url: '/setting.SensitiveWord/del', data })
}

export function setSensitiveConfig(data: any) {
    return request.post({ url: '/setting.sensitive_word/setConfig', data })
}

export function getSensitiveConfig() {
    return request.get({ url: '/setting.sensitive_word/getConfig' })
}
