import request from '@/utils/request'

// 创作分类列表
export function creationCategoryList(params?: any) {
    return request.get(
        { url: '/creation.creationCategory/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

// 新增创作分类
export function addCreationCategory(params: any) {
    return request.post({ url: '/creation.creationCategory/add', params })
}
// 编辑创作分类
export function editCreationCategory(params: any) {
    return request.post({ url: '/creation.creationCategory/edit', params })
}
// 删除创作分类
export function delCreationCategory(params: any) {
    return request.post({ url: '/creation.creationCategory/del', params })
}
// 更新创作分类状态
export function changeCreationCategoryStatus(params: any) {
    return request.post({ url: '/creation.creationCategory/status', params })
}
