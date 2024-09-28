<template>
    <view>
        <view class="mb-[60rpx]">
            <u-form>
                <u-form-item>
                    <u-icon
                        class="mr-2"
                        :size="36"
                        name="/static/images/icon/icon_mobile.png"
                    />
                    <u-input
                        class="flex-1"
                        v-model="formData.email"
                        :border="false"
                        placeholder="请输入邮箱账号"
                    />
                </u-form-item>
                <u-form-item v-if="appStore.getLoginConfig.is_captcha">
                    <u-icon
                        class="mr-2"
                        :size="36"
                        name="/static/images/icon/icon_code.png"
                    />
                    <u-input
                        class="flex-1"
                        v-model="formData.captcha"
                        placeholder="图形验证码"
                        :border="false"
                    />
                    <view
                        class="pl-3 leading-4 ml-3 w-[180rpx]"
                        @click="getCaptchaFn"
                    >
                        <image
                            :src="captchaImage"
                            title="点击刷新"
                            alt="验证码"
                            class='w-full h-[50rpx]'
                        />
                    </view>
                </u-form-item>
                <u-form-item>
                    <u-icon
                        class="mr-2"
                        :size="36"
                        name="/static/images/icon/icon_password.png"
                    />
                    <u-input
                        class="flex-1"
                        v-model="formData.password"
                        type="password"
                        placeholder="请输入密码"
                        :border="false"
                    />
                    <router-navigate
                        to="/packages/pages/forget_pwd/forget_pwd?type=3"
                    >
                        <view
                            class="border-l border-solid border-0 border-light pl-3 text-muted leading-4 ml-3"
                        >
                            忘记密码？
                        </view>
                    </router-navigate>
                </u-form-item>
            </u-form>
        </view>

        <view class="mb-[40rpx]">
            <agreement ref="agreementRef" />
        </view>
        <u-button
            type="primary"
            shape="circle"
            hover-class="none"
            @click="handleLogin"
        >
            登 录
        </u-button>

        <view class="text-content flex justify-end mt-[40rpx]">
            <router-navigate to="/packages/pages/register/register?type=3">
                注册账号
            </router-navigate>
        </view>
    </view>
</template>
<script setup lang="ts">
import { reactive, shallowRef } from 'vue'
import { useAppStore } from '@/stores/app'
import useCaptchaEffect from '@/hooks/useCaptchaEffect'

const appStore = useAppStore()
const { captchaKey, captchaImage, getCaptchaFn } = useCaptchaEffect()

const emit = defineEmits<{
    (event: 'login', value: any): void
}>()
const agreementRef = shallowRef()

const formData = reactive({
    scene: 3,
    email: '',
    captcha: '',
    code: '',
    password: ''
})

const handleLogin = () => {
    if (!agreementRef.value?.checkAgreement()) {
        return
    }
    if (!formData.email) return uni.$u.toast('请输入邮箱号码')
    if (!formData.password) return uni.$u.toast('请输入密码')

    emit('login', {
        ...formData,
        key: captchaKey.value,
        captcha: formData.captcha
    })
    setTimeout(getCaptchaFn, 200)
}
</script>
