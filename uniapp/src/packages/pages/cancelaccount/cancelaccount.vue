<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="p-[40rpx]">
        <view class="flex flex-col bg-white w-full p-[40rpx] rounded-lg">
            <image
                src="@/packages/static/images/cancel.png"
                class="w-[60rpx] h-[60rpx] mx-[auto]"
            ></image>
            <view class="font-medium text-[32rpx] text-center mt-[30rpx]">
                注销账号将永久失效且不可恢复，并且放弃以下权益资产和服务
            </view>
            <view class="mt-[80rpx] text-[#999999]">
                1、账号将无法登录；<br />
                2、您的个人相关数据将会被清空；设置的头像、昵称、收藏等...<br />
                3、账号一旦注销之后，无法使用平台的任何功能；<br />
                4、若您的账号开通过会员或者余额还剩余次数，将会被清空<br />
            </view>
            <view class="mt-[120rpx]">注销原因</view>
            <view class="bg-page p-[10rpx] mt-[20rpx]">
                <u-input
                    v-model="cancelledRemark"
                    type="textarea"
                    :height="200"
                    auto-height
                />
            </view>
        </view>
    </view>
    <u-button
        type="primary"
        :custom-style="{
            width: '700rpx',
            height: '100rpx',
            borderRadius: '200rpx',
            color: 'black'
        }"
        @click="handlecancel"
    >
        确认注销
    </u-button>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { cancelled } from '@/api/user'
import { useRouter, useRoute } from 'uniapp-router-next'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
// import { useRouter, useRoute } from 'uniapp-router-next-zm'

const router = useRouter()
const cancelledRemark = ref('')

const handlecancel = async () => {
    const res = await uni.showModal({
        title: '确定注销该账号吗？',
        confirmText: '注销',
        confirmColor: '#FF2C3C'
    })
    if (res.confirm) {
        await cancelled({
            cancelled_remark: cancelledRemark.value
        })
        router.reLaunch('/pages/index/index')
    }
}
</script>
