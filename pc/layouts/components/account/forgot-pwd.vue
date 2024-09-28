<template>
    <div class="flex-1 flex flex-col">
        <div class="flex flex-1 flex-col pt-[30px] px-[30px] min-h-0">
            <span class="text-2xl font-medium text-tx-primary">更换密码</span>
            <ElForm
                ref="formRef"
                class="mt-[35px]"
                size="large"
                :model="formData"
                :rules="formRules"
            >
                <ElFormItem
                    v-if="
                        userStore.loginPopupType ===
                        LoginPopupTypeEnum.FORGOT_PWD_MOBILE
                    "
                    prop="mobile"
                >
                    <ElInput
                        v-model="formData.mobile"
                        placeholder="请输入手机号"
                    >
                        <template #prepend>
                            <ElSelect model-value="+86" style="width: 80px">
                                <ElOption label="+86" value="+86" />
                            </ElSelect>
                        </template>
                    </ElInput>
                </ElFormItem>
                <ElFormItem
                    v-if="
                        userStore.loginPopupType ===
                        LoginPopupTypeEnum.FORGOT_PWD_MAILBOX
                    "
                    prop="email"
                >
                    <ElInput
                        v-model="formData.email"
                        placeholder="请输入邮箱账号"
                    />
                </ElFormItem>
                <ElFormItem prop="code">
                    <ElInput v-model="formData.code" placeholder="请输入验证码">
                        <template #suffix>
                            <div
                                class="flex justify-center leading-5 w-[90px] pl-2.5 border-l border-br"
                            >
                                <VerificationCode
                                    ref="verificationCodeRef"
                                    @click-get="sendCode"
                                />
                            </div>
                        </template>
                    </ElInput>
                </ElFormItem>
                <ElFormItem prop="password">
                    <ElInput
                        v-model="formData.password"
                        placeholder="请输入6-20位数字+字母或符号组合"
                        type="password"
                        show-password
                    />
                </ElFormItem>
                <ElFormItem prop="password_confirm">
                    <ElInput
                        v-model="formData.password_confirm"
                        placeholder="请再次输入密码"
                        type="password"
                        show-password
                    />
                </ElFormItem>
                <ElFormItem class="mt-[30px]">
                    <ElButton
                        class="w-full"
                        type="primary"
                        :loading="isLock"
                        @click="handleConfirmLock"
                    >
                        确认
                    </ElButton>
                </ElFormItem>
                <ElFormItem class="mt-[20px]">
                    <div class="flex justify-center flex-1">
                        <ElButton
                            v-if="!userStore.isLogin"
                            type="primary"
                            link
                            @click="setLoginPopupType(LoginPopupTypeEnum.LOGIN)"
                        >
                            返回登录
                        </ElButton>
                    </div>
                </ElFormItem>
            </ElForm>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance, FormRules } from 'element-plus'

import { smsSend } from '@/api/app'
import { forgotPassword } from '@/api/user'
import { LoginPopupTypeEnum, SMSEnum } from '@/enums/appEnums'
import { useUserStore } from '@/stores/user'
import { sendEmailCode } from '~/api/account'
const userStore = useUserStore()
const { setLoginPopupType } = useUserStore()
const formRef = shallowRef<FormInstance>()
const verificationCodeRef = shallowRef()
const formRules: FormRules = {
    mobile: [
        {
            required: true,
            message: '请输入手机号码'
        }
    ],
    email: [
        {
            required: true,
            message: '请输入邮箱账号'
        },
        { type: 'email', message: '请输入正确的邮箱账号' }
    ],
    code: [
        {
            required: true,
            message: '请输入验证码',
            trigger: ['change', 'blur']
        }
    ],
    password: [
        {
            required: true,
            message: '请输入6-20位数字+字母或符号组合',
            trigger: ['change', 'blur']
        },
        {
            min: 6,
            max: 20,
            message: '密码长度应为6-20',
            trigger: ['change', 'blur']
        }
    ],
    password_confirm: [
        {
            validator(rule: any, value: any, callback: any) {
                if (value === '') {
                    callback(new Error('请再次输入密码'))
                } else if (value !== formData.password) {
                    callback(new Error('两次输入的密码不一致'))
                } else {
                    callback()
                }
            },
            trigger: ['change', 'blur']
        }
    ]
}
const formData = reactive({
    email: '',
    mobile: '',
    scene: '',
    password: '',
    code: '',
    password_confirm: ''
})
const sendCode = async () => {
    try {
        userStore.loginPopupType === LoginPopupTypeEnum.FORGOT_PWD_MOBILE
            ? await sendSms()
            : await sendEmail()
        verificationCodeRef.value?.start()
    } catch (error) {
        console.log('error', error)
    }
}
const sendSms = async () => {
    await formRef.value?.validateField(['mobile'])
    await smsSend({
        scene: SMSEnum.FIND_PASSWORD,
        mobile: formData.mobile
    })
}
const sendEmail = async () => {
    await formRef.value?.validateField(['email'])
    await sendEmailCode({
        scene: SMSEnum.FIND_PASSWORD,
        email: formData.email
    })
}

const handleConfirm = async () => {
    await formRef.value?.validate()
    await forgotPassword({
        ...formData,
        scene:
            userStore.loginPopupType ===
            LoginPopupTypeEnum.FORGOT_PWD_MOBILE
                ? 2
                : 3
    })
    userStore.logout()
    setLoginPopupType(LoginPopupTypeEnum.LOGIN)
}
const { lockFn: handleConfirmLock, isLock } = useLockFn(handleConfirm)
</script>

<style lang="scss" scoped></style>
