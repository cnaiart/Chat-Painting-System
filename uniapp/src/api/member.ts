import request from '@/utils/request'

export function getMemberLists() {
    return request.get({ url: '/member/lists' }, { isAuth: true })
}

export function getCommentLists(data: any) {
    return request.get({ url: '/member/commentLists', data }, { isAuth: true })
}

export function memberBuy(data: any) {
    return request.post({ url: '/member/buy', data }, { isAuth: true })
}

export function getMemberBuyLog(data?: any) {
    return request.get({ url: '/member/buyLog', data }, { isAuth: true })
}
