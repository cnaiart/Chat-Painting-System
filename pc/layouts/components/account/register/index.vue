<template>
    <div class="flex-1 flex flex-col">
        <div class="flex flex-1 flex-col py-[20px] px-[30px] min-h-0">
            <el-tabs v-model="formData.scene" @tabChange="changeTabs">
                <el-tab-pane
                    v-for="(item, index) in registerWayListsFilter"
                    :key="index"
                    :label="item.name"
                    :name="item.type"
                />
            </el-tabs>
            <ElForm
                v-if="getRegisterWay.length"
                ref="formRef"
                class="mt-[10px]"
                size="large"
                :model="formData"
                :rules="formRules"
            >
                <ElFormItem
                    v-if="formData.scene === RegisterSceneEnum.MOBILE"
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
                    v-if="formData.scene === RegisterSceneEnum.MAILBOX"
                    prop="email"
                >
                    <ElInput
                        v-model="formData.email"
                        placeholder="请输入邮箱账号"
                    />
                </ElFormItem>
                <ElFormItem v-if="isOpenSendSms" prop="code">
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
                        type="password"
                        show-password
                        placeholder="请输入6-20位数字+字母或符号组合"
                    />
                </ElFormItem>
                <ElFormItem prop="password_confirm">
                    <ElInput
                        v-model="formData.password_confirm"
                        type="password"
                        show-password
                        placeholder="请再次输入密码"
                    />
                </ElFormItem>
                <ElFormItem class="mt-[30px]">
                    <ElButton
                        class="w-full"
                        type="primary"
                        :loading="isLock"
                        @click="handleConfirmLock"
                    >
                        注册
                    </ElButton>
                </ElFormItem>
            </ElForm>

            <div class="flex justify-center">
                已有账号？<ElButton
                    type="primary"
                    link
                    @click="setLoginPopupType(LoginPopupTypeEnum.LOGIN)"
                >
                    立即登录
                </ElButton>
            </div>
        </div>
        <div
            v-if="isOpenAgreement"
            class="bg-[#f4f4f4] px-[20px] py-[15px] flex"
        >
            <div class="flex-1 text-tx-secondary">
                您注册即同意
                <NuxtLink
                    v-slot="{ href }"
                    :to="`/policy/${PolicyAgreementEnum.SERVICE}`"
                    custom
                >
                    <a class="text-tx-primary" :href="href" target="_blank">
                        用户协议
                    </a>
                </NuxtLink>
                和
                <NuxtLink
                    v-slot="{ href }"
                    class="text-tx-primary"
                    :to="`/policy/${PolicyAgreementEnum.PRIVACY}`"
                    custom
                >
                    <a class="text-tx-primary" :href="href" target="_blank">
                        隐私政策
                    </a>
                </NuxtLink>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance, FormRules } from 'element-plus'
import { smsSend } from '@/api/app'
import {
    LoginPopupTypeEnum,
    PolicyAgreementEnum,
    SMSEnum
} from '@/enums/appEnums'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'
import { register, sendEmailCode } from '@/api/account'

enum RegisterSceneEnum {
    MOBILE = '2',
    MAILBOX = '3'
}
const registerWayLists = [
    {
        name: '手机号注册',
        type: RegisterSceneEnum.MOBILE
    },
    {
        name: '邮箱注册',
        type: RegisterSceneEnum.MAILBOX
    }
]
const getRegisterWay = computed<string[]>(
    () => appStore?.getLoginConfig?.login_way || []
)

const registerWayListsFilter = computed(() => {
    const value = registerWayLists.filter((item) =>
        getRegisterWay.value.includes(item.type)
    )
    if (value.length) {
        formData.scene = value[0].type
    }
    return value
})

const { setLoginPopupType } = useUserStore()
const formRef = shallowRef<FormInstance>()
const formRules: FormRules = {
    mobile: [
        {
            required: true,
            message: '请输入手机号'
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
            message: '请输入验证码'
        }
    ],
    password: [
        {
            required: true,
            message: '请输入6-20位数字+字母或符号组合'
        },
        {
            min: 6,
            max: 20,
            message: '密码长度应为6-20'
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
            }
        }
    ]
}
const formData = reactive({
    scene: '2',
    mobile: '',
    code: '',
    email: '',
    password: '',
    password_confirm: ''
})

const verificationCodeRef = shallowRef()

const changeTabs = (index: number) => {
    if (index == 3) {
        formData.mobile = ''
    } else {
        formData.email = ''
    }
}

const sendCode = async () => {
    formData.scene === RegisterSceneEnum.MOBILE
        ? await sendSms()
        : await sendEmail()
    verificationCodeRef.value?.start()
}
const sendSms = async () => {
    await formRef.value?.validateField(['mobile'])
    await smsSend({
        scene: SMSEnum.REGISTER,
        mobile: formData.mobile
    })
}
const sendEmail = async () => {
    await formRef.value?.validateField(['email'])
    await sendEmailCode({
        scene: SMSEnum.REGISTER,
        email: formData.email
    })
}
const appStore = useAppStore()
const isOpenSendSms = computed(() => appStore.getLoginConfig?.sms_verify === 1)
const isOpenAgreement = computed(
    () => appStore.getLoginConfig.is_agreement === 1
)
const handleConfirm = async () => {
    await formRef.value?.validate()
    await register(formData)
    setLoginPopupType(LoginPopupTypeEnum.LOGIN)
}
const { lockFn: handleConfirmLock, isLock } = useLockFn(handleConfirm)

watch(
    () => formData.scene,
    () => {
        formData.password = ''
        formData.code = ''
        formData.password = ''
        formRef.value?.clearValidate()
    }
)
</script>

<style lang="scss" scoped>
.el-tabs {
    :deep(.el-tabs__item) {
        @apply text-xl;
    }
}
</style>
