<script setup lang="ts">
import { onLaunch, onShow } from '@dcloudio/uni-app'
import { useAppStore } from './stores/app'
import { useThemeStore } from './stores/theme'
import { useSharedId } from './mixins/share'
import { addVisit } from './api/shop'
import { useUserStore } from './stores/user'
import cache from './utils/cache'
import { SHARE_ID, USER_SN } from './enums/constantEnums'
import { strToParams } from './utils/util'
// import { useRoute, useRouter } from 'uniapp-router-next-zm'
import { useRoute, useRouter } from 'uniapp-router-next'
const router = useRouter()
const route = useRoute()
const appStore = useAppStore()
const userStore = useUserStore()
const { getTheme } = useThemeStore()

const cacheInvite = (query: any = {}) => {
    const { share_id } = query
    const user_sn =
        query.user_sn ||
        strToParams(decodeURIComponent(query['scene']))['user_sn']
    if (share_id) {
        cache.set(SHARE_ID, share_id)
    }
    if (user_sn) {
        cache.set(USER_SN, user_sn)
    }
}
//#ifdef H5
const setH5WebIcon = () => {
    const config = appStore.getWebsiteConfig
    let favicon: HTMLLinkElement = document.querySelector('link[rel="icon"]')!
    if (favicon) {
        favicon.href = config.shop_logo
        return
    }
    favicon = document.createElement('link')
    favicon.rel = 'icon'
    favicon.href = config.shop_logo
    document.head.appendChild(favicon)
}

//#endif
const getConfig = async () => {
    await appStore.getConfig()
    //#ifdef H5
    setH5WebIcon()
    //#endif
    const { status, page_status, page_url } = appStore.getH5Config
    if (route.meta.webview) return
    //处理关闭h5渠道
    //#ifdef H5
    if (status == 0) {
        if (page_status == 1) return (location.href = page_url)
        router.reLaunch('/pages/empty/empty')
    }
    //#endif
}

onLaunch(async (opinion) => {
    getConfig()
    getTheme()
    await addVisit()
    userStore.getUser()
    useSharedId()
    cacheInvite(opinion?.query)
})
</script>
<style lang="scss">
//
</style>
