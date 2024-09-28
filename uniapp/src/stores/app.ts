import { defineStore } from 'pinia'
import { getConfig } from '@/api/app'

interface AppSate {
    config: Record<string, any>
}
export const useAppStore = defineStore({
    id: 'appStore',
    state: (): AppSate => ({
        config: {}
    }),
    getters: {
        getCardCodeConfig: (state) => state.config.card_code || {},
        getBulletinConfig: (state) => state.config.bulletin_config || {},
        getWebsiteConfig: (state) => state.config.website || {},
        getLoginConfig: (state) => state.config.login || {},
        getTabbarConfig: (state) => state.config.tabbar || {},
        getH5Config: (state) => state.config.webPage || {},
        getShareConfig: (state) => state.config.share || {},
        getIsShowVip: (state) => state.config.member_package_status || false,
        getIsShowRecharge: (state) =>
            state.config.recharge_package_status || false,
        getChatConfig: (state) => state.config.chat || {},
        getDrawConfig: (state) => state.config.draw || {},
        getDrawSquareConfig: (state) => state.config.draw_square_config || {},
        getMindMapConfig: (state) => state.config.mindmap_config || {},
        //语音播报
        getIsVoiceOpen: (state) =>
            !!state.config.voice?.voice_broadcast?.is_open || false,
        getIsVoiceTransfer: (state) =>
            !!state.config.voice?.voice_input?.is_open || false,
        getIsVoiceChat: (state) =>
            !!state.config.voice?.voice_chat?.is_open || false
    },
    actions: {
        getImageUrl(url: string) {
            return url.indexOf('http') ? `${this.config.domain}${url}` : url
        },
        async getConfig(payload?: any) {
            const data = await getConfig(payload)
            this.config = data
        }
    }
})
