
export function addopinion(params?: any) {
    return $request.post({ url: '/feedback/add', params })
}
export function getmemberlists() {
    return $request.post({ url: '/member/lists' })
}
export function getcollectLists(params?: any) {
    return $request.get({ url: '/chat_records/collectLists', params })
}
export function cancelCollect(params?: any) {
    return $request.post({ url: '/chat_records/cancelCollect', params })
}
export function getMemberBuyLog() {
    return $request.get({ url: '/member/buyLog' })
}

