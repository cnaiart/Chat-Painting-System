import request from '@/utils/request'

// 技能列表
export function skillModelLists(params: any) {
    return request.get(
        { url: '/skill.skill/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

// 新增技能
export function addSkillModel(params: any) {
    return request.post({ url: '/skill.skill/add', params })
}

// 编辑技能
export function editkillModel(params: any) {
    return request.post({ url: '/skill.skill/edit', params })
}

// 删除技能
export function delSkillModel(params: any) {
    return request.post({ url: '/skill.skill/del', params })
}

//更新技能状态
export function changeSkillModelStatus(params: any) {
    return request.post({ url: '/skill.skill/status', params })
}

// 技能模型列表
export function modelList() {
    return request.get({ url: '/skill.skill/modelList' })
}
