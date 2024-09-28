import request from '@/utils/request'

// 创作分类列表
export function creationModelList(params: any) {
    return request.get(
        { url: '/creation.creationModel/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

// 创作分类列表
export function creationModelDetail(params: any) {
    return request.get({ url: '/creation.creationModel/detail', params })
}

// 新增创作分类
export function addCreationModel(params: any) {
    return request.post({ url: '/creation.creationModel/add', params })
}
// 编辑创作分类
export function editCreationModel(params: any) {
    return request.post({ url: '/creation.creationModel/edit', params })
}
// 删除创作分类
export function delCreationModel(params: any) {
    return request.post({ url: '/creation.creationModel/del', params })
}
// 更新创作分类状态
export function changeCreationModelStatus(params: any) {
    return request.post({ url: '/creation.creationModel/status', params })
}
