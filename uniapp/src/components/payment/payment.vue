<template>
    <u-popup
        v-model="showPay"
        mode="bottom"
        :safe-area-inset-bottom="safeArea"
        :mask-close-able="false"
        border-radius="14"
        closeable
        :z-index="899"
        @close="handleClose"
    >
        <view class>
            <page-status :status="popupStatus" :fixed="false">
                <template #error>
                    <u-empty
                        text="订单信息错误，无法查询到订单信息"
                        mode="order"
                    ></u-empty>
                </template>
                <template #default>
                    <view class="payment w-full pb-[20rpx]">
                        <view
                            class="header py-[50rpx] flex flex-col items-center"
                        >
                            <view class="text-[34rpx]">选择支付方式</view>
                        </view>
                        <view class="main min-h-[300rpx] mx-[20rpx]">
                            <view>
                                <view class="payway-lists">
                                    <u-radio-group
                                        v-model="payWay"
                                        class="w-full"
                                        :active-color="$theme.primaryColor"
                                    >
                                        <view
                                            class="p-[20rpx] flex items-center w-full payway-item"
                                            v-for="(
                                                item, index
                                            ) in payData.lists"
                                            :key="index"
                                            @click="selectPayWay(item.pay_way)"
                                        >
                                            <u-icon
                                                class="flex-none"
                                                :size="48"
                                                :name="item.icon"
                                            ></u-icon>
                                            <view class="mx-[16rpx] flex-1">
                                                <view
                                                    class="payway-item--name flex-1"
                                                >
                                                    {{ item.name }}
                                                </view>
                                                <view
                                                    class="text-muted text-xs"
                                                    >{{ item.extra }}</view
                                                >
                                            </view>

                                            <u-radio
                                                class="mr-[-20rpx]"
                                                :name="item.pay_way"
                                            >
                                            </u-radio>
                                        </view>
                                    </u-radio-group>
                                </view>
                            </view>
                        </view>

                        <view class="submit-btn p-[20rpx] mt-[50rpx]">
                            <u-button
                                @click="handlePay"
                                shape="circle"
                                type="primary"
                                :loading="isLock"
                            >
                                立即支付
                                <price
                                    v-if="orderAmount"
                                    :content="orderAmount"
                                    mainSize="34rpx"
                                    minorSize="34rpx"
                                    fontWeight="500"
                                    color="var(--color-btn-text)"
                                ></price>
                            </u-button>
                        </view>
                    </view>
                </template>
            </page-status>
        </view>
    </u-popup>
    <payment-check
        v-model:show="showCheckPay"
        :from="from"
        :order-id="orderId"
        @success="checkSuccess"
        @fail="checkFail"
    />
</template>

<script lang="ts" setup>
import { pay, PayWayEnum } from '@/utils/pay'
import { getPayWay, prepay } from '@/api/pay'
import { computed, onMounted, ref, watch } from 'vue'
import { useLockFn } from '@/hooks/useLockFn'
import { series } from '@/utils/util'
import { ClientEnum, PageStatusEnum, PayStatusEnum } from '@/enums/appEnums'
import { useUserStore } from '@/stores/user'
import { client, getClientString } from '@/utils/client'
import { useRouter } from 'uniapp-router-next'
// import { useRouter } from 'uniapp-router-next-zm'
// #ifdef H5
import wechatOa, { UrlScene } from '@/utils/wechat'
// #endif
import PaymentCheck from './check.vue'

/*
页面参数 orderId：订单id，from：订单来源
*/

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    showCheck: {
        type: Boolean
    },
    // 订单id
    orderId: {
        type: Number,
        required: true
    },
    //订单来源
    from: {
        type: String,
        required: true
    },
    //h5微信支付回跳路径，一般为拉起支付的页面路径
    redirect: {
        type: String
    },
    orderAmount: {
        type: Number
    },
    safeArea: {
        type: Boolean,
        required: true
    }
})

