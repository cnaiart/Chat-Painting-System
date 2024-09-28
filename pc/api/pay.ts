
//获取支付方式
export function getPayWayList(params) {
    return $request.get({ url: '/pay/payWay', params })
}

//充值
export function apiRechage(params) {
    return $request.post({ url: '/recharge/recharge', params })
}

//会员购买
export function memberBuy(params) {
    return $request.post({ url: '/member/buy', params })
}

//支付
export function prepay(params) {
    return $request.post({ url: '/pay/prepay', params })
}

//订单状态
export function OrderStatus(params) {
    return $request.get({ url: '/pay/payStatus', params })
}