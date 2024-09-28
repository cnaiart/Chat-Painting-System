<template>
    <div class="pb-[28px]">
        <div class="mt-[20px]">
            <ElForm
                ref="formRef"
                size="large"
                :model="formData"
                :rules="formRules"
            >
                <ElFormItem prop="mobile">
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
                    v-if="isPasswordLogin"
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
                <ElFormItem prop="code" v-if="isCodeLogin">

                    <ElInput v-model="formData.code" placeholder="请输入验证码">
                        <template #suffix>
                            <div
                                class="flex justify-center leading-5 w-[90px] pl-2.5 border-l border-br"
                            >
                                <VerificationCode
                                    ref="verificationCodeRef"
                                    @click-get="sendSms"
                                />
                            </div>
                        </template>
                    </ElInput>
                </ElFormItem>

                <div class="flex">
                    <div class="flex-1">
                        <ElButton
                            v-if="isPasswordLogin"
                            type="primary"
                            link
                            @click="changeLoginScene(MobileSceneEnum.CODE)"
                        >
                            手机验证码登录
                        </ElButton>
                        <ElButton
                            v-if="isCodeLogin"
                            type="primary"
                            link
                            @click="changeLoginScene(MobileSceneEnum.PASSWORD)"
                        >
                            手机密码登录
                        </ElButton>
                    </div>

                    <ElButton
                        v-if="isPasswordLogin"
                        link
                        @click="
                            userStore.setLoginPopupType(
                                LoginPopupTypeEnum.FORGOT_PWD_MOBILE
                            )
                        "
                    >
                        忘记密码？
                    </ElButton>
                </div>
                <ElFormItem class="mt-[30px]">
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
import {
    ElForm,
    ElFormItem,
    ElInput,
    ElButton,
    ElSelect,
    ElOption,
    FormInstance,
    FormRules
} from 'element-plus'
import { login } from '~/api/account'
import { smsSend } from '~/api/app'
import { LoginPopupTypeEnum, SMSEnum } from '~/enums/appEnums'
import { useUserStore } from '~/stores/user'
import { useAppStore } from '~/stores/app'
import useCaptchaEffect from '../hooks/useCaptchaEffect'

const userStore = useUserStore()
const appStore = useAppStore()
const { captchaKey, captchaImage, getCaptchaFn } = useCaptchaEffect()

const formRef = shallowRef<FormInstance>()
enum MobileSceneEnum {
    CODE = 4,
    PASSWORD = 2
}

const formRules: FormRules = {
    mobile: [
        {
            required: true,
            message: '请输入手机号'
        }
    ],
    password: [
        {
            required: true,
            message: '请输入密码'
        }
    ],
    captcha: [
        {
            required: true,
            message: '请输入图形验证码'
        }
    ],
    code: [
        {
            required: true,
            message: '请输入验证码'
        }
    ]
}
const passwordRules = reactive<any>({
    error: '',
    status: ''
})
const formData = reactive({
    code: '',
    mobile: '',
    captcha: '',
    password: '',
    scene: MobileSceneEnum.PASSWORD
})

const isCodeLogin = computed(() => formData.scene === MobileSceneEnum.CODE)
const isPasswordLogin = computed(
    () => formData.scene === MobileSceneEnum.PASSWORD
)

const changeLoginScene = (scene: MobileSceneEnum) => {
    getCaptchaFn()
    formData.scene = scene
}

const verificationCodeRef = shallowRef()
const sendSms = async () => {
    if (!formData.captcha.length && appStore.getLoginConfig.is_captcha) {
        feedback.msgError('请先输入图形验证码')
        return
    }
    await formRef.value?.validateField(['mobile'])
    await smsSend({
        key: captchaKey.value,
        captcha: formData.captcha,
        scene: SMSEnum.LOGIN,
        mobile: formData.mobile
    })

    verificationCodeRef.value?.start()
}

const { lockFn: loginLock, isLock } = useLockFn(async () => {
    await formRef.value?.validate()
    try {
        const data = await login({
            ...formData,
            key: captchaKey.value,
            captcha: formData.captcha
        })
        userStore.login(data.token)
        location.reload()
        await userStore.getUser()
        userStore.toggleShowLogin(false)
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
