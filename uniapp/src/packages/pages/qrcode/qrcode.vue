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
        v-if="config?.is_open == -1"
        class="w-full h-full bg-white rounded-[6px] flex items-center justify-center"
    >
        <u-loading :size="60" mode="flower" />
        <tabbar />
    </view>
    <view
        v-if="config?.is_open == 0"
        class="w-full h-full bg-white rounded-[6px] flex items-center justify-center"
    >
        <u-empty text="艺术二维码功能未开启" mode="favor"></u-empty>
        <tabbar />
    </view>
    <view v-else class="qrcode-container">
        <view
            class="drawing-content"
            :class="{
                'safe-area-inset-bottom': !showTabbar
            }"
        >
            <view class="h-full" v-show="pageIndex == 0">
                <QrcodeControl class="h-full"></QrcodeControl>
            </view>
            <view class="h-full" v-show="pageIndex == 1">
                <QrcodeRecord class="h-full"></QrcodeRecord>
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
import QrcodeRecord from './component/qrcode-record/index.vue'
import QrcodeControl from './component/qrcode-control/index.vue'

import { useRoute } from 'uniapp-router-next'
import { useAppStore } from '@/stores/app'
const appStore = useAppStore()
const route = useRoute()

import { useIndexEffect } from './hooks/useIndexEffect'
const { pageIndex, qrcodeForm, consumptionCount } = useIndexEffect()

import useConfigEffect from './hooks/useConfigEffect'
import { computed, onMounted } from 'vue'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
const { config, getConfig } = useConfigEffect({
    dataTransform(data: any) {
        qrcodeForm.model = data.model
        consumptionCount.value = data.balance
    }
})
onMounted(() => getConfig())

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
</script>

<style lang="scss">
page {
    height: 100%;
}

.qrcode-container {
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
