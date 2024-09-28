import request from '@/utils/request'

// 生成记录
export function recordList(params?: any) {
    return request.get({ url: '/qrcode.qrcode_records/lists', params })
}

// 删除记录
export function delRecord(params?: any) {
    return request.post({ url: '/qrcode.qrcode_records/delete', params })
}

//下拉选项
export function dropDownList(params?: any) {
    return request.get({ url: '/qrcode.qrcode_records/option', params })
}

//获取示例
export function getExample(params?: any) {
    return request.get({ url: '/qrcode.qrcode_records/getExample', params })
}

//设置示例
export function setExample(params?: any) {
    return request.post({ url: '/qrcode.qrcode_records/setExample', params })
}
