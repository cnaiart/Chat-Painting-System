import request from '@/utils/request'

// 技能分类列表
export function skillCategoryLists(params?: any) {
    return request.get(
        { url: '/skill.skillCategory/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

// 新增技能分类
export function addSkillCategory(params: any) {
    return request.post({ url: '/skill.skillCategory/add', params })
}

// 编辑技能分类
export function editkillCategory(params: any) {
    return request.post({ url: '/skill.skillCategory/edit', params })
}

// 删除技能分类
export function delSkillCategory(params: any) {
    return request.post({ url: '/skill.skillCategory/del', params })
}

//更新技能状态
export function changeSkillCategoryStatus(params: any) {
    return request.post({ url: '/skill.skillCategory/status', params })
}
