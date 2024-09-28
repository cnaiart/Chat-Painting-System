<template>
    <div class="pb-[28px]">
        <div class="mt-[20px]">
            <ElForm
                ref="formRef"
                size="large"
                :model="formData"
                :rules="formRules"
            >
                <ElFormItem prop="email">
                    <ElInput
                        v-model="formData.email"
                        placeholder="请输入邮箱账号"
                    />
                </ElFormItem>
                <ElFormItem prop="captcha" v-if="appStore.getLoginConfig.is_captcha">
                    <ElInput v-model="formData.captcha" placeholder="图形验证码">
                        <template #suffix>
                            <div
                                class="flex justify-center leading-5 w-[90px] cursor-pointer"
                            >
                                <img
                                    :src="captchaImage"
                                    title="点击刷新"
                                    alt="验证码"
                                    @click="getCaptchaFn"
                                />
                            </div>
                        </template>
                    </ElInput>
                </ElFormItem>
                <ElFormItem
                    prop="password"
                    :error="passwordRules.error"
                    :validate-status="passwordRules.status"
                >
                    <ElInput
                        v-model="formData.password"
                        type="password"
                        show-password
                        placeholder="请输入密码"
                    />
                </ElFormItem>
                <div class="flex justify-end">
                    <ElButton
                        link
                        @click="
                            userStore.setLoginPopupType(
                                LoginPopupTypeEnum.FORGOT_PWD_MAILBOX
                            )
                        "
                    >
                        忘记密码？
                    </ElButton>
                </div>

                <ElFormItem class="my-[30px]">
                    <ElButton
                        class="w-full"
                        type="primary"
                        :loading="isLock"
                        @click="loginLock"
                    >
                        登录
                    </ElButton>
                </ElFormItem>
            </ElForm>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance, FormRules } from 'element-plus'
import { LoginPopupTypeEnum } from '@/enums/appEnums'
import { login } from '@/api/account'
import { useUserStore } from '@/stores/user'
import { useAppStore } from '~/stores/app'
import { ElFormItem, ElInput } from 'element-plus'
import useCaptchaEffect from '../hooks/useCaptchaEffect'
// import { terminal } from '~/config'

const userStore = useUserStore()
const appStore = useAppStore()
const { captchaKey, captchaImage, getCaptchaFn } = useCaptchaEffect()
const formRef = shallowRef<FormInstance>()

const formRules: FormRules = {
    email: [
        {
            required: true,
            message: '请输入邮箱账号'
        },
        { type: 'email', message: '请输入正确的邮箱账号' }
    ],
    captcha: [
        {
            required: true,
            message: '请输入图形验证码'
        }
    ],
    password: [
        {
            required: true,
            message: '请输入密码'
        }
    ]
}
const passwordRules = reactive<any>({
    error: '',
    status: ''
})
const formData = reactive({
    email: '',
    password: '',
    captcha: '',
    scene: 3
})

const { lockFn: loginLock, isLock } = useLockFn(async () => {
    try {
        await formRef.value?.validate()
        const data = await login({
            ...formData,
            key: captchaKey.value,
            captcha: formData.captcha
        })
        if (!data.mobile && appStore.getLoginConfig.coerce_mobile) {
            userStore.temToken = data.token
            userStore.setLoginPopupType(LoginPopupTypeEnum.BIND_MOBILE)
        } else {
            userStore.login(data.token)
            userStore.setUser(data)
            userStore.toggleShowLogin(false)
            location.reload()
        }
    } catch (error) {
        if (error == '密码错误') {
            passwordRules.error = error
            passwordRules.status = 'error'
        }
        getCaptchaFn()
        console.log(error)
    }
})
</script>

<style lang="scss" scoped></style>
