<template>
    <u-popup
        class="pay-popup"
        v-model="showCheckPay"
        round
        mode="center"
        borderRadius="10"
        :maskCloseAble="false"
    >
        <view class="content bg-white w-[560rpx] p-[40rpx]">
            <view class="text-2xl font-medium text-center"> 支付确认 </view>
            <view class="pt-[30rpx] pb-[40rpx]">
                <view>
                    请在微信/支付宝内完成支付，如果您已支付成功，请点击`已完成支付`按钮
                </view>
            </view>
            <view class="flex">
                <view class="flex-1 mr-[20rpx]">
                    <u-button
                        shape="circle"
                        type="primary"
                        plain
                        size="medium"
                        hover-class="none"
                        :customStyle="{ width: '100%' }"
                        @click="queryPayResult(false)"
                    >
                        重新支付
                    </u-button>
                </view>
                <view class="flex-1">
                    <u-button
                        shape="circle"
                        type="primary"
                        size="medium"
                        hover-class="none"
                        :customStyle="{ width: '100%' }"
                        @click="queryPayResult()"
                    >
                        已完成支付
                    </u-button>
                </view>
            </view>
        </view>
    </u-popup>
</template>
<script setup lang="ts">
import { getPayResult } from '@/api/pay'
import { computed } from 'vue'

const props = defineProps({
    show: {
        type: Boolean,
        required: true
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
    }
})

const emit = defineEmits(['update:show', 'success', 'fail'])

const showCheckPay = computed({
    get() {
        return props.show
    },
    set(value) {
        emit('update:show', value)
    }
})
const queryPayResult = async (confirm = true) => {
    const res = await getPayResult({
        order_id: props.orderId,
        from: props.from
    })

    if (res.pay_status === 0) {
        if (confirm == true) {
            uni.$u.toast('您的订单还未支付，请重新支付')
        }
        emit('fail')
        // showPay.value = true
        // handlePayResult(PayStatusEnum.FAIL)
    } else {
        if (confirm == false) {
            uni.$u.toast('您的订单已经支付，请勿重新支付')
        }
        // handlePayResult(PayStatusEnum.SUCCESS)
        emit('success')
    }
    showCheckPay.value = false
}
</script>
