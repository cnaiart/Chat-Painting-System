<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="h-full flex flex-col">
        <view class="flex flex-col items-center pt-[160rpx] pb-[40rpx]">
            <image
                :src="appStore.getWebsiteConfig.shop_logo"
                mode=""
                class="w-[160rpx] h-[160rpx]"
            ></image>
            <view class="text-[32rpx] font-medium my-[30rpx]">
                {{ appStore.getWebsiteConfig.shop_name }}
            </view>
            <view class="w-full px-[75rpx] pt-[60rpx]">
                <template v-if="isWeixinLogin">
                    <weixin @login="wxLoginLock" :loading="isLock" />
                </template>
                <template v-if="isMobileLogin">
                    <mobile @login="mobileLoginLock" />
                </template>
                <template v-if="isMailboxLogin">
                    <mailbox @login="mobileLoginLock" />
                </template>
            </view>
        </view>
        <view
            class="p-[40rpx]"
            :class="{
                'mt-[140rpx]': isWeixinLogin
            }"
            v-if="showOtherWayBtn"
        >
            <u-divider>其他登录方式</u-divider>
            <div class="flex justify-around mt-[40rpx] px-[80rpx]">
                <div
                    class="flex flex-col items-center"
                    v-if="hasWeixinLogin && !isWeixinLogin"
                    @click="changeLoginWay(LoginWayEnum.WEIXIN)"
                >
                    <u-icon name="/static/images/icon/icon_wx.png" :size="80" />
                    <div class="text-sm mt-[10px]">微信登录</div>
                </div>
                <div
                    class="flex flex-col items-center"
                    v-if="hasMobileLogin && !isMobileLogin"
                    @click="changeLoginWay(LoginWayEnum.MOBILE)"
                >
                    <u-icon
                        name="/static/images/icon/icon_phone.png"
                        :size="80"
                    />
                    <div class="text-sm mt-[10px]">手机号登录</div>
                </div>
                <div
                    class="flex flex-col items-center"
                    v-if="hasMailboxLogin && !isMailboxLogin"
                    @click="changeLoginWay(LoginWayEnum.MAILBOX)"
                >
                    <u-icon
                        name="/static/images/icon/icon_email.png"
                        :size="80"
                    />
                    <div class="text-sm mt-[10px]">邮箱登录</div>
                </div>
            </div>
            <!-- <agreement /> -->
        </view>

        <update-user-info
            v-model:show="showLoginPopup"
            :logo="websiteConfig.shop_logo"
            :title="websiteConfig.shop_name"
            :userInfo="loginData"
            @update="handleUpdateUser"
        />
        <bind-mobile
            v-model:show="showBindMobilePopup"
            :userInfo="loginData"
            @success="bindMobileSuccess"
            @close="removeWxQuery"
        />
    </view>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import { computed, ref, watch } from 'vue'
import weixin from './components/weixin.vue'
import mobile from './components/mobile.vue'
import mailbox from './components/mailbox.vue'
import updateUserInfo from './components/update-user-info.vue'
import BindMobile from './components/bind-mobile.vue'
// import bindMobile from './components/bind-mobile.vue'
import { useLockFn } from '@/hooks/useLockFn'
import {
    OALogin,
    login,
    mnpLogin as mnpLoginApi,
    updateUser,
    uninAppLogin
} from '@/api/account'
import { useUserStore } from '@/stores/user'
import { useRouter, useRoute } from 'uniapp-router-next'
// import { useRouter, useRoute } from 'uniapp-router-next-zm'
import { onLoad } from '@dcloudio/uni-app'
import cache from '@/utils/cache'
import { BACK_URL, SHARE_ID, USER_SN } from '@/enums/constantEnums'
import { series } from '@/utils/util'
import { bindInvite } from '@/api/task'
import { getClient } from '@/utils/client'
// #ifdef H5
import wechatOa, { UrlScene } from '@/utils/wechat'

// #endif

const appStore = useAppStore()
const userStore = useUserStore()
const router = useRouter()
const route = useRoute()
enum LoginWayEnum {
    WEIXIN = '1',
    MOBILE = '2',
    MAILBOX = '3'
}
const showLoginPopup = ref(false)
const showBindMobilePopup = ref(false)
const loginWay = ref<string | number>(0)
const isWeixinLogin = computed(() => loginWay.value == LoginWayEnum.WEIXIN)
const isMobileLogin = computed(() => loginWay.value == LoginWayEnum.MOBILE)
const isMailboxLogin = computed(() => loginWay.value == LoginWayEnum.MAILBOX)
const hasWeixinLogin = computed(() =>
    appStore.getLoginConfig.login_way.includes(LoginWayEnum.WEIXIN)
)
const hasMobileLogin = computed(() =>
    appStore.getLoginConfig.login_way.includes(LoginWayEnum.MOBILE)
)
const hasMailboxLogin = computed(() =>
    appStore.getLoginConfig.login_way.includes(LoginWayEnum.MAILBOX)
)
const showOtherWayBtn = computed(
    () => appStore.getLoginConfig.login_way?.length > 1
)
const websiteConfig = computed(() => appStore.getWebsiteConfig)
const changeLoginWay = (way: LoginWayEnum) => {
    loginWay.value = way
}
const loginData = ref<any>({})

