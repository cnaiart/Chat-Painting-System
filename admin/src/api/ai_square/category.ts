import request from '@/utils/request'

// 创作分类列表
export function squareCategoryList(params?: any) {
    return request.get(
        { url: '/draw.draw_square_category/lists', params },
        {
            ignoreCancelToken: true
        }
    )
}

// 新增创作分类
export function addSquareCategory(params: any) {
    return request.post({ url: '/draw.draw_square_category/add', params })
}
// 编辑创作分类
export function editSquareCategory(params: any) {
    return request.post({ url: '/draw.draw_square_category/edit', params })
}
// 删除创作分类
export function delSquareCategory(params: any) {
    return request.post({ url: '/draw.draw_square_category/del', params })
}
// 更新创作分类状态
export function changeSquareCategoryStatus(params: any) {
    return request.post({ url: '/draw.draw_square_category/status', params })
}
