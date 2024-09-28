<template>
    <div class="edit-popup">
        <popup ref="popupRef" title="重置密码" :async="true" width="550px" @confirm="handleSubmit">
            <el-form ref="formRef" :model="formData" label-width="84px" :rules="rules" @submit.native.prevent>
                <el-form-item label="密码设置" prop="password">
                    <el-input
                        v-model="formData.password"
                        placeholder="请输入重置后的密码"
                        type="password"
                    />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance, FormRules } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { rePassword } from '@/api/consumer'
const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const props = defineProps<{
    userId: number | string
}>()
const formData = ref({
    password: ''
})

const rules: FormRules = {
    password: [
        {
            required: true,
            message: '请输入重置后的密码'
        }
    ]
}

const handleSubmit = async () => {
    await rePassword({
        id: props.userId,
        ...formData.value
    })
    popupRef.value?.close()
    emit('success')
}

const open = () => {
    popupRef.value?.open()
}

defineExpose({
    open
})
</script>
