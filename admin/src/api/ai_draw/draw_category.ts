import request from '@/utils/request'

// 绘画分类列表
export function getDrawCategoryList(params?: any) {
    return request.get({ url: '/draw.draw_category/lists', params })
}

// 新增绘画分类
export function addDrawCategory(params: any) {
    return request.post({ url: '/draw.draw_category/add', params })
}

// 新增绘画分类
export function editDrawCategory(params: any) {
    return request.post({ url: '/draw.draw_category/edit', params })
}

// 删除绘画分类
export function delDrawCategory(params: any) {
    return request.post({ url: '/draw.draw_category/delete', params })
}

// 修改状态
export function editDrawCategoryStatus(params: any) {
    return request.post({ url: '/draw.draw_category/status', params })
}

// 全部绘画分类列表
export function allDrawCategoryList() {
    return request.get({ url: '/draw.draw_category/all' })
}
