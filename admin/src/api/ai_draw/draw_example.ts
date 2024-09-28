import request from '@/utils/request'

//示例列表
export function getPromptExampleList(params: any) {
    return request.get(
        { url: '/draw.draw_prompt_example/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

//示例详情
export function getPromptExampleDetail(params: { id: number | string }) {
    return request.get({ url: '/draw.draw_prompt_example/detail', params })
}

//新增示例
export function addPromptExample(params: any) {
    return request.post({ url: '/draw.draw_prompt_example/add', params })
}

//编辑示例
export function editPromptExample(params: any) {
    return request.post({ url: '/draw.draw_prompt_example/edit', params })
}

//删除示例
export function delPromptExample(params: any) {
    return request.post({ url: '/draw.draw_prompt_example/delete', params })
}
//修改状态
export function editPromptExampleStatus(params: any) {
    return request.post({ url: '/draw.draw_prompt_example/status', params })
}
