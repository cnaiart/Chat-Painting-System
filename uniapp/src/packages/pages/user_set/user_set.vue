<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="user-set p-[20rpx]">
        <view class="bg-white rounded-[14rpx]">
            <view class="header pt-[30rpx]">
                <view class="flex justify-center pb-5">
                    <avatar-upload
                        v-model="userInfo.avatar"
                        file-key="url"
                        :round="true"
                        @upload="uploadImg"
                    >
                        <template #footer>
                            <!-- #ifdef MP-WEIXIN -->
                            <view class="text-muted text-xs">同步微信头像</view>
                            <!-- #endif -->
                        </template>
                    </avatar-upload>
                </view>
            </view>
            <view class="px-[20rpx]">
                <!-- 用户ID -->
                <!--                <view-->
                <!--                    class="item text-nr flex justify-between"-->
                <!--                    @click="-->
                <!--                        ;(showUserName = true),-->
                <!--                            (newUsername = userInfo?.username)-->
                <!--                    "-->
                <!--                >-->
                <!--                    <view class="label">账号</view>-->
                <!--                    <view class="content">{{ userInfo?.account }}</view>-->
                <!--                    <u-icon name="arrow-right" size="22" color="#666"></u-icon>-->
                <!--                </view>-->

                <!-- 昵称 -->
                <view
                    class="item text-nr flex justify-between"
                    @click="
                        ;(showNickName = true),
                            (newNickname = userInfo?.nickname)
                    "
                >
                    <view class="label">昵称</view>
                    <view class="content">{{ userInfo?.nickname }}</view>
                    <u-icon name="arrow-right" size="22" color="#666"></u-icon>
                </view>

                <view
                    class="item text-nr flex justify-between"
                    @click="copy(userInfo?.sn)"
                >
                    <view class="label">用户ID</view>
                    <view class="content">{{ userInfo?.sn }}</view>
                    <u-icon name="arrow-right" size="22" color="#666"></u-icon>
                </view>

                <!-- 手机号 -->
                <view class="item text-nr flex justify-between">
                    <view class="label">手机号</view>
                    <view class="content">{{
                        userInfo?.mobile == ''
                            ? '未绑定手机号'
                            : userInfo?.mobile
                    }}</view>

                    <!-- #ifdef MP-WEIXIN -->
                    <u-button
                        open-type="getPhoneNumber"
                        @getphonenumber="getPhoneNumber"
                        type="primary"
                        shape="circle"
                        size="medium"
                        :plain="true"
                    >
                        {{
                            userInfo?.mobile == '' ? '绑定手机号' : '更换手机号'
                        }}
                    </u-button>
                    <!-- #endif -->
                    <!-- #ifndef MP-WEIXIN -->
                    <u-button
                        @click="showMobilePop = true"
                        size="mini"
                        type="primary"
                        shape="circle"
                        :plain="true"
                    >
                        {{
                            userInfo?.mobile == '' ? '绑定手机号' : '更换手机号'
                        }}
                    </u-button>
                    <!-- #endif -->
                </view>

                <!-- 注册时间 -->
                <view class="item text-nr flex justify-between">
                    <view class="label">注册时间</view>
                    <view class="content">{{ userInfo?.create_time }}</view>
                </view>
                <!-- 微信绑定 -->
                <!-- #ifndef APP-PLUS -->
                <view
                    class="item text-nr flex justify-between"
                    @click="handleBindwx"
                    v-if="isWeixin"
                >
                    <view class="label">微信绑定</view>
                    <view class="content"
                        >{{ userInfo?.has_auth == false ? '未绑定' : '已绑定' }}
                    </view>
                    <u-icon
                        name="arrow-right"
                        size="22"
                        color="#666"
                        v-if="userInfo?.has_auth == false"
                    ></u-icon>
                </view>
                <!-- #endif -->
            </view>
        </view>

        <!-- 登录密码 -->
        <view
            class="text-nr flex justify-between mt-[10rpx] px-[30rpx] bg-white h-[100rpx] items-center"
            @click="handlePwd"
        >
            <view class="">登录密码</view>
            <u-icon name="arrow-right" color="#666"></u-icon>
        </view>
        <!-- 注销账号 -->
        <view
            v-if="userInfo?.is_cancelled"
            class="text-nr flex justify-between mt-[10rpx] px-[30rpx] bg-white h-[100rpx] items-center"
            @click="handlecancel"
        >
            <view class="label">注销账号</view>
            <u-icon name="arrow-right" size="22" color="#666"></u-icon>
        </view>
        <!-- 退出登录 -->
        <view
            class="item text-nr flex justify-center items-center mt-[10rpx] px-[30rpx] bg-white h-[100rpx]"
            @click="handleloginout"
        >
            退出登录
        </view>

        <!-- 昵称修改组件 -->
        <u-popup
            v-model="showNickName"
            :closeable="true"
            mode="center"
            :maskCloseAble="false"
            border-radius="20"
        >
            <view class="px-[50rpx] py-[40rpx] bg-white" style="width: 85vw">
                <form @submit="changeNameConfirm">
                    <view class="mb-[70rpx] text-xl text-center">
                        修改昵称
                    </view>
                    <u-form-item borderBottom>
                        <input
                            class="nr h-[60rpx] w-full"
                            :value="userInfo.nickname"
                            name="nickname"
                            type="nickname"
                            placeholder="请输入昵称"
                        />
                    </u-form-item>
                    <view class="mt-[80rpx]">
                        <button
                            form-type="submit"
                            class="bg-primary rounded-full h-[80rpx] text-lg leading-[80rpx] text-btn-text"
                        >
                            确定
                        </button>
                    </view>
                </form>
            </view>
        </u-popup>

        <!-- 账号修改组件 -->
        <u-popup
            v-model="showUserName"
            :closeable="true"
            mode="center"
            border-radius="20"
        >
            <view class="px-[50rpx] py-[40rpx] bg-white" style="width: 85vw">
                <view class="mb-[70rpx] text-xl text-center">修改账号</view>
                <u-form-item borderBottom>
                    <u-input
                        class="flex-1"
                        v-model="newUsername"
                        placeholder="请输入账号"
                        :border="false"
                    />
                </u-form-item>
                <view class="mt-[80rpx]">
                    <u-button
                        @click="changeUserNameConfirm"
                        type="primary"
                        shape="circle"
                    >
                        确定
                    </u-button>
                </view>
            </view>
        </u-popup>

        <!-- 账号修改组件 -->
        <u-popup
            v-model="showMobilePop"
            :closeable="true"
            mode="center"
            border-radius="20"
        >
            <view class="px-[50rpx] py-[40rpx] bg-white" style="width: 85vw">
                <view class="mb-[70rpx] text-xl text-center">
                    修改手机号码
                </view>
                <u-form-item borderBottom>
                    <u-input
                        class="flex-1"
                        v-model="newMobile"
                        placeholder="请输入新的手机号码"
                        :border="false"
                    />
                </u-form-item>
                <u-form-item borderBottom>
                    <u-input
                        class="flex-1"
                        v-model="mobileCode"
                        placeholder="请输入验证码"
                        :border="false"
                    />
                    <view
                        class="border-l border-solid border-0 border-light pl-3 text-muted leading-4 ml-3 w-[180rpx]"
                        @click="sendSms"
                    >
                        <u-verification-code
                            ref="uCodeRef"
                            :seconds="60"
                            @change="codeChange"
                            change-text="x秒"
                        />
                        {{ codeTips }}
                    </view>
                </u-form-item>
                <view class="mt-[80rpx]">
                    <u-button
                        @click="changeCodeMobile"
                        type="primary"
                        shape="circle"
                    >
                        确定
                    </u-button>
                </view>
            </view>
        </u-popup>

        <!--    选择密码    -->
        <u-action-sheet
            :list="passwordList"
            v-model="showPassword"
            @click="handleSelectPwd"
            :safe-area-inset-bottom="true"
        >
        </u-action-sheet>
        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
    </view>
