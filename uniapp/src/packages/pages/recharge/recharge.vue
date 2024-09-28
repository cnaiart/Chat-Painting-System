<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <page-status v-if="paySetup.is_open" :status="status">
        <view class="task-center" v-if="rechargeData.status">
            <view class="pb-[20rpx]">
                <view
                    class="recharge overflow-hidden"
                    v-if="rechargeData.lists?.length"
                >
                    <view class="tasks-title">
                        <view class="font-medium text-xl">充值套餐</view>
                        <view class="ml-[14rpx] text-muted text-sm">
                            VIP期间对话不消耗次数，次数永不过期
                        </view>
                    </view>
                    <view
                        class="recharge-template flex flex-wrap px-[30rpx] mx-[-10rpx]"
                    >
                        <view
                            class="w-1/2 mb-[20rpx]"
                            v-for="item in rechargeData.lists"
                            :key="item.id"
                            @click="currentId = item.id"
                        >
                            <view
                                class="flex flex-col rounded-[16rpx] mx-[10rpx] h-full border-[1px] border-solid border-light overflow-hidden relative"
                            >
                                <view class="pt-[60%] relative w-full h-0">
                                    <view
                                        class="absolute left-0 right-0 bottom-0 top-0"
                                    >
                                        <u-image
                                            width="100%"
                                            height="100%"
                                            :src="item.image"
                                            mode="aspectFill"
                                        />
                                    </view>
                                </view>
                                <view
                                    class="absolute top-[-1px] left-[-1px] bg-[#FF3232] text-white text-sm rounded-[8rpx] px-[20rpx] py-[8rpx] max-w-[100%]"
                                    v-if="item.tag"
                                >
                                    {{ item.tag }}
                                </view>
                                <view
                                    class="p-[20rpx] flex-1 min-h-0 flex flex-col"
                                >
                                    <view>
                                        <price
                                            :content="item.sell_price"
                                            mainSize="40rpx"
                                            minorSize="28rpx"
                                            color="#FF2C3C"
                                            :fontWeight="500"
                                        ></price>
                                    </view>
                                    <view
                                        class="text-xl font-medium mt-[12rpx] line-clamp-1"
                                    >
                                        {{ item.name }}
                                    </view>
                                    <view
                                        class="text-sm text-muted mt-[20rpx] line-clamp-3"
                                    >
                                        {{ item.describe }}
                                    </view>
                                    <view class="mt-auto">
                                        <view
                                            class="flex flex-wrap justify-between mt-[20rpx] text-sm"
                                        >
                                            <view class="mr-[20rpx] flex-none">
                                                对话次数: {{ item.number }}
                                            </view>
                                            <view
                                                class="text-primary"
                                                v-if="
                                                    item.is_give &&
                                                    item.give_number
                                                "
                                            >
                                                +{{ item.give_number }}
                                            </view>
                                        </view>
                                        <view
                                            class="flex flex-wrap justify-between mt-[12rpx] text-sm"
                                        >
                                            <view class="mr-[10px] flex-none">
                                                绘画次数:
                                                {{ item.draw_number }}
                                            </view>
                                            <view
                                                class="text-primary"
                                                v-if="
                                                    item.is_give &&
                                                    item.give_draw_number
                                                "
                                            >
                                                +{{ item.give_draw_number }}
                                            </view>
                                        </view>
                                        <view
                                            class="pt-[30rpx] pb-[10rpx]"
                                            v-if="paySetup.is_open"
                                        >
                                            <u-button
                                                :custom-style="{
                                                    width: '100%',
                                                    height: '68rpx',
                                                    fontSize: '28rpx'
                                                }"
                                                type="primary"
                                                shape="circle"
                                                @click="rechargeLock(item.id)"
                                            >
                                                立即充值
                                            </u-button>
                                        </view>
                                    </view>
                                </view>
                            </view>
                        </view>
                    </view>
                </view>
            </view>
        </view>
        <view v-else class="py-[400rpx]">
            <u-empty text="功能未开启"></u-empty>
        </view>
        <template #error>
            <u-empty text="加载出错～"></u-empty>
        </template>
        <payment
            v-model:show="payState.showPay"
            :order-id="payState.orderId"
            v-model:showCheck="payState.showCheck"
            :from="payState.from"
            redirect="/packages/pages/recharge/recharge"
            :order-amount="payState.orderAmount"
            @success="handlePaySuccess"
        />
        <u-popup
            v-model="payState.showPaySuccess"
            :safe-area-inset-bottom="true"
            :mask-close-able="false"
            border-radius="14"
            :z-index="899"
            round
            mode="center"
            width="600"
        >
            <view class="pt-[20rpx]">
                <view class="px-[30rpx] py-[40rpx]">
                    <view class="text-success text-center">
                        <u-icon name="checkmark-circle-fill" size="100" />
                    </view>
                    <view class="text-xl font-medium mt-[20rpx] text-center">
                        支付成功
                    </view>
                    <view class="flex mt-[60rpx]">
                        <view class="flex-1">
                            <view
                                class="h-[72rpx] leading-[72rpx] rounded-full bg-page text-center"
                                @click="payState.showPaySuccess = false"
                            >
                                <text>继续充值</text>
                            </view>
                        </view>
                        <view class="flex-1 ml-[20rpx]">
                            <router-navigate
                                class="h-[72rpx] leading-[72rpx] rounded-full bg-primary text-center text-btn-text"
                                to="/pages/index/index"
                                nav-type="reLaunch"
                            >
                                <text>返回首页</text>
                            </router-navigate>
                        </view>
                    </view>
                </view>
            </view>
        </u-popup>
    </page-status>
    <view v-else class="h-full flex justify-center items-center"> </view>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->

    <tabbar />
