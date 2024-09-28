import request from '@/utils/request'

//对话记录列表
export function chatRecordsLists(params?: any) {
    return request.get({ url: '/chat_records/lists', params }, { ignoreCancelToken: true })
}

//删除对话记录
export function chatRecordsDel(params?: any) {
    return request.post({ url: '/chat_records/del', params })
}

//创作分类
export function chatRecordCategoryList(params?: any) {
    return request.get({ url: '/ChatRecords/otherLists', params })
}
