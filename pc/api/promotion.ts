// 分销申请
export function applyDistribution(params: any) {
    return $request.post({ url: '/distribution/distributionApply', params })
}

// 分销中心
export function distributionCenter(params?: any) {
    return $request.get({ url: '/distribution/index', params })
}

// 粉丝列表
export function fansList(params?: any) {
    return $request.get({ url: '/distribution/distributionFans', params })
}

//佣金明细变动类型
export function MoneyChangeType(params?: any) {
    return $request.get({ url: '/account_log/getMoneyChangeType', params })
}

//账户流水记录列表
export function MoneyList(params?: any) {
    return $request.get({ url: '/account_log/lists', params })
}

//提现记录列表
export function withdrawList(params?: any) {
    return $request.get({ url: '/withdraw/lists', params })
}

//提现申请
export function applyWithdraw(params?: any) {
    return $request.post({ url: '/withdraw/apply', params })
}

//提现详情
export function withdrawDetail(params?: any) {
    return $request.get({ url: '/withdraw/detail', params })
}

//提现方式
export function withdrawWay(params?: any) {
    return $request.get({ url: '/withdraw/withdrawType', params })
}