</template>

<script setup lang="ts">
import { getRechargeConfig, recharge } from '@/api/recharge'
import { getIosPayConfig } from '@/api/pay'

import { PageStatusEnum } from '@/enums/appEnums'
import { useLockFn } from '@/hooks/useLockFn'

import { onLoad, onPullDownRefresh, onShow } from '@dcloudio/uni-app'

import { reactive, ref } from 'vue'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
const status = ref(PageStatusEnum.LOADING)

const rechargeData = ref<any>({
    lists: []
})
const system = uni.getSystemInfoSync()
const paySetup = ref({
    is_open: 1,
    tips: '立即支付'
})
const currentId = ref()

const payState = reactive({
    orderId: '',
    from: 'recharge',
    showPay: false,
    orderAmount: '',
    showCheck: false,
    showPaySuccess: false
})

const { isLock, lockFn: rechargeLock } = useLockFn(
    async (currentId: number) => {
        uni.showLoading({
            mask: true,
            title: '请稍后...'
        })
        try {
            const data = await recharge({
                recharge_package_id: currentId
            })
            payState.orderId = data.order_id
            payState.showPay = true
            payState.orderAmount = data.order_amount
        } catch (error) {
        } finally {
            uni.hideLoading()
        }
    }
)

const handlePaySuccess = async () => {
    payState.showPay = false
    payState.showPaySuccess = true
}

const getRechargeConfigData = async () => {
    rechargeData.value = await getRechargeConfig()
    currentId.value =
        rechargeData.value.lists.find((item: any) => item.is_default)?.id || ''
}

const getData = async () => {
    try {
        await Promise.all([getRechargeConfigData()])
        status.value = PageStatusEnum.NORMAL
    } catch (error) {
        status.value = PageStatusEnum.ERROR
    }
}

// 获取支付配置
const getPaySetup = async () => {
    try {
        paySetup.value = await getIosPayConfig()
        console.log(paySetup.value)
        if (paySetup.value.is_open == 0) {
            uni.showModal({
                title: '提示',
                content: paySetup.value.tips,
                showCancel: false,
                success: () => uni.navigateBack()
            })
        }
    } catch (error) {
        console.log('获取支付设置错误=>', error)
    }
}

onShow(async () => {
    getData()

    // #ifdef MP|| APP-PLUS
    if (system.system.indexOf('iOS') !== -1) {
        await getPaySetup()
    }
    // #endif
})

onLoad((options: any) => {
    // h5支付用于弹起手动确认支付弹窗
    if ((options?.id || options['amp;id']) && (options.from || options['amp;from'])) {
        payState.orderId = options?.id || options['amp;id']
        payState.from = options.from || options['amp;from']
    }

    if (options?.checkPay) {
        payState.showCheck = true
    }
})

onPullDownRefresh(async () => {
    try {
        await getData()
    } catch (error) {}
    uni.stopPullDownRefresh()
})
</script>

<style lang="scss">
page {
    background-color: #fff;
}
.task-center {
    .tasks-title {
        padding: 0 30rpx;
        height: 88rpx;
        display: flex;
        align-items: center;
    }
    .tasks-content {
        padding-bottom: 20rpx;
        .tasks-item {
            &:not(:last-of-type) {
                border-bottom: 1px solid $u-border-color;
                margin-bottom: 10rpx;
            }
        }
    }
}
</style>
