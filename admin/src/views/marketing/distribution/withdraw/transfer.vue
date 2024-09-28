<template>
    <div class="edit-popup">
        <popup ref="popupRef" title="转账" :async="true" width="550px" @confirm="handleSubmit" @close="handleClose">
            <el-form ref="formRef" class="mb-[-16px]" :model="formData">
                <el-form-item label="转账状态">
                    <el-radio-group v-model="formData.transfer_status" class="ml-4">
                        <el-radio :label="1">转账成功</el-radio>
                        <el-radio :label="2">转账失败</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="拒绝原因" v-if="formData.transfer_status == 2">
                    <el-input type="textarea" :rows="5" placeholder="请输入失败原因" v-model="formData.transfer_remark"></el-input>
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { withdrawtransfer } from '@/api/marketing/distribution'

const emit = defineEmits(['success', 'close'])
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const formData = ref({
    id: '',
    transfer_status: 1,
    transfer_remark: ''
})
const open = (val: any) => {
    popupRef.value?.open()
    formData.value.id = val
}
const handleClose = () => {
    emit('close')
}
const handleSubmit = async () => {
    await withdrawtransfer(formData.value)
    emit('close')
}
defineExpose({
    open
})
const radio1 = ref(1)
</script>
