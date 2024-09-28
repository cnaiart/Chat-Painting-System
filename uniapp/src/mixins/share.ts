import { getShareId, shareClick } from '@/api/task'
// #ifdef H5
import wechatOa from '@/utils/wechat'
// #endif

import { paramsToStr } from '@/utils/util'
import { useAppStore } from '@/stores/app'
import { useRoute } from 'uniapp-router-next'
import router from '@/router'
import appConfig from '@/config'

export default {
    onShow() {
        const route = useRoute()
        const menuList = [
            'menuItem:share:appMessage',
            'menuItem:share:timeline',
            'menuItem:share:qq',
            'menuItem:share:weiboApp',
            'menuItem:share:QZone'
        ]
        if (!route.meta.share) {
            // 不允许分享的页面隐藏分享按钮
            // #ifdef MP-WEIXIN
            uni.hideShareMenu({
                hideShareItems: []
            })
            // #endif
            // #ifdef H5
            wechatOa.hideMenuItems(menuList)
            // #endif
        } else {
            // 公众号隐藏分享按钮后切换页面需要调显示按钮api
            // #ifdef H5
            wechatOa.showMenuItems(menuList)
            // #endif
        }
    },
    async onShareAppMessage() {
        const appStore = useAppStore()
        const options = await createShareOptions({
            ...appStore.getShareConfig,
            ...appStore.getWebsiteConfig
        })
        return {
            title: options.title,
            path: options.link,
            imageUrl: options.img_url
        }
    },
    // #ifdef MP-WEIXIN
    async onShareTimeline() {
        const appStore = useAppStore()
        const options = await createShareOptions({
            ...appStore.getShareConfig,
            ...appStore.getWebsiteConfig
        })
        const route = router.currentRoute.value
        return {
            title: options.title,
            query: route.query,
            imageUrl: options.img_url
        }
    }
    // #endif
}

export async function createShareOptions(config: any) {
    uni.showLoading({
        title: '请稍后...'
    })
    const { share_title, share_image, share_content, shop_name, share_page } =
        config
    const path = await generateSharePath(share_page == 2)
    uni.hideLoading()
    return {
        title: share_title || shop_name,
        link: path,
        img_url: share_image,
        desc: share_content
    }
}

export function initShareEvent() {
    // #ifdef H5
    document.addEventListener(
        'WeixinJSBridgeReady',
        async () => {
            const appStore = useAppStore()
            //@ts-ignore
            WeixinJSBridge.on('menu:share:appmessage', async function () {
                const options = await createShareOptions({
                    ...appStore.getShareConfig,
                    ...appStore.getWebsiteConfig
                })
                //@ts-ignore
                WeixinJSBridge.invoke('sendAppMessage', options)
            })
            //@ts-ignore
            WeixinJSBridge.on('menu:share:timeline', async function () {
                const options = await createShareOptions({
                    ...appStore.getShareConfig,
                    ...appStore.getWebsiteConfig
                })
                //@ts-ignore
                WeixinJSBridge.invoke('shareTimeline', options)
            })
        },
        false
    )
    // #endif
}

export async function generateSharePath(isHome = false) {
    const appStore = useAppStore()
    const route = router.currentRoute.value
    let origin = ''
    //#ifdef H5
    origin = `${window.location.origin}/mobile`
    //#endif
    //#ifdef APP-PLUS
    origin = `${appConfig.baseUrl}mobile`
    //#endif

    const config = {
        path: isHome ? '/pages/index/index' : route.path,
        query: isHome ? {} : route.query
    }
    const path = `${origin}${config.path}`
    const options: any = config.query
    try {
        const { share_id } = await getShareId()
        if (share_id) {
            options.share_id = share_id
        }
    } catch (error) {}
    return `${path}${paramsToStr(options)}`
}

export async function useSharedId() {
    const options = uni.getEnterOptionsSync()
    const share_id = options.query.share_id

    if (share_id) {
        await shareClick({
            share_id
        })
    }
}
