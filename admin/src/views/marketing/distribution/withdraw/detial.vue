<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            title="提现详情"
            :async="true"
            width="550px"
            @close="handleClose"
            cancelButtonText=""
            confirmButtonText=""
        >
            <el-form ref="formRef" class="mb-[-16px]" :model="formData">
                <el-form-item label="提现状态">
                    <el-tag type="primary" v-if="formData.status == 1">
                        {{ formData.status_desc }}
                    </el-tag>
                    <el-tag type="warning" v-if="formData.status == 2">
                        {{ formData.status_desc }}
                    </el-tag>
                    <el-tag type="success" v-if="formData.status == 3">
                        {{ formData.status_desc }}
                    </el-tag>
                    <el-tag type="danger" v-if="formData.status == 4">
                        {{ formData.status_desc }}
                    </el-tag>
                </el-form-item>
                <el-form-item label="用户信息">{{ formData.nickname }} </el-form-item>
                <el-form-item label="提现金额"> {{ formData.money }} </el-form-item>
                <el-form-item label="手续费">
                    {{ formData.handling_fee }}({{ formData.handling_fee_ratio }})
                </el-form-item>
                <el-form-item label="到账金额" class="text-red">
                    {{ formData.left_money }}
                </el-form-item>
                <el-form-item label="提现方式"> {{ formData.type_desc }} </el-form-item>
                <el-form-item v-if="formData.type != 2" label="真实姓名">
                    {{ formData.real_name }}
                </el-form-item>
                <el-form-item
                    v-if="formData.type != 2"
                    :label="`${formData.type == 3 ? '微信' : '支付宝'}账号`"
                >
                    {{ formData.account }}
                </el-form-item>
                <el-form-item label="收款码" v-if="formData.type == 3 || formData.type == 4">
                    <el-image
                        :src="formData.money_qr_code"
                        class="w-[60px] h-[60px]"
                        :preview-src-list="[formData.money_qr_code]"
                    ></el-image>
                </el-form-item>
                <el-form-item label="申请时间">
                    {{ formData.create_time }}
                </el-form-item>
                <el-form-item label="审核时间" v-if="formData.status != 1">
                    {{ formData.verify_time || '-' }}
                </el-form-item>
                <el-form-item label="转账时间" v-if="formData.status == 3">
                    {{ formData.finish_time || '-' }}
                </el-form-item>
                <el-form-item label="失败时间" v-if="formData.status == 4">
                    {{ formData.finish_time || '-' }}
                </el-form-item>
                <el-form-item label="转账说明" v-if="formData.transfer_remark">
                    <div style="word-break: break-all">
                        {{ formData.transfer_remark || '-' }}
                    </div>
                </el-form-item>
                <el-form-item label="审核说明" v-if="formData.verify_remark">
                    <div style="word-break: break-all">
                        {{ formData.verify_remark || '-' }}
                    </div>
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { withdrawdetail } from '@/api/marketing/distribution'

const emit = defineEmits(['success', 'close'])
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const open = async (val: any) => {
    popupRef.value?.open()
    formData.value = await withdrawdetail({ id: val })
}
const handleClose = () => {
    emit('close')
}
const formData = ref<any>({})
defineExpose({
    open
})
</script>