const emit = defineEmits([
    'update:showCheck',
    'update:show',
    'close',
    'success',
    'fail'
])
const router = useRouter()
const payWay = ref()

const popupStatus = ref(PageStatusEnum.LOADING)
const payData = ref<any>({
    lists: []
})

const showCheckPay = computed({
    get() {
        return props.showCheck
    },
    set(value) {
        emit('update:showCheck', value)
    }
})

const showPay = computed({
    get() {
        return props.show
    },
    set(value) {
        emit('update:show', value)
    }
})

const handleClose = () => {
    showPay.value = false
    emit('close')
}
const getPayData = async () => {
    popupStatus.value = PageStatusEnum.LOADING
    try {
        payData.value = await getPayWay({
            order_id: props.orderId,
            from: props.from
        })
        popupStatus.value = PageStatusEnum.NORMAL
        const checkPay =
            payData.value.lists.find((item: any) => item.is_default) ||
            payData.value.lists[0]
        payWay.value = checkPay?.pay_way
    } catch (error) {
        popupStatus.value = PageStatusEnum.ERROR
    }
}

const userStore = useUserStore()
const selectPayWay = (pay: number) => {
    payWay.value = pay
}
const checkIsBindWx = async () => {
    if (
        userStore.userInfo.is_auth == 0 &&
        [ClientEnum.OA_WEIXIN, ClientEnum.MP_WEIXIN].includes(client) &&
        payWay.value == PayWayEnum.WECHAT
    ) {
        switch (client) {
            case ClientEnum.OA_WEIXIN: {
                wechatOa.getUrl(UrlScene.BASE, 'snsapi_base', {
                    id: props.orderId,
                    from: props.from
                })
                return Promise.reject()
            }
            case ClientEnum.MP_WEIXIN: {
                const data = await uni.login()
                return data.code
            }
        }
    }
}

const payment = async (code: string | undefined) => {
    // 调用预支付
    try {
        uni.showLoading({
            title: '正在支付中'
        })
        const data = await prepay({
            device: getClientString(),
            order_id: props.orderId,
            from: props.from,
            pay_way: payWay.value,
            redirect: props.redirect,
            code
        })
        const res = await pay.payment(data.pay_way, data?.config || data?.payurl || data?.qrcode)
        return res
    } catch (error) {
        return Promise.reject(error)
    }
}
const { isLock, lockFn: handlePay } = useLockFn(async () => {
    try {
        const code = await checkIsBindWx()
        const res: PayStatusEnum = await payment(code)
        handlePayResult(res)
        uni.hideLoading()
    } catch (error) {
        uni.hideLoading()
        console.log(error)
    }
})

const handlePayResult = (status: PayStatusEnum) => {
    switch (status) {
        case PayStatusEnum.SUCCESS:
            emit('success')
            break
        case PayStatusEnum.FAIL:
            emit('fail')
            break
    }
}
const checkSuccess = () => {
    handlePayResult(PayStatusEnum.SUCCESS)
}

const checkFail = () => {
    showPay.value = true
    handlePayResult(PayStatusEnum.FAIL)
}

watch(
    () => props.show,
    async (value) => {
        if (value) {
            if (!props.orderId) {
                popupStatus.value = PageStatusEnum.ERROR
                return
            }
            await getPayData()
        }
    },
    {
        immediate: true
    }
)
onMounted(async () => {
    // #ifdef H5
    const options = wechatOa.getAuthData()
    if (options.code && options.scene === UrlScene.BASE) {
        payWay.value = PayWayEnum.WECHAT
        showPay.value = true
        try {
            const res: PayStatusEnum = await payment(options.code)
            handlePayResult(res)
            uni.hideLoading()
        } catch (error) {
            uni.hideLoading()
            console.log(error)
        } finally {
            wechatOa.setAuthData({})
        }
    }
    // #endif
})
</script>

<style lang="scss">
.payway-lists {
    .payway-item {
        border-bottom: 1px solid;
        @apply border-page;
    }
}
</style>
