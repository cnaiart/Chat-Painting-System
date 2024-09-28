<template>
    <u-tabbar
        v-show="showTabbar"
        v-bind="tabbarStyle"
        :list="tabbarList"
        @change="handleChange"
        :hide-tab-bar="true"
    ></u-tabbar>
</template>

<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import { useNavigationBarTitleStore } from '@/stores/navigationBarTitle'
import { navigateTo } from '@/utils/util'
import { useRoute } from 'uniapp-router-next'
import { watch } from 'vue'
import { computed, ref } from 'vue'
const appStore = useAppStore()
const route = useRoute()
const navigationBarTitleStore = useNavigationBarTitleStore()
const tabbarList = computed(() => {
    return (
        appStore.getTabbarConfig?.list
            ?.filter((item: any) => item.is_show == '1')
            ?.map((item: any) => {
                return {
                    iconPath: appStore.getImageUrl(item.unselected),
                    selectedIconPath: appStore.getImageUrl(item.selected),
                    text: item.name,
                    link: item.link,
                    pagePath: item.link.path
                }
            }) || []
    )
})

// 原生菜单列表
const nativeTabList = [
    '/pages/index/index',
    '/pages/ai_creation/ai_creation',
    '/pages/skills/skills',
    '/pages/app/app',
    '/pages/user/user'
]

const getCurrentIndex = () => {
    const current = tabbarList.value.findIndex((item: any) => {
        return item.pagePath === route.path
    })
    return route.path == '/' ? 0 : current
}
const showTabbar = computed(() => {
    const current = getCurrentIndex()
    return current >= 0
})

const tabbarStyle = computed(() => ({
    activeColor: appStore.getTabbarConfig?.style?.selected_color,
    inactiveColor: appStore.getTabbarConfig?.style?.default_color
}))
const handleChange = (index: number) => {
    const selectTab = tabbarList.value[index]
    const navigateType = nativeTabList.includes(selectTab.pagePath)
        ? 'switchTab'
        : 'reLaunch'
    navigateTo(selectTab.link, false, navigateType)
}

watch(
    tabbarList,
    (value) => {
        const current = getCurrentIndex()

        if (current >= 0 && value.length) {
            const currentTab = value[current]
            navigationBarTitleStore.add({
                path: currentTab.pagePath,
                title: currentTab.text
            })
            navigationBarTitleStore.setTitle()
        }
    },
    {
        immediate: true
    }
)
</script>
