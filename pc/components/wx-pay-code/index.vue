<template>
    <ElDialog
        v-model="popShow"
        width="400px"
        center
        :close-on-click-modal="false"
        @close="closePop"
    >
        <div class="flex flex-col items-center">
            <div class="text-[18px] mb-[20px]">微信扫一扫支付</div>
            <QrcodeVue :value="qrCode" :size="160" />
            <div class="mt-[10px]">支付成功后自动关闭窗口</div>
            <div class="mt-[10px]">如遇到支付问题，请联系客服解决</div>
        </div>
    </ElDialog>
</template>

<script setup lang="ts">
import { ElDialog } from 'element-plus'
import QrcodeVue from 'qrcode.vue'
const emit = defineEmits(['popClose'])

const qrCode = ref('')

const popShow = ref(false)

//打开弹框
const open = (wxData: any) => {
    popShow.value = true
    qrCode.value = wxData?.config || wxData.qrcode
}
const close = () => {
    popShow.value = false
}
const closePop = () => {
    emit('popClose')
}

defineExpose({ open, close })
</script>

<style lang="scss" scoped></style>
