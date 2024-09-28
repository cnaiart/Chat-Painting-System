<template>
    <ElDialog
        v-model="popShow"
        width="500px"
        title="提现详情"
        :close-on-click-modal="false"
        @close="closePop"
    >
        <el-form label-width="120px">
            <el-form-item label="提现状态">
                <!-- {{ formData.status_desc || '-' }} -->
                <el-tag v-if="formData.status == 1">{{
                    formData.status_desc || '-'
                }}</el-tag>
                <el-tag type="success" v-if="formData.status == 3">{{
                    formData.status_desc || '-'
                }}</el-tag>

                <el-tag type="warning" v-if="formData.status == 2">{{
                    formData.status_desc || '-'
                }}</el-tag>
                <el-tag type="danger" v-if="formData.status == 4">{{
                    formData.status_desc || '-'
                }}</el-tag>
            </el-form-item>
            <el-form-item label="用户信息">
                {{ formData.nickname || '-' }}
            </el-form-item>
            <el-form-item label="提现金额">
                {{ formData.money || '-' }}
            </el-form-item>
            <el-form-item label="手续费">
                {{ formData.handling_fee || '-' }}
            </el-form-item>
            <el-form-item label="到账金额">
                {{ formData.left_money || '-' }}
            </el-form-item>
            <el-form-item label="提现方式">
                {{ formData.type_desc || '-' }}
            </el-form-item>
            <el-form-item label="真实姓名">
                {{ formData.real_name || '-' }}
            </el-form-item>
            <el-form-item
                v-if="formData.type != 2"
                :label="`${formData.type == 3 ? '微信' : '支付宝'}账号`"
            >
                {{ formData.account }}
            </el-form-item>
            <el-form-item
                label="收款码"
                v-if="formData.type == 3 || formData.type == 4"
            >
                <el-image
                    :src="formData.money_qr_code"
                    class="w-[60px] h-[60px]"
                    :preview-src-list="[formData.money_qr_code]"
                ></el-image>
            </el-form-item>
            <el-form-item label="申请时间">
                {{ formData.create_time || '-' }}
            </el-form-item>
            <el-form-item label="审核时间">
                {{ formData.verify_time || '-' }}
            </el-form-item>
            <el-form-item label="失败时间" v-if="formData.status == 4">
                {{ formData.finish_time || '-' }}
            </el-form-item>
            <el-form-item label="审核说明" v-if="formData.verify_remark">
                {{ formData.verify_remark || '-' }}
            </el-form-item>
            <el-form-item label="转账说明" v-if="formData.transfer_remark">
                {{ formData.transfer_remark || '-' }}
            </el-form-item>
        </el-form>
    </ElDialog>
</template>

<script setup lang="ts">
import { ElDialog, ElForm, ElFormItem, ElTag } from 'element-plus'
import { ref } from 'vue'
import { withdrawDetail } from '@/api/promotion'

const emit = defineEmits(['closePop'])

//弹框显示/隐藏
const popShow = ref(false)

const formData: any = ref({})

//打开弹框
const open = async (option: any) => {
    popShow.value = true
    formData.value = await withdrawDetail({ id: option.id })
}

//关闭弹框
const closePop = () => {
    popShow.value = false
    emit('closePop')
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
