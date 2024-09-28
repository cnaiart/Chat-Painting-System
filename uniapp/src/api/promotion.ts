import request from '@/utils/request'

// 分销中心
export function getDistributionIndex(data?: any) {
    return request.get({ url: '/distribution/index', data }, { isAuth: true })
}

// 分销申请
export function distributionApply(data: any) {
    return request.post(
        { url: '/distribution/distributionApply', data },
        { isAuth: true }
    )
}

// 分销订单
export function distributionOrder(data?: any) {
    return request.get(
        { url: '/distribution/distributionOrder', data },
        { isAuth: true }
    )
}

// 粉丝列表
export function distributionFans(data?: any) {
    return request.get(
        { url: '/distribution/distributionFans', data },
        { isAuth: true }
    )
}

// 提现 ---------------------------------------------------------------------------------
export type WithdrawApplyType = {
    money: string | number // 提现金额
    account: string | number // 支付宝账号
    real_name: string | number // 支付宝姓名
    type: string | number
    money_qr_code: string[] | string
}
// 提现申请
export function withdrawApply(data: WithdrawApplyType) {
    return request.post({ url: '/withdraw/apply', data }, { isAuth: true })
}

// 提现记录
export function withdrawList(data?: { page_no: number; page_size: number }) {
    return request.get({ url: '/withdraw/lists', data }, { isAuth: true })
}
// 提现方式
export function withdrawWay(data?: any) {
    return request.get(
        { url: '/withdraw/withdrawType', data },
        { isAuth: true }
    )
}
