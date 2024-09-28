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
        class="h-full flex flex-col items-center justify-center"
        v-if="userInfo.token"
    >
        <u-avatar size="140" :src="userInfo.avatar"></u-avatar>
        <view class="my-[30rpx] text-xl">{{ userInfo.nickname }}</view>
        <view class="text-[36rpx]">微信授权成功</view>
    </view>
</template>

<script setup lang="ts">
import { OALogin } from '@/api/account'
import { ref } from 'vue'
// #ifdef H5
import wechatOa, { UrlScene } from '@/utils/wechat'
// #endif

import { onLoad } from '@dcloudio/uni-app'
import { useRoute } from 'uniapp-router-next'
// import { useRoute } from 'uniapp-router-next-zm'
// import { useUserStore } from '@/stores/user'
// const userStore = useUserStore()
const userInfo = ref<any>({})
const route = useRoute()
const oaLogin = async (options: any = { getUrl: true }) => {
    let data = null
    const { code, key = '', getUrl } = options

    if (code) {
        data = await OALogin({
            code,
            key
        })
        userInfo.value = data
    }
    if (getUrl) {
        await wechatOa.getUrl(UrlScene.PC_LOGIN)
    }
}

onLoad(() => {
    const options = route.query
    const authData = wechatOa.getAuthData()
    if (authData.code && authData.scene == UrlScene.PC_LOGIN) {
        try {
            oaLogin({ ...options, ...authData })
        } catch (error) {
        } finally {
            wechatOa.setAuthData()
        }
    } else if (options.is_pc && options.key) {
        oaLogin()
    }
})
</script>

<style lang="scss">
page {
    height: 100%;
    background-color: #fff;
}
</style>