</template>

<script lang="ts" setup>
import { ref, shallowRef } from 'vue'
import { onShow, onLoad } from '@dcloudio/uni-app'
import {
    getUserInfo,
    userEdit,
    userBindMobile,
    userMnpMobile,
    apiBindwx,
    OaAuthBind
} from '@/api/user'
import { smsSend, uploadImage } from '@/api/app'
import { FieldType, SMSEnum } from '@/enums/appEnums'
import { useCopy } from '@/hooks/useCopy'
import router from '@/router'
import cache from '@/utils/cache'
import { isWeixinClient, getClient } from '@/utils/client'
import { ClientEnum } from '@/enums/appEnums'
import { useUserStore } from '@/stores/user'
// #ifdef H5
import wechatOa, { UrlScene } from '@/utils/wechat'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
// #endif

const { copy } = useCopy()
const userStore = useUserStore()
// 用户信息
const userInfo = ref<any>({})
// 用户信息的枚举
const fieldType = ref(FieldType.NONE)

//显示昵称弹窗
const showNickName = ref<boolean | null>(false)
//显示账户弹窗
const showUserName = ref<boolean | null>(false)
//显示性别选择弹窗
// 显示手机号验证码调整弹窗 非小程序才需要
const showMobilePop = ref<boolean | null>(false)
//登录密码
const showPassword = ref(false)
const passwordList = ref([
    {
        text: '修改密码'
    },
    {
        text: '忘记密码'
    }
])

//新昵称
const newNickname = ref<string>('')
//新账号
const newUsername = ref<string>('')
//新的手机号码
const newMobile = ref<string>('')

//修改手机验证码
const mobileCode = ref<string>('')
const codeTips = ref('')
const uCodeRef = shallowRef()

// 获取用户信息
const getUser = async (): Promise<void> => {
    userInfo.value = await getUserInfo()
}

// 获取验证码显示字段
const codeChange = (text: string) => {
    codeTips.value = text
}

