import request from '@/utils/request'

// key列表
export function getKeyDownRuleLists(params?: any) {
    return request.get(
        { url: '/setting.key_down_rule/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}
export function getKeyDownRuleDetail(params?: any) {
    return request.get({ url: '/setting.key_down_rule/detail', params })
}

// ai模型
export function getKeyDownRuleAiModel(params?: any) {
    return request.get({ url: '/setting.key_down_rule/getAimodel', params })
}

// 新增
export function addKeyDownRule(data?: any) {
    return request.post({ url: '/setting.key_down_rule/add', data })
}

// 编辑
export function editKeyDownRule(data?: any) {
    return request.post({ url: '/setting.key_down_rule/edit', data })
}

// 删除
export function delKeyDownRule(data?: any) {
    return request.post({ url: '/setting.key_down_rule/del', data })
}

// 修改状态
export function statusKeyDownRule(data?: any) {
    return request.post({ url: '/setting.key_down_rule/status', data })
}
