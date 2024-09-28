import request from '@/utils/request'

// 用户列表
export function getUserList(params: any) {
    return request.get({ url: '/user.user/lists', params }, { ignoreCancelToken: true })
}

// 用户详情
export function getUserDetail(params: any) {
    return request.get({ url: '/user.user/detail', params })
}

// 用户编辑
export function userEdit(params: any) {
    return request.post({ url: '/user.user/edit', params })
}

// 用户编辑
export function adjustMoney(params: any) {
    return request.post({ url: '/user.user/adjustMoney', params })
}

// 用户添加
export function accountAdd(params: any) {
    return request.post({ url: '/user.user/add', params })
}

//
export function adjustUserDraw(params: any) {
    return request.post({ url: '/user.user/adjustUserDraw', params })
}

// 用户编辑会员时间
export function adjustMember(params: any) {
    return request.post({ url: '/user.user/adjustMember', params })
}
//加入黑名单
export function disable(params: any) {
    return request.post({ url: '/user.user/blacklist', params })
}

//会员开通记录
export function getOpenVipRecord(params: any) {
    return request.get({ url: '/user.user/userMember', params })
}

// 会员排序
export function userMemberSort(params: any) {
    return request.post({ url: '/user.user/userMemberSort', params })
}

//会员列表
export function getMemberList(params?: any) {
    return request.get({ url: '/member.member_package/commonLists', params })
}
export function rePassword(params?: any) {
    return request.post({ url: '/user.user/rePassword', params })
}

export function adjustLeader(params?: any) {
    return request.post({ url: '/user.user/adjustLeader', params })
}
