import request from '@/utils/request'

//问题分类列表
export function getQuestionSampleList(params: any) {
    return request.get(
        { url: '/question.questionSample/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

//新增问题示例
export function addQusetionSample(params: any) {
    return request.post({ url: '/question.questionSample/add', params })
}

//编辑问题示例
export function editQusetionSample(params: any) {
    return request.post({ url: '/question.questionSample/edit', params })
}

//删除问题示例
export function delQusetionSample(params: any) {
    return request.post({ url: '/question.questionSample/del', params })
}
//修改状态
export function editQusetionSampleStatus(params: any) {
    return request.post({ url: '/question.questionSample/status', params })
}
