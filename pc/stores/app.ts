import { defineStore } from 'pinia'
import { getConfig } from '~~/api/app'
import { getrechargePackage } from '~~/api/recharge'

interface AppSate {
    config: Record<string, any>
}

export const useAppStore = defineStore({
    id: 'appStore',
    state: (): AppSate => ({
        config: {}
    }),
    getters: {
        getIsInstall: (state) => state.config.install ?? true,
        getCardCodeConfig: (state) => state.config.card_code || {},
        getBulletinConfig: (state) => state.config.bulletin_config || {},
        getWebsiteConfig: (state) => state.config.website || {},
        getLoginConfig: (state) => state.config.login || {},
        getCopyrightConfig: (state) => state.config.copyright || [],
        getQrcodeConfig: (state) => state.config.qrcode || {},
        getTabbarConfig: (state) => state.config.tabbar || {},
        getAdminUrl: (state) => state.config.admin_url,
        getIsShowVip: (state) => state.config.member_package_status || false,
        getIsShowRecharge: (state) =>
            state.config.recharge_package_status || false,
        getDrawConfig: (state) => state.config.draw || {},
        getPcPageConfig: (state) => state.config.pc_page || {},
        getDrawSquareConfig: (state) => state.config.draw_square_config || {},
        getChatConfig: (state) => state.config.chat || {},
        getMindMapConfig: (state) => state.config.mindmap_config || {},
        getIsVoiceOpen: (state) =>
            !!state.config.voice?.voice_broadcast?.is_open || false
    },
    actions: {
        getImageUrl(url: string) {
            return url.indexOf('http') ? `${this.config.domain}${url}` : url
        },
        async getConfig() {
            const config = await getConfig()
            this.config = config
        }
    }
})
