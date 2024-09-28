import request from '@/utils/request'

//支付方式
export function getPayWay(data?: any) {
    return request.get({ url: '/pay/payWay', data }, { isAuth: true })
}

// 预支付
export function prepay(data: any) {
    return request.post({ url: '/pay/prepay', data }, { isAuth: true })
}

// 预支付
export function getPayResult(data: any) {
    return request.get({ url: '/pay/payStatus', data }, { isAuth: true })
}

// 获取ios支付配置
export function getIosPayConfig() {
    return request.get(
        { url: '/pay/iosPayConfig' },
        { isAuth: true, ignoreCancel: true }
    )
}
