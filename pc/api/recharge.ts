//获取充值包
export function getrechargePackage(params: any) {
    return $request.get({
        url: '/recharge/rechargePackage',
        params
    })
}

//获取任务信息
export function getTaskMsg(params?: any) {
    return $request.get({
        url: '/share/task',
        params
    })
}

//获取分享ID
export function toShare(params?: any) {
    return $request.get({
        url: '/share/share',
        params
    })
}

