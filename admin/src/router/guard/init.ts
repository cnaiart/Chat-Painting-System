import useAppStore from '@/stores/modules/app'
import type { Router } from 'vue-router'

export default function createInitGuard(router: Router) {
    router.beforeEach(async () => {
        const appStore = useAppStore()
        if (Object.keys(appStore.config).length == 0) {
            // 获取配置
            const data: any = await appStore.getConfig()

            if (!data.install) {
                window.location.replace('/install/install.php')
                return
            }
            // 设置网站logo
            let favicon: HTMLLinkElement = document.querySelector('link[rel="icon"]')!
            if (favicon) {
                favicon.href = data.web_favicon
            }
            favicon = document.createElement('link')
            favicon.rel = 'icon'
            favicon.href = data.web_favicon
            document.head.appendChild(favicon)
        }
    })
}
