<template>
    <view class="h-screen bg-white flex flex-col items-center justify-center">
        <view class="px-[120rpx] text-center">
            长按识别/下载图片，扫码关注公众号 开始与AI畅聊
        </view>
        <view class="flex mt-[20rpx]">
            <view class="mx-[10rpx]" v-for="item in 3" :key="item">
                <u-icon name="/static/images/icon/icon_hand.png" :size="50">
                </u-icon>
            </view>
        </view>
        <view class="mt-[50rpx]">
            <image
                class="w-[360rpx] h-[360rpx]"
                :src="appStore.config.official_qr_code"
                show-menu-by-longpress
            />
        </view>

        <tabbar />
        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
    </view>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const appStore = useAppStore()
</script>

<style></style>
