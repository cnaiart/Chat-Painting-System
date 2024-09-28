<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="h-[100vh] bg-white">
        <view class="text-[28rpx] text-[#999999] p-[30rpx]">反馈类型</view>
        <view class="px-[30rpx]">
            <u-radio-group v-model="formData.type">
                <u-radio
                    v-for="(item, index) in items"
                    :key="index"
                    :name="item.value"
                    class="mr-[40rpx]"
                >
                    {{ item.name }}
                </u-radio>
            </u-radio-group>
        </view>

        <view class="text-[28rpx] text-[#999999] p-[30rpx]">反馈内容</view>
        <view class="p-[20rpx] mx-[30rpx] container-text">
            <u-input
                v-model="formData.content"
                type="textarea"
                placeholder="请输入反馈内容"
                class="container-text"
            />
        </view>
        <view class="text-[28rpx] text-[#999999] p-[30rpx]">联系方式</view>
        <view class="px-[30rpx]">
            <input
                v-model="formData.mobile"
                placeholder="请输入联系方式"
                class="p-[20rpx] container-text"
            />
        </view>
        <view class="mx-[30rpx] mt-[100rpx]">
            <u-button
                type="primary"
                shape="circle"
                :loading="isLock"
                @click="lockFn"
            >
                立即提交
            </u-button>
        </view>
        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
    </view>
</template>
<script setup lang="ts">
import { feedbackPost } from '@/api/user'
import { useLockFn } from '@/hooks/useLockFn'
import { reactive } from 'vue'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
const formData = reactive({
    content: '',
    type: 1,
    mobile: ''
})
const items = [
    {
        value: 1,
        name: '故障'
    },
    {
        value: 2,
        name: '建议'
    },
    {
        value: 3,
        name: '投诉'
    }
]
const { isLock, lockFn } = useLockFn(async () => {
    await feedbackPost(formData)
})
</script>
<style lang="scss" scoped>
.container-text {
    background-color: #f8f8f8;
    border-radius: 7rpx;
}
</style>
