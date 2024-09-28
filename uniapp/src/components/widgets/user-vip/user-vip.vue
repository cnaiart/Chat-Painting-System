<template>
    <view class="user-vip mx-[20rpx] rounded-lg mb-[20rpx]">
        <view class="p-[30rpx] flex" v-if="appStore.getIsShowVip">
            <u-image
                width="80"
                height="80"
                :src="getImageUrl(content.icon)"
                alt=""
            />
            <view class="flex justify-between w-full">
                <view
                    v-if="!user.member_expired"
                    class="ml-[20rpx] flex flex-col justify-between"
                >
                    <view class="text-[32rpx] text-[#55300F] font-medium">
                        {{ user.is_member ? '您已成为VIP会员' : content.title }}
                    </view>
                    <view class="text-[24rpx] text-[#6A3D15]">
                        {{
                            user.is_member
                                ? user.member_perpetual
                                    ? '有效期至：永久'
                                    : `有效期至：${user.member_end_time}`
                                : content.sub_title
                        }}
                    </view>
                </view>
                <view v-else class="ml-[20rpx] flex flex-col justify-between">
                    <view class="text-[32rpx] text-[#55300F] font-medium">
                        会员已过期
                    </view>
                    <view class="text-[24rpx] text-[#6A3D15]">
                        过期时间: {{ user.member_end_time }}
                    </view>
                </view>
                <view class="flex flex-col justify-center" v-if="isLogin">
                    <u-button
                        v-if="appStore.getIsShowVip && !user.member_perpetual"
                        shape="circle"
                        size="medium"
                        :customStyle="{
                            padding: '0 24rpx',
                            height: '56rpx',
                            background: '#333333',
                            color: '#F8C596'
                        }"
                        hover-class="none"
                        @click="navigateTo('/packages/pages/open_vip/open_vip')"
                    >
                        {{ user.is_member ? '立即续费' : content.btn }}
                    </u-button>
                </view>
            </view>
        </view>
        <router-navigate to="/packages/pages/recharge/recharge">
            <view class="bg-white rounded-lg flex p-[30rpx] pr-[24rpx]">
                <view class="flex-1">
                    <view>对话余额</view>
                    <view class="flex items-center mt-[20rpx]">
                        <text class="text-primary">
                            {{ user.balance || 0 }}条
                        </text>
                    </view>
                </view>
                <view class="flex-1" v-if="appStore.getDrawConfig.is_open">
                    <view>绘画余额</view>
                    <view class="flex items-center mt-[20rpx]">
                        <text class="text-primary">
                            {{ user.balance_draw || 0 }}次
                        </text>
                    </view>
                </view>
                <view class="text-muted flex">
                    <u-icon name="arrow-right" />
                </view>
            </view>
        </router-navigate>
    </view>
</template>
<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import { useRouter } from 'uniapp-router-next'
// import { useRouter } from 'uniapp-router-next-zm'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    },
    user: {
        type: Object,
        default: () => ({})
    },
    isLogin: {
        type: Boolean
    }
})
const router = useRouter()
const appStore = useAppStore()
const { getImageUrl } = useAppStore()
const navigateTo = (path: string) => {
    router.navigateTo(path)
    // console.log(path)
    // uni.switchTab({ url: path })
}
</script>

<style lang="scss">
.user-vip {
    background: linear-gradient(90deg, #ffe8cf 0%, #ffe8cf 20%, #e1ab7a 100%);
}
</style>
