<script lang="ts" setup>
import { ID_INJECTION_KEY, ElConfigProvider } from 'element-plus'
import zhCn from 'element-plus/es/locale/lang/zh-cn'
import { useAppStore } from './stores/app'
import { addVisit } from './api/shop'
import { useThemeStore } from './stores/theme'
const router = useRouter()
provide(ID_INJECTION_KEY, {
    prefix: 100,
    current: 0
})
const config = {
    locale: zhCn
}
const appStore = useAppStore()
const themeStore = useThemeStore()
themeStore.getTheme()
const { pc_title, pc_ico, pc_keywords, pc_desc } = appStore.getWebsiteConfig
useHead({
    title: pc_title,
    meta: [
        { name: 'description', content: pc_desc },
        { name: 'keywords', content: pc_keywords }
    ],
    link: [
        {
            rel: 'icon',
            href: pc_ico
        }
    ]
})

const { status, page_status, page_url } = appStore.getPcPageConfig
// 重定向到安装路径，由于将跟路径/pc/ => 改为 /
if (!appStore.getIsInstall) {
    window.location.replace('/install/install.php')
} else {
    if (status == 0) {
        if (page_status == 1) {
            location.href = page_url
        }
        if (page_status == 0) {
            router.replace('/empty')
        }
    }
}

addVisit()
</script>
<template>
    <ElConfigProvider v-bind="config">
        <NuxtLayout>
            <NuxtLoadingIndicator color="#4a5dff" :height="2" />
            <NuxtPage />
        </NuxtLayout>
    </ElConfigProvider>
</template>
