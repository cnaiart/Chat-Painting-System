<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="bg-white m-[30rpx] p-[40rpx] rounded-lg flex justify-between">
        <view class="flex flex-col justify-center items-center w-1/2">
            <view class="font-medium text-[40rpx]">0</view>
            <view class="text-[#999999]">推广人数</view>
        </view>
        <view class="flex flex-col justify-center items-center w-1/2">
            <view class="font-medium text-[40rpx]">6</view>
            <view class="text-[#999999]">累计获得条数</view>
        </view>
    </view>
    <view class="font-medium text-[30rpx] my-[40rpx] mx-[30rpx]">推广列表</view>
    <view class="bg-white m-[30rpx] p-[30rpx] rounded-lg flex justify-between">
        <view class="flex">
            <u-image
                width="80"
                height="80"
                src="@/static/images/user/default_avatar.png"
                alt=""
            />
            <view class="flex flex-col justify-between ml-[20rpx]">
                <view class="font-medium"> 微信用户</view>
                <view> 手机号码：13800138000</view>
                <view> 注册时间：2022-11-08 09:27:16</view>
            </view>
        </view>
        <view class="text-[#FF4747] text-[28rpx]">+2条</view>
    </view>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>
<script setup lang="ts">
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
</script>