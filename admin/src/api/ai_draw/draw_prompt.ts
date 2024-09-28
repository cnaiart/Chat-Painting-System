import request from '@/utils/request'

//问题分类列表
export function getPromptList(params: any) {
    return request.get(
        { url: '/draw.draw_prompt/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

//新增问题示例
export function addPrompt(params: any) {
    return request.post({ url: '/draw.draw_prompt/add', params })
}

//编辑问题示例
export function editPrompt(params: any) {
    return request.post({ url: '/draw.draw_prompt/edit', params })
}

//删除问题示例
export function delPrompt(params: any) {
    return request.post({ url: '/draw.draw_prompt/delete', params })
}
//修改状态
export function editPromptStatus(params: any) {
    return request.post({ url: '/draw.draw_prompt/status', params })
}