// 发送验证码
const sendSms = async () => {
    if (!newMobile.value) return uni.$u.toast('请输入新的手机号码')
    if (uCodeRef.value?.canGetCode) {
        await smsSend({
            scene: userInfo.value.mobile
                ? SMSEnum.CHANGE_MOBILE
                : SMSEnum.BIND_MOBILE,
            mobile: newMobile.value
        })
        uni.$u.toast('发送成功')
        uCodeRef.value?.start()
    }
}

const uploadImg = async (file: string) => {
    uni.showLoading({
        title: '正在上传中...'
    })
    try {
        const res: any = await uploadImage(file)
        handleAvatarChange(res.uri)
        uni.hideLoading()
    } catch (error) {
        uni.hideLoading()
        uni.$u.toast(error)
    }
}
const handleAvatarChange = (value: string) => {
    fieldType.value = FieldType.AVATAR
    setUserInfoFun(value)
}

// 验证码修改手机号-非微信小程序
const changeCodeMobile = async () => {
    await userBindMobile({
        type: userInfo.value.mobile ? 'change' : 'bind',
        mobile: newMobile.value,
        code: mobileCode.value
    })
    uni.$u.toast('操作成功')
    showMobilePop.value = false
    getUser()
}

// 修改用户信息
const setUserInfoFun = async (value: string): Promise<void> => {
    await userEdit({
        field: fieldType.value,
        value: value
    })
    uni.$u.toast('操作成功')
    getUser()
}

// 修改用户账号
const changeUserNameConfirm = () => {
    if (newUsername.value == '') return uni.$u.toast('账号不能为空')
    if (newUsername.value.length > 10)
        return uni.$u.toast('账号长度不得超过十位数')

    fieldType.value = FieldType.USERNAME
    setUserInfoFun(newUsername.value)
    showUserName.value = false
}

// 修改用户昵称
const changeNameConfirm = async (e: any) => {
    newNickname.value = e.detail.value.nickname
    if (newNickname.value == '') return uni.$u.toast('昵称不能为空')
    if (newNickname.value.length > 10)
        return uni.$u.toast('昵称长度不得超过十位数')
    fieldType.value = FieldType.NICKNAME
    await setUserInfoFun(newNickname.value)

    showNickName.value = false
}

// 微信小程序 绑定｜｜修改用户手机号
const getPhoneNumber = async (e: any): Promise<void> => {
    const { encryptedData, iv, code } = e.detail
    const data = {
        code,
        encrypted_data: encryptedData,
        iv
    }
    if (encryptedData) {
        await userMnpMobile({
            ...data
        })
        uni.$u.toast('操作成功')
        getUser()
    }
}
//注销账号
const handlecancel = () => {
    router.navigateTo('/packages/pages/cancelaccount/cancelaccount')
}

//登录密码
const handlePwd = () => {
    if (!userInfo.value.has_password)
        return router.navigateTo(
            '/packages/pages/change_password/change_password?type=set'
        )
    showPassword.value = true
}

// 去修改/忘记密码
const handleSelectPwd = (index: number) => {
    switch (index) {
        case 0:
            router.navigateTo('/packages/pages/change_password/change_password')
            break
        case 1:
            router.navigateTo(
                `/packages/pages/forget_pwd/forget_pwd?type=${
                    userInfo.value.mobile ? 2 : 3
                }`
            )
            break
    }
}

//退出登陆
const handleloginout = () => {
    userStore.logout()
    router.reLaunch('/pages/index/index')
}
//绑定微信
const isWeixin = ref(true)
// // #ifdef H5
isWeixin.value = isWeixinClient()
// // #endif
const handleBindwx = async () => {
    if (userInfo?.value.has_auth) return
    uni.showLoading({
        title: '请稍后...'
    })
    try {
        if (getClient() == ClientEnum.MP_WEIXIN) {
            const { code }: any = await uni.login({
                provider: 'weixin'
            })
            const data = await apiBindwx({
                code
            })
        } else if (getClient() == ClientEnum.OA_WEIXIN) {
            wechatOa.getUrl(UrlScene.BIND_WX)
        }
    } catch (error: any) {
        uni.$u.toast(error)
    } finally {
        uni.hideLoading()
    }
}
onShow(async () => {
    getUser()
})

onLoad(async (options) => {
    // #ifdef H5
    const { code, scene } = wechatOa.getAuthData()

    if (code.length && scene === UrlScene.BIND_WX) {
        uni.showLoading({
            title: '请稍后...'
        })
        cache.set('code', code)
        //用于清空code
        try {
            await OaAuthBind({ code })
        } catch (error: any) {
        } finally {
            uni.hideLoading()
            wechatOa.setAuthData()
        }
    }
    // #endif
})

// onUnload(() => { })
</script>

<style lang="scss">
.header {
    width: 100%;

    image {
        width: 120rpx;
        height: 120rpx;
        border-radius: 50%;
    }
}

.item {
    padding: 0 10rpx;
    height: 100rpx;
    background-color: #ffffff;
    align-items: center;

    &:not(:last-of-type) {
        border-bottom: 1px solid $u-light-color;
    }

    .label {
        width: 150rpx;
    }

    .content {
        flex: 1;
        width: 80%;
    }
}
</style>
