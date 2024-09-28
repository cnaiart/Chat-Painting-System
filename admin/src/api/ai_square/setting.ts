import request from '@/utils/request'

export type DrawSquareSetFormType = {
    is_allow_share: number // 是  允许用户分享：1-开启；0-关闭；
    chat_rewards: number // 是  分享奖励对话次数
    draw_rewards: number // 是  分享奖励绘画次数
    max_share: number // 是  每天最多分享次数
    is_auto_pass: number // 是  自动通过审核：1-开启；0-关闭；
    is_show_user: number // 是  显示用户信息：1-开启；0-关闭；
}

export function drawSquareGetConfig(): Promise<DrawSquareSetFormType> {
    return request.get({ url: '/draw.draw_square/getConfig' })
}

//获取卡密设置
export function drawSquareSetConfig(params: DrawSquareSetFormType) {
    return request.post({ url: '/draw.draw_square/setConfig', params })
}