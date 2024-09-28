import type { RequestEventStreamOptions } from 'ofetch'

export function getCreationLists(params?: any) {
    return $request.get({ url: '/creation/lists', params })
}

export function getCreationDetail(params: any) {
    return $request.get({ url: '/creation/detail', params })
}

export function creationChat(params: any, config: RequestEventStreamOptions) {
    return $request.eventStream(
        { url: '/chat/creationChat', params, method: 'POST' },
        config
    )
}

//收藏
export function creationCollection(params?: any) {
    return $request.post({ url: '/creation/collect', params })
}
export function getCreationCategory(params?: any) {
    return $request.get({ url: '/creation/categoryLists', params })
}

export function getCreationListsByCategory(params?: any) {
    return $request.get({ url: '/creation/creationLists', params })
}
