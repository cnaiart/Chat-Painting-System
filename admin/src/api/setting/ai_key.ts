import request from '@/utils/request'

// key列表
export function getKeyPoolLists(params?: any) {
    return request.get(
        { url: '/setting.KeyPool/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}
export function getKeyPoolDetail(params?: any) {
    return request.get({ url: '/setting.KeyPool/detail', params })
}

// ai模型
export function getKeyPoolAiModel(params?: any) {
    return request.get({ url: '/setting.KeyPool/getAimodel', params })
}

// 新增
export function addKeyPool(data?: any) {
    return request.post({ url: '/setting.KeyPool/add', data })
}

// 编辑
export function editKeyPool(data?: any) {
    return request.post({ url: '/setting.KeyPool/edit', data })
}

// 删除
export function delKeyPool(data?: any) {
    return request.post({ url: '/setting.KeyPool/del', data })
}

// 修改状态
export function statusKeyPool(data?: any) {
    return request.post({ url: '/setting.KeyPool/status', data })
}

// 查询余额
export function getKeyPoolQueryBalance(data?: any) {
    return request.post({ url: '/setting.KeyPool/queryBalance', data })
}
export function setKeyPoolConfig(data: any) {
    return request.post({ url: '/setting.KeyPool/setConfig', data })
}

export function getKeyPoolConfig() {
    return request.get({ url: '/setting.KeyPool/getConfig' })
}
