import { getClient } from '~~/utils/env'

//首页数据
export function getIndex() {
    return $request.get({ url: '/pc/index' })
}

export function addVisit() {
    return $request.post({
        url: '/index/visit',
        params: { terminal: getClient() }
    })
}
