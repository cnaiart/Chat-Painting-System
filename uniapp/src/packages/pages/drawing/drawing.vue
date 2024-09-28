<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view
        v-if="!appStore.getDrawConfig.is_open"
        class="w-full h-full bg-white rounded-[6px] flex items-center justify-center"
    >
        <u-empty text="绘画功能未开启" mode="favor"></u-empty>
        <tabbar />
    </view>
    <view v-else class="drawing-container">
        <view
            class="drawing-content"
            :class="{
                'safe-area-inset-bottom': !showTabbar
            }"
        >
            <view class="h-full" v-show="pageIndex == 0">
                <DrawingControl class="h-full"></DrawingControl>
            </view>
            <view class="h-full" v-show="pageIndex == 1">
                <DrawingRecord class="h-full"></DrawingRecord>
            </view>
        </view>
        <tabbar />
        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
    </view>
</template>

<script lang="ts" setup>
import DrawingRecord from './component/drawing-record/index.vue'
import DrawingControl from './component/drawing-control/index.vue'

import { useIndexEffect } from './hooks/useIndexEffect'
const { pageIndex } = useIndexEffect()

import { useRoute } from 'uniapp-router-next'
const route = useRoute()

import { useAppStore } from '@/stores/app'
import { computed } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
const appStore = useAppStore()

const tabbarList = computed(() => {
    return (
        appStore.getTabbarConfig?.list
            ?.filter((item: any) => item.is_show == '1')
            ?.map((item: any) => {
                return {
                    pagePath: item.link.path
                }
            }) || []
    )
})

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

onLoad(({ type }) => {
    setTimeout(() => {
        if (type == 1) {
            pageIndex.value = 1
        }
    }, 500)
})
</script>

<style lang="scss">
page {
    height: 100%;
}

.drawing-container {
    display: flex;
    flex-direction: column;
    flex: 1;
    height: 100%;
    @apply bg-white;
    .drawing-content {
        flex: 1;
        min-height: 0;
    }
}
</style>

<style scoped>
.safe-area-inset-bottom {
    padding-bottom: calc(constant(safe-area-inset-bottom));
    padding-bottom: calc(env(safe-area-inset-bottom));
}
</style>
