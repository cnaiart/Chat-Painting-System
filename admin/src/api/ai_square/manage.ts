import request from '@/utils/request'

//列表
export function getDrawSquareList(params: any) {
    return request.get(
        { url: '/draw.draw_square/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

//详情
export function getDrawSquareDetail(params: { id?: number }) {
    return request.get({ url: '/draw.draw_square/detail', params })
}

//新增
export function addDrawSquare(params: any) {
    return request.post({ url: '/draw.draw_square/add', params })
}

//编辑
export function editDrawSquare(params: any) {
    return request.post({ url: '/draw.draw_square/edit', params })
}

//审核
export function auditDrawSquare(params: any) {
    return request.post({ url: '/draw.draw_square/verifyStatus', params })
}

//删除
export function delDrawSquare(params: any) {
    return request.post({ url: '/draw.draw_square/del', params })
}

//修改状态
export function editDrawSquareStatus(params: any) {
    return request.post({ url: '/draw.draw_square/isShow', params })
}
