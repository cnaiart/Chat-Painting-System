<template>
    <view
        class="user-info flex px-[50rpx] justify-between pt-[50rpx] pb-[140rpx]"
    >
        <view class="flex items-center">
            <u-image
                width="120"
                height="120"
                :src="userInfo.avatar"
                borderRadius="50%"
            />
            <view class="text-btn-text ml-[20rpx]">
                <view class="text-2xl">{{ userInfo.nickname }}</view>
                <view class="text-xs mt-[18rpx]">
                    我的邀请人：{{ userInfo.first_leader_nickname || '系统' }}
                </view>
            </view>
        </view>
    </view>
    <view class="m-[20rpx] p-[20rpx] bg-white rounded-lg mt-[-100rpx]">
        <view class="flex justify-between col-start py-[10rpx]">
            <view>
                <view class="text-muted">可提现收入(元)</view>
                <view class="text-[40rpx] font-medium mt-[10rpx]">
                    {{ userInfo.user_money }}
                </view>
            </view>
            <view>
                <!-- <router-navigate
                    to="/packages/pages/withdraw/withdraw"
                > -->
                <u-button
                    type="primary"
                    shape="circle"
                    size="medium"
                    :customStyle="{
                        padding: '0 30rpx',
                        height: '58rpx'
                    }"
                    @click="toWithDraw"
                >
                    去提现
                </u-button>
                <!-- </router-navigate> -->
            </view>
        </view>
        <view class="mt-[20rpx] flex justify-between">
            <view class="flex flex-col justify-between items-center">
                <view class="text-muted">今日收入(元)</view>
                <view class="text-[36rpx] font-medium mt-[10rpx]">
                    {{ userInfo.today_money }}
                </view>
            </view>
            <view class="flex flex-col justify-between items-center">
                <view class="text-muted">已提现(元)</view>
                <view class="text-[36rpx] font-medium mt-[10rpx]">
                    {{ userInfo.withdrawn_money }}
                </view>
            </view>
            <view class="flex flex-col justify-between items-center">
                <view class="text-muted">累计收入(元)</view>
                <view class="text-[36rpx] font-medium mt-[10rpx]">
                    {{ userInfo.total_user_money }}
                </view>
            </view>
        </view>
    </view>
    <view class="m-[20rpx] pt-[30rpx] pb-[40rpx] bg-white rounded-lg">
        <view class="flex justify-between">
            <view v-for="(item, index) in navLists" :key="index" class="w-1/4">
                <router-navigate :to="item.link">
                    <view class="flex flex-col justify-center items-center">
                        <u-image
                            width="88"
                            height="88"
                            :src="item.icon"
                            alt=""
                        />
                        <view class="mt-[10rpx] text-base text-main">{{
                            item.name
                        }}</view>
                    </view>
                </router-navigate>
            </view>
        </view>
    </view>
    <view class="m-[20rpx] px-[20rpx] bg-white rounded-lg">
        <view class="py-[24rpx]">
            <view class="flex justify-between">
                <text class="font-medium text-[30rpx]"> 收益明细 </text>
                <router-navigate
                    to="/packages/pages/earnings_detail/earnings_detail"
                >
                    <text class="text-muted">查看更多</text>
                    <u-icon name="arrow-right" class="text-muted" />
                </router-navigate>
            </view>
        </view>

        <view
            class="py-[20rpx] border-t border-solid border-light border-0"
            v-for="(item, index) in accountLog"
            :key="index"
        >
            <view class="flex justify-between">
                <view>
                    <text class="text-[30rpx]">
                        {{ item.change_type_desc }}
                    </text>
                    <view class="text-muted text-[24rpx]">
                        {{ item.create_time }}
                    </view>
                </view>
                <view class="flex items-center">
                    <text
                        class="text-[30rpx]"
                        :class="[item.action == 1 ? 'text-error' : 'text-main']"
                    >
                        {{ item.action == 1 ? '+' : '-'
                        }}{{ item.change_amount }}
                    </text>
                </view>
            </view>
        </view>
    </view>
</template>
<script setup lang="ts">
import router from '@/router'

const props = defineProps<{
    userInfo: Record<string, any>
    accountLog: any[]
    navLists: any[]
    withdrawConfig: any
}>()

//去提现
const toWithDraw = () => {
    if (props.withdrawConfig.type.length == 0) {
        uni.$u.toast('后台未设置提现方式！')
        return
    }
    // uni.navigateTo({ url: '/packages/pages/withdraw/withdraw' })
    router.navigateTo('/packages/pages/withdraw/withdraw')
}
</script>

<style lang="scss">
.user-info {
    background: url('../../../../static/images/user/user_bg.png'),
        linear-gradient(90deg, $u-type-primary, $u-minor-color);
    background-position: bottom;
    background-size: 100% auto;
}
</style>
