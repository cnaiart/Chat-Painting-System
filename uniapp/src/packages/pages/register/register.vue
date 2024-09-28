<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view
        class="register bg-white min-h-full flex flex-col items-center px-[40rpx] pt-[40rpx] box-border"
    >
        <view class="w-full">
            <view class="pb-[40rpx]">
                <u-tabs
                    :list="registerWayListsFilter"
                    :is-scroll="false"
                    v-model="currentTabs"
                    :active-color="$theme.primaryColor"
                    @change="changeTabs"
                ></u-tabs>
            </view>
            <u-form borderBottom :label-width="150">
                <u-form-item label="手机号" borderBottom v-if="isMobile">
                    <u-input
                        class="flex-1"
                        v-model="formData.mobile"
                        :border="false"
                        placeholder="请输入手机号码"
                    />
                </u-form-item>
                <u-form-item label="邮箱" borderBottom v-if="isMailbox">
                    <u-input
                        class="flex-1"
                        v-model="formData.email"
                        :border="false"
                        placeholder="请输入邮箱账号"
                    />
                </u-form-item>
                <u-form-item label="验证码" v-if="isOpenSendSms">
                    <u-input
                        class="flex-1"
                        v-model="formData.code"
                        placeholder="请输入验证码"
                        :border="false"
                    />
                    <view
                        class="border-l border-solid border-0 border-light pl-3 leading-4 ml-3 w-[180rpx]"
                        @click="sendCode"
                    >
                        <u-verification-code
                            ref="uCodeRef"
                            :seconds="60"
                            @change="codeChange"
                            change-text="x秒"
                        />
                        <text
                            class="text-muted"
                            :class="{
                                'text-primary':
                                    (isValidMobile && isMobile) ||
                                    (isValidMailBox && isMailbox)
                            }"
                        >
                            {{ codeTips }}
                        </text>
                    </view>
                </u-form-item>
                <u-form-item label="设置密码" borderBottom>
                    <u-input
                        class="flex-1"
                        type="password"
                        v-model="formData.password"
                        placeholder="请输入字母+数字组合的密码"
                        :border="false"
                    />
                </u-form-item>
                <u-form-item label="确认密码" borderBottom>
                    <u-input
                        class="flex-1"
                        type="password"
                        v-model="formData.password_confirm"
                        placeholder="再次输入密码"
                        :border="false"
                    />
                </u-form-item>
            </u-form>
            <view class="mt-[40rpx]">
                <agreement ref="agreementRef" />
            </view>
            <view class="mt-[60rpx]">
                <u-button
                    type="primary"
                    shape="circle"
                    @click="accountRegister"
                >
                    注册
                </u-button>
            </view>
        </view>
    </view>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>

<script setup lang="ts">
import { register, sendEmailCode } from '@/api/account'
import { useAppStore } from '@/stores/app'
import { computed, reactive, ref, shallowRef } from 'vue'
// import { useRouter } from 'uniapp-router-next-zm'
import { useRouter } from 'uniapp-router-next'
import { smsSend } from '@/api/app'
import { SMSEnum } from '@/enums/appEnums'
import { onLoad } from '@dcloudio/uni-app'
import { useLockFn } from '@/hooks/useLockFn'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

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
const currentTabs = ref<number>(0)

const getRegisterWay = computed<string[]>(
    () => appStore.getLoginConfig?.login_way || []
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

const appStore = useAppStore()
const router = useRouter()
const agreementRef = shallowRef()
const formData = reactive({
    scene: '2',
    mobile: '',
    code: '',
    email: '',
    password: '',
    password_confirm: ''
})
const uCodeRef = shallowRef()
const codeTips = ref('')
const codeChange = (text: string) => {
    codeTips.value = text
}
const isValidMobile = computed(() => uni.$u.test.mobile(formData.mobile))
const isValidMailBox = computed(() => uni.$u.test.email(formData.email))
const isOpenSendSms = computed(() => appStore.getLoginConfig?.sms_verify == 1)
const isMobile = computed(() => formData.scene == RegisterSceneEnum.MOBILE)
const isMailbox = computed(() => formData.scene == RegisterSceneEnum.MAILBOX)

const changeTabs = (index: number) => {
    currentTabs.value = index
    formData.scene = registerWayLists[index].type
    if (index == 1) {
        formData.mobile = ''
    } else {
        formData.email = ''
    }
}

const sendCode = async () => {
    if (!uCodeRef.value?.canGetCode) return
    try {
        isMobile.value ? await sendSms() : await sendEmail()
        uni.$u.toast('发送成功')
        uCodeRef.value?.start()
    } catch (error) {
        console.log('发送验证码失败=>', error)
    }
}
const sendEmail = async () => {
    console.log(isValidMailBox.value)
    if (!formData.email) {
        uni.$u.toast('请输入邮箱')
        return Promise.reject()
    }
    if (!isValidMailBox.value) {
        uni.$u.toast('请输入正确的邮箱地址')
        return Promise.reject()
    }
    await sendEmailCode({
        scene: SMSEnum.REGISTER,
        email: formData.email
    })
}
const sendSms = async () => {
    if (!formData.mobile) {
        uni.$u.toast('请输入手机号')
        return Promise.reject()
    }
    if (isValidMailBox.value) {
        uni.$u.toast('请输入正确的手机号')
        return Promise.reject()
    }
    await smsSend({
        scene: SMSEnum.REGISTER,
        mobile: formData.mobile
    })
}

const { lockFn: accountRegister } = useLockFn(async () => {
    if (!agreementRef.value?.checkAgreement()) {
        return
    }
    if (!formData.mobile && isMobile.value)
        return uni.$u.toast('请输入手机号码')
    if (!formData.email && isMailbox.value) return uni.$u.toast('请输入邮箱')
    if (!formData.code && isOpenSendSms.value)
        return uni.$u.toast('请输入验证码')
    if (!formData.password) return uni.$u.toast('请输入密码')
    if (!formData.password_confirm) return uni.$u.toast('请再次输入密码')
    if (formData.password != formData.password_confirm)
        return uni.$u.toast('两次输入的密码不一致')
    await register(formData)
    setTimeout(() => {
        router.navigateBack()
    }, 1500)
})

onLoad(async ({ type }: any) => {
    setTimeout(() => {
        formData.scene = type
        if (
            type == 3 &&
            getRegisterWay.value.includes(RegisterSceneEnum.MOBILE)
        ) {
            currentTabs.value = 1
        }
    }, 300)
})
</script>

<style lang="scss">
page {
    height: 100%;
}
</style>
