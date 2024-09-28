<template>
    <view class="bg-white h-[100vh] container">
        <view class="text-[40rpx] font-medium text-center py-[80rpx]">
            申请成为分销商
        </view>
        <view class="px-[30rpx]">
            <input
                type="text"
                placeholder="请输入姓名"
                class="h-[78rpx] p-[10rpx]"
            />
        </view>
        <view class="mt-[40rpx] px-[30rpx]">
            <input
                type="number"
                placeholder="请输入手机号"
                class="h-[78rpx] p-[10rpx]"
            />
        </view>
        <view class="my-[40rpx] px-[30rpx] text-[#999999]">
            <label>
                <checkbox
                    value="cb"
                    checked="true"
                    style="transform: scale(0.7)"
                    color="#FFC94D"
                />
            </label>
            <text>阅读并同意</text>
            <text class="container-user">《用户分销协议》</text>
        </view>
        <view class="px-[30rpx]">
            <button class="container-btn rounded-[50rpx]">立即申请</button>
        </view>
    </view>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>
<style lang="scss" scoped>
.container {
    &-btn {
        background: $u-type-primary;
    }
    &-user {
        color: $u-type-primary;
    }
}

input {
    background-color: rgba(136, 136, 136, 0.1);
}
</style>
<script setup lang='ts'>
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
</script>