const oaLogin = async (options: any = { getUrl: true }) => {
    const { code, getUrl } = options
    if (getUrl) {
        await wechatOa.getUrl(UrlScene.LOGIN)
    } else {
        const data = await OALogin({
            code
        })
        return data
    }
    return Promise.reject()
}

const mnpLogin = async () => {
    const { code }: any = await uni.login({
        provider: 'weixin'
    })
    const data = await mnpLoginApi({
        code
    })
    if (data.is_new_user) {
        //是新用户
        showLoginPopup.value = true
    }
    return data
}

const appLogin = async () => {
    return new Promise((resolve, reject) => {
        uni.login({
            provider: 'weixin',
            onlyAuthorize: true,
            success: async (res) => {
                //@ts-ignore
                const data = await uninAppLogin({
                    code: res.code,
                    terminal: getClient()
                })
                resolve(data)
            },
            fail: (err) => {
                reject(err)
            }
        })
    })
}

const checkIsBindMobile = async () => {
    if (!loginData.value.mobile && appStore.getLoginConfig.coerce_mobile) {
        showBindMobilePopup.value = true
    } else {
        loginHandle()
    }
}

const loginHandle = async () => {
    const { token } = loginData.value
    userStore.login(token)
    userStore.getUser()
    // #ifdef APP-PLUS
    router.navigateBack()
    // #endif
    // #ifndef APP-PLUS
    const pages = getCurrentPages()
    if (pages.length > 1) {
        const prevPage = pages[pages.length - 2]
        await router.navigateBack()
        // @ts-ignore
        const { onLoad, options } = prevPage
        // 刷新上一个页面
        onLoad && onLoad(options)
    } else if (cache.get(BACK_URL)) {
        try {
            router.redirectTo(cache.get(BACK_URL))
        } finally {
            router.switchTab(cache.get(BACK_URL))
        }
    } else {
        router.reLaunch('/pages/index/index')
    }
    cache.remove(BACK_URL)
    // #endif
}

const loginAfter = (() => {
    const bindUsers = async () => {
        const share_id = cache.get(SHARE_ID)
        const user_sn = cache.get(USER_SN)
        try {
            if (share_id || user_sn) {
                await bindInvite(
                    {
                        share_id,
                        user_sn
                    },
                    loginData.value.token
                )
                cache.remove(SHARE_ID)
                cache.remove(USER_SN)
            }
        } catch (error) {}
    }
    const updateUsers = async () => {
        if (loginData.value.is_new_user && !showLoginPopup.value) {
            try {
                await updateUser(
                    {
                        avatar: loginData.value.avatar,
                        nickname: loginData.value.nickname
                    },
                    { token: loginData.value.token }
                )
            } catch (error) {}
        } else if (showLoginPopup.value) {
            return Promise.reject()
        }
    }

    return series(bindUsers, updateUsers, checkIsBindMobile)
})()

const bindMobileSuccess = () => {
    showBindMobilePopup.value = false
    loginHandle()
}


const { lockFn: wxLoginLock, isLock } = useLockFn(async () => {
    let data: any = null

    // #ifdef H5
    data = await oaLogin()
    // #endif

    // #ifdef MP-WEIXIN
    data = await mnpLogin()
    // #endif

    // #ifdef APP-PLUS
    data = await appLogin()
    // #endif
    console.log(data)
    if (data) {
        loginData.value = data

        loginAfter()
    }
})

const { lockFn: mobileLoginLock } = useLockFn(async (formData: any) => {
    uni.showLoading({
        title: '请稍后...'
    })
    try {
        const data = await login(formData)
        loginData.value = data
        await loginAfter()
        uni.hideLoading()
    } catch (error: any) {
        uni.hideLoading()
        uni.$u.toast(error)
    }
})

const handleUpdateUser = async (value: any) => {
    await updateUser(value, { token: loginData.value.token })
    showLoginPopup.value = false
    checkIsBindMobile()
}

watch(
    () => appStore.getLoginConfig,
    (value) => {
        loginWay.value = value.default_login_way.toString()
        // if (value.login_way) {
        //     loginWay.value = value.login_way[0]
        // }
        // if (value.login_way.includes(LoginWayEnum.WEIXIN)) {
        //     loginWay.value = LoginWayEnum.WEIXIN
        // }
    },
    {
        immediate: true
    }
)

const removeWxQuery = () => {
    const options = route.query
    if (options.code && options.state) {
        delete options.code
        delete options.state
        router.redirectTo({ path: route.path, query: options })
    }
}

onLoad(async () => {
    //#ifdef H5
    const options = wechatOa.getAuthData()
    try {
        if (options.code && options.scene === UrlScene.LOGIN) {
            uni.showLoading({
                title: '请稍后...'
            })
            const data = await oaLogin(options)
            if (data) {
                loginData.value = data

                await loginAfter()
            }
        }
    } catch (error) {
        removeWxQuery()
    } finally {
        uni.hideLoading()
        //清除保存的授权数据
        wechatOa.setAuthData()
    }
    //#endif
})
</script>

<style lang="scss">
page {
    height: 100%;
    background-color: #fff;
}
</style>
