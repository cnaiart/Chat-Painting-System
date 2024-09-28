import request from '@/utils/request'

//问题分类列表
export function getQuestionCategoryList(params?: any) {
    return request.get(
        { url: '/question.questionCategory/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

//新增问题分类
export function addQusetionCategory(params: any) {
    return request.post({ url: '/question.questionCategory/add', params })
}

//新增问题分类
export function editQusetionCategory(params: any) {
    return request.post({ url: '/question.questionCategory/edit', params })
}

//删除问题分类
export function delQusetionCategory(params: any) {
    return request.post({ url: '/question.questionCategory/del', params })
}
//修改状态
export function editQusetionCategoryStatus(params: any) {
    return request.post({ url: '/question.questionCategory/status', params })
}
