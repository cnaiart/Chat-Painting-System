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
        class="register bg-white min-h-full flex flex-col items-center px-[40rpx] pt-[100rpx] box-border"
    >
        <view class="w-full">
            <view class="pb-[40rpx]">
                <view class="text-2xl font-medium mb-[60rpx]"
                    >忘记登录密码</view
                >
                <u-tabs
                    :list="forgetWayListsFilter"
                    :is-scroll="false"
                    v-model="currentTabs"
                    :active-color="$theme.primaryColor"
                    @change="changeTabs"
                ></u-tabs>
            </view>
            <u-form borderBottom :label-width="150">
                <u-form-item label="手机号" borderBottom v-show="isMobile">
                    <u-input
                        class="flex-1"
                        v-model="formData.mobile"
                        :border="false"
                        placeholder="请输入手机号码"
                    />
                </u-form-item>
                <u-form-item label="邮箱" borderBottom v-show="isMailbox">
                    <u-input
                        class="flex-1"
                        v-model="formData.email"
                        :border="false"
                        placeholder="请输入邮箱账号"
                    />
                </u-form-item>
                <u-form-item label="验证码" borderBottom>
                    <u-input
                        class="flex-1"
                        v-model="formData.code"
                        placeholder="请输入验证码"
                        :border="false"
                    />
                    <view
                        class="border-l border-solid border-0 border-light pl-3 text-muted leading-4 ml-3 w-[180rpx]"
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
                <u-form-item label="新密码" borderBottom>
                    <u-input
                        class="flex-1"
                        type="password"
                        v-model="formData.password"
                        placeholder="6-20位数字+字母或符号组合"
                        :border="false"
                    />
                </u-form-item>
                <u-form-item label="确认密码" borderBottom>
                    <u-input
                        class="flex-1"
                        type="password"
                        v-model="formData.password_confirm"
                        placeholder="再次输入新密码"
                        :border="false"
                    />
                </u-form-item>
            </u-form>
            <view class="mt-[100rpx]">
                <u-button type="primary" shape="circle" @click="handleConfirm">
                    确定
                </u-button>
            </view>
        </view>
        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
    </view>
</template>

<script setup lang="ts">
import { smsSend } from '@/api/app'
import { forgotPassword } from '@/api/user'
import { SMSEnum } from '@/enums/appEnums'
// import { useRouter } from 'uniapp-router-next-zm'
import { useRouter } from 'uniapp-router-next'
import { computed, reactive, ref, shallowRef } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { sendEmailCode } from '@/api/account'
import { useAppStore } from '@/stores/app'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

enum ForgotPwdSceneEnum {
    MOBILE = '2',
    MAILBOX = '3'
}

const forgetWayLists = [
    {
        name: '手机号',
        type: ForgotPwdSceneEnum.MOBILE
    },
    {
        name: '邮箱',
        type: ForgotPwdSceneEnum.MAILBOX
    }
]
const currentTabs = ref<number>(0)

const getRegisterWay = computed<string[]>(
    () => appStore.getLoginConfig?.login_way || []
)

const forgetWayListsFilter = computed(() => {
    const value = forgetWayLists.filter((item) =>
        getRegisterWay.value.includes(item.type)
    )
    if (value.length) {
        formData.scene = value[0].type
    }
    return value
})

const appStore = useAppStore()
const uCodeRef = shallowRef()
const codeTips = ref('')
const formData = reactive({
    email: '',
    mobile: '',
    password: '',
    scene: '2',
    code: '',
    password_confirm: ''
})

const isValidMobile = computed(() => uni.$u.test.mobile(formData.mobile))
const isValidMailBox = computed(() => uni.$u.test.email(formData.email))
const isMobile = computed(() => formData.scene == ForgotPwdSceneEnum.MOBILE)
const isMailbox = computed(() => formData.scene == ForgotPwdSceneEnum.MAILBOX)

const changeTabs = (index: number) => {
    currentTabs.value = index
    formData.scene = forgetWayLists[index].type
    if (index == 1) {
        formData.mobile = ''
    } else {
        formData.email = ''
    }
}

const codeChange = (text: string) => {
    codeTips.value = text
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
        scene: SMSEnum.FIND_PASSWORD,
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
        scene: SMSEnum.FIND_PASSWORD,
        mobile: formData.mobile
    })
}

const router = useRouter()
const handleConfirm = async () => {
    if (!formData.mobile && isMobile.value)
        return uni.$u.toast('请输入手机号码')
    if (!formData.email && isMailbox.value) return uni.$u.toast('请输入邮箱')
    if (!formData.password) return uni.$u.toast('请输入密码')
    if (!formData.password_confirm) return uni.$u.toast('请输入确认密码')
    if (formData.password != formData.password_confirm)
        return uni.$u.toast('两次输入的密码不一致')
    await forgotPassword(formData)
    setTimeout(() => {
        router.navigateBack()
    }, 1500)
}

onLoad(({ type }: any) => {
    formData.scene = type
    if (type == 3 && getRegisterWay.value.includes(ForgotPwdSceneEnum.MOBILE)) {
        currentTabs.value = 1
    }
})
</script>

<style lang="scss">
page {
    height: 100%;
}
</style>
