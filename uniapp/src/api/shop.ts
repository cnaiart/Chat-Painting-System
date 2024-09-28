import { client } from '@/utils/client'
import request from '@/utils/request'

//首页数据
export function getIndex() {
    return request.get({ url: '/index/index' })
}

// 装修页面
export function getDecorate(data: any) {
    return request.get({ url: '/index/decorate', data }, { ignoreCancel: true })
}

export function addVisit() {
    return request.post({ url: '/index/visit', data: { terminal: client } })
}
