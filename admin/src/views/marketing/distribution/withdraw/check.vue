<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            title="审核"
            :async="true"
            width="550px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams">
                <el-form-item label="审核状态">
                    <el-radio-group v-model="formData.verify_status" class="ml-4">
                        <el-radio :label="2">审核通过</el-radio>
                        <el-radio :label="3">审核拒绝</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item
                    label="拒绝原因"
                    v-if="formData.verify_status == 3"
                    class="is-required"
                >
                    <el-input
                        type="textarea"
                        :rows="5"
                        placeholder="请输入拒绝原因"
                        v-model="formData.verify_remark"
                    ></el-input>
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { verify } from '@/api/marketing/distribution'

const emit = defineEmits(['success', 'close'])
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const formData = ref({
    id: '',
    verify_status: 2,
    verify_remark: ''
})
const open = (val: any) => {
    popupRef.value?.open()
    formData.value.id = val
}
const queryParams = ref()
const handleClose = () => {
    emit('close')
}
const handleSubmit = async () => {
    await verify(formData.value)
    emit('close')
}
defineExpose({
    open
})
const radio1 = ref(1)
</script>
