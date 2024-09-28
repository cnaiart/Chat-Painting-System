import request from '@/utils/request'

export function getMindMapConfig() {
    return request.get({ url: '/setting.mindmap/getConfig' })
}

export function setMindMapConfig(params: any) {
    return request.post({ url: '/setting.mindmap/setConfig', params })
}
