<template>
    <popup
        ref="popupRef"
        title="创建用户"
        width="500px"
        :async="true"
        @confirm="handleConfirm"
    >
        <div class="pr-8">
            <el-form ref="formRef" :model="formData" label-width="120px" :rules="formRules" @submit.native.prevent>
                <el-form-item label="用户昵称" prop="avatar">
                    <material-picker v-model="formData.avatar"></material-picker>
                </el-form-item>
                <el-form-item label="用户昵称" prop="nickname">
                    <el-input v-model="formData.nickname" placeholder="请输入用户昵称" />
                </el-form-item>
                <el-form-item label="手机号" prop="mobile">
                    <el-input
                        v-model="formData.mobile"
                        placeholder="请输入手机号码，手机号和邮箱必填一个"
                    />
                </el-form-item>
                <el-form-item label="邮箱" prop="email">
                    <el-input
                        v-model="formData.email"
                        placeholder="请输入邮箱，邮箱和手机号必填一个"
                    />
                </el-form-item>
                <el-form-item label="真实姓名" prop="real_name">
                    <el-input v-model="formData.real_name" placeholder="请输入真实姓名" />
                </el-form-item>
                <el-form-item label="登录密码" prop="password">
                    <el-input v-model="formData.password" placeholder="请输入登录密码" />
                </el-form-item>
                <el-form-item label="确认密码" prop="password_confirm">
                    <el-input v-model="formData.password_confirm" placeholder="请输入确认密码" />
                </el-form-item>
            </el-form>
        </div>
    </popup>
</template>
<script lang="ts" setup>
import type Popup from '@/components/popup/index.vue'
import type { FormInstance, FormRules } from 'element-plus'
import { accountAdd } from '@/api/consumer'
import feedback from '@/utils/feedback'
const formRef = shallowRef<FormInstance>()

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    value: {
        type: [Number, String],
        required: true
    }
})

const emit = defineEmits<{
    (event: 'close', value: void): void
    (event: 'success', value: void): void
    (event: 'confirm', value: any): void
}>()

const formData = reactive({
    avatar: '',
    nickname: '',
    mobile: '',
    email: '',
    real_name: '',
    password: '',
    password_confirm: ''
})
const popupRef = shallowRef<InstanceType<typeof Popup>>()

const passwordConfirmValidator = (rule: object, value: string, callback: any) => {
    if (formData.password) {
        if (!value) callback(new Error('请再次输入密码'))
        if (value !== formData.password) callback(new Error('两次输入密码不一致!'))
    }
    callback()
}

const formRules: FormRules = {
    avatar: [{ required: true, message: '请选择头像', trigger: 'blur' }],
    nickname: [{ required: true, message: '请输入用户昵称', trigger: 'blur' }],
    password: [{ required: true, message: '请输入用户密码', trigger: 'blur' }],
    password_confirm: [{ required: true, validator: passwordConfirmValidator, trigger: 'blur' }]
}

const handleConfirm = async () => {
    await formRef.value?.validate()
    try {
        await accountAdd(formData)
        emit('success')
        emit('close')
        feedback.msgSuccess('操作成功')
    } catch (e) {
        console.log('创建用户失败=>', e)
    }
}

const open = () => {
    popupRef.value?.open()
}

defineExpose({ open })
</script>
