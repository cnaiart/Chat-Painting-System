<template>
    <ElDialog
        v-if="popShow"
        v-model="popShow"
        width="540px"
        title="立即支付"
        center
        @close="closePop"
    >
        <div class="px-[30px] py-[15px]">
            <ElForm label-width="70px">
                <!-- <ElFormItem label="充值条数" v-if="type == 'recharge'">
                    <div>{{ order.number }}条</div>
                </ElFormItem> -->
                <slot name="top" v-bind="order" />
                <ElFormItem label="支付金额">
                    <div>¥{{ order.sell_price }}</div>
                </ElFormItem>
                <ElFormItem label="实付金额">
                    <div class="text-[#FF4141]">¥{{ order.sell_price }}</div>
                </ElFormItem>

                <ElFormItem label="支付方式">
                    <div
                        v-for="(item, index) in payWayList.lists"
                        :key="index"
                        class="flex items-center w-[175px] px-[20px] py-[10px] mt-[10px] rounded-lg unactive mr-2"
                        :class="{
                            actived: selectedType == item.pay_way
                        }"
                        @click="handleSelect(item.pay_way)"
                    >
                        <ElImage
                            :src="item.icon"
                            class="h-[24px] w-[24px]"
                        ></ElImage>
                        <div class="ml-[10px]">{{ item.name }}</div>
                    </div>
                </ElFormItem>
            </ElForm>
            <div class="flex justify-center">
                <ElButton type="primary" @click="orderPay">确认支付</ElButton>
            </div>
        </div>
    </ElDialog>
    <WxPayCode ref="wxPayCodePopRef" @popClose="closePop"></WxPayCode>
    <AliPayCode ref="aliPayCodePopRef" @popClose="closePop"></AliPayCode>
</template>

<script setup lang="ts">
import { ElDialog, ElForm, ElFormItem, ElButton, ElImage } from 'element-plus'
import { getPayWayList, prepay, OrderStatus } from '@/api/pay'
import usePolling from '~~/composables/usePolling'
import feedback from '~~/utils/feedback'
import { useUserStore } from '~~/stores/user'
import {onMounted} from "vue";

const emits = defineEmits(['PaySuccess'])

const route = useRoute()
const router = useRouter()
const popShow = ref(false)
const props = defineProps(['modelValue', 'type', 'redirect'])
const userStore = useUserStore()
const { getUser } = userStore

//支付方式列表
const payWayList: any = ref([])

//订单信息
const order: any = ref({})

//支付参数
const payParam: any = ref({
    from: props.type,
    pay_way: ''
})

//微信二维码弹框
const wxPayCodePopRef = ref(null)
// 支付宝二维码弹窗
const aliPayCodePopRef = ref(null)

//选中的支付方式
const selectedType = ref(0)
//选择支付方式
const handleSelect = (type: number) => {
    end()
    selectedType.value = type
    payParam.value.pay_way = type
}

const runtimeConfig = useRuntimeConfig()
//订单支付
const orderPay = async () => {
    const data = await prepay({
        device: 'pc',
        order_id: order.value.order_id,
        ...payParam.value,
        redirect: `${runtimeConfig.public.baseUrl}${props.redirect}`
    })
    if (data?.code == -1) {
        feedback.msgError(data.msg)
        return
    }
    if (selectedType.value == 3) {
        const alipayPage = window.open('', '_self')
        alipayPage.document.body.innerHTML = data.config
        alipayPage.document.forms[0].submit()
    } else if (selectedType.value == 2){
        openWxPayCodePop(data)
        start()
    } else if (selectedType.value == 4){
        // if (data?.config) {
            const alipayPage = window.open('', '_self')
            alipayPage.document.body.innerHTML = data.config
            alipayPage.document.forms[0].submit()
        // } else {
        //     openWxPayCodePop(data)
        //     start()
        // }
    } else if (selectedType.value == 5){
        const alipayPage = window.open('', '_self')
        alipayPage.document.body.innerHTML = data.config
        alipayPage.document.forms[0].submit()
        // if (data?.payurl) {
        //     window.open(data.payurl, '_self')
        //     start()
        // } else {
        //     openAliPayCodePop(data)
        //     start()
        // }
    }
}

//微信二维码支付弹框
const openWxPayCodePop = (value) => {
    wxPayCodePopRef.value.open(value)
}

//支付宝二维码支付弹框
const openAliPayCodePop = (value) => {
    aliPayCodePopRef.value.open(value)
}

//获取支付方式
const getPaywayList = async () => {
    payWayList.value = await getPayWayList({
        order_id: order.value.order_id,
        from: props.type
    })
    payWayList.value.lists.map((item) => {
        if (item.is_default == 1) {
            payParam.value.pay_way = item.pay_way
            selectedType.value = item.pay_way
        }
    })
}

const check = async () => {
    const { pay_status } = await OrderStatus({
        order_id: order.value.order_id,
        from: props.type
    })
    if (pay_status == 1) {
        emits('PaySuccess')
        feedback.alertSuccess('支付成功')
        getUser()
        popShow.value = false
        end()
        wxPayCodePopRef.value.close()
    }
}

onMounted(() => {
    if (route.query.checkPay || route.query['amp;checkPay']) {
        emits('PaySuccess')
        feedback.alertSuccess('支付成功')
        router.replace({
            path: '',
            query: {
                mode: route.query.mode
            }
        })
        getUser()
    }
})

const endCallback = () => {
    popShow.value = false
    feedback.alertWarning('支付超时！')
}

//轮询参数
const { start, end, result } = usePolling(check, {
    totalTime: 300 * 1000,
    callback: endCallback
})
//关闭弹框回调
const closePop = () => {
    end()
}

//打开弹框
const open = (orderMsg: any) => {
    popShow.value = true
    console.log(orderMsg)
    order.value = orderMsg
    getPaywayList()
}

defineExpose({ open })
</script>

<style lang="scss" scoped>
.actived {
    border: 1px solid var(--el-color-primary) !important;
}
.unactive {
    border: 1px solid #e5e5ea;
}
.payBox {
    border-top: 1px solid #e2e2e2;
    .wxpay {
        border: 1px solid #4ab415;
    }
    .alipay {
        border: 1px solid #02aaee;
    }
}
</style>
