<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="bg-white h-[100vh] p-[30rpx]">
        <view class="as-us flex flex-1 flex-col items-center">
            <image
                :src="appStore.getWebsiteConfig.shop_logo"
                mode=""
                class="img"
            ></image>
            <view class="text-[32rpx] font-medium my-[30rpx]">
                {{ appStore.getWebsiteConfig.shop_name }}
            </view>
            <view class="text-content">
                版本号：v{{ appStore.config.version }}
            </view>
        </view>
        <view class="mt-[90rpx]">
            <router-navigate
                to="/packages/pages/agreement/agreement?type=service"
            >
                <view class="flex justify-between py-[28rpx] container">
                    <text class="text-[28rpx]">用户协议</text>
                    <u-icon name="arrow-right" class="text-right" />
                </view>
            </router-navigate>
            <router-navigate
                to="/packages/pages/agreement/agreement?type=privacy"
            >
                <view class="flex justify-between py-[30rpx] container">
                    <text class="text-[28rpx]">隐私政策</text>
                    <u-icon name="arrow-right" />
                </view>
            </router-navigate>
        </view>
    </view>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
const appStore = useAppStore()
</script>

<style lang="scss" scoped>
.as-us {
    .img {
        width: 160rpx;
        height: 160rpx;
        border-radius: 20rpx;
        margin-top: 96rpx;
    }
}
.container {
    border-bottom: 1px solid $u-light-color;
}
</style>
