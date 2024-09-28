import request from '@/utils/request'

export function getShareId() {
    return request.get({ url: '/share/share' })
}

export function shareClick(data: any) {
    return request.post({ url: '/share/click', data })
}

export function bindInvite(data: any, token: string) {
    return request.post({ url: '/share/invite', data, header: { token } })
}

export function getTask() {
    return request.get({ url: '/share/task' })
}
