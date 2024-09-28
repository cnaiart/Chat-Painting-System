<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="app">
        <view class="header">
            <view class="title">全部应用</view>
        </view>
        <view class="main">
            <view
                class="menu-item text-center"
                v-for="(item, index) in showList"
                :key="index"
                @click="onJump(item)"
            >
                <u-image
                    :src="getImageUrl(item.image)"
                    width="300"
                    height="300"
                    border-radius="16"
                ></u-image>
                <view class="mt-[16rpx] font-medium text-xl">
                    {{ item.title }}
                </view>
                <view class="mt-[12rpx] text-sm text-[#999999] truncate">
                    {{ item.desc }}
                </view>
            </view>
        </view>

        <tabbar />
    </view>
</template>
<script setup lang="ts">
import { getDecorate } from '@/api/shop'
import { computed, ref } from 'vue'
import { useAppStore } from '@/stores/app'
import { navigateTo } from '@/utils/util'
import { onPullDownRefresh, onShow } from '@dcloudio/uni-app'

const { getImageUrl, getConfig } = useAppStore()
const decorate = ref<any[]>([])
const getData = async () => {
    const data = await getDecorate({ id: 7 })
    decorate.value = JSON.parse(data.data)
}
const showList = computed(() => {
    return (
        decorate?.value[0]?.content.data.filter(
            (item: any) => item.is_show == 1
        ) || []
    )
})
getData()

// 原生菜单列表
const nativeTabList = [
    '/pages/index/index',
    '/pages/ai_creation/ai_creation',
    '/pages/skills/skills',
    '/pages/app/app',
    '/pages/user/user'
]

const onJump = (row: any) => {
    const navigateType = nativeTabList.includes(row.link.path)
        ? 'switchTab'
        : 'navigateTo'
    navigateTo(row.link, false, navigateType)
}

onShow(() => {
    getConfig()
})

onPullDownRefresh(async () => {
    await getData()
    uni.stopPullDownRefresh()
})
</script>
<style lang="scss" scoped>
.app {
    min-height: 100vh;
    background: linear-gradient(to right, #e8fdf8, #ffffff, #e5f7ff);
}
.header {
    padding: 30rpx;
    padding-bottom: 0;
    .title {
        display: inline-block;
        font-size: 40rpx;
        font-weight: bold;
        background: -webkit-linear-gradient(90deg, #19e8b7, #00abff);
        background: -moz-linear-gradient(90deg, #19e8b7, #00abff);
        background: -o-linear-gradient(90deg, #19e8b7, #00abff);
        background: linear-gradient(90deg, #19e8b7, #00abff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        -moz-background-clip: text;
        -moz-text-fill-color: transparent;
        -o-background-clip: text;
        -o-text-fill-color: transparent;
        background-clip: text;
        text-fill-color: transparent;
    }
}
.main {
    flex: 1;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    padding: 24rpx;

    .menu-item {
        display: inline-block;
        margin-bottom: 20rpx;
        padding: 20rpx;
        width: 340rpx;
        //height: 380rpx;
        border-radius: 16rpx;
        background-color: #ffffff;
        box-shadow: 0 3rpx 10rpx #e3e3e3;
    }
}
</style>
