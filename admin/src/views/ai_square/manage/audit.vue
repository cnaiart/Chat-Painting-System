<template>
    <div class="audit-popup">
        <popup
            ref="popupRef"
            title="审核"
            :async="true"
            width="550px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form
                class="ls-form"
                ref="formRef"
                :rules="rules"
                :model="formData"
                label-width="90px"
            >
                <el-form-item label="审核结果" prop="image">
                    <el-radio-group v-model="formData.verify_status">
                        <el-radio :label="1" size="large">审核通过</el-radio>
                        <el-radio :label="2" size="large">审核拒绝</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="拒绝原因" prop="verify_result" v-if="formData.verify_status == 2">
                    <el-input
                        v-model="formData.verify_result"
                        type="textarea"
                        :autosize="{ minRows: 8, maxRows: 20 }"
                        placeholder="请输入拒绝原因"
                    />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import { auditDrawSquare } from '@/api/ai_square/manage'
import Popup from '@/components/popup/index.vue'
const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
//表单数据
const formData = reactive({
    id: [] as number[],
    verify_status: 1,
    verify_result: ''
})
//校验规则
const rules = {}

//提交
const handleSubmit = async () => {
    await formRef.value?.validate()
    await auditDrawSquare(formData)
    popupRef.value?.close()
    emit('success')
}

const handleClose = () => {
    emit('close')
}

const open = (ids: number[]) => {
    formData.id = ids
    popupRef.value?.open()
}

defineExpose({ open })
</script>
