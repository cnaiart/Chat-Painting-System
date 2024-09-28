<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <page-status :status="state.status">
        <template #default>
            <view class="flex justify-center">
                <!--  海报  -->
                <view
                    class="w-[540rpx] h-[900rpx] bg-white rounded-lg mt-[20rpx]"
                >
                    <u-image width="540" height="900" :src="state.poster_url">
                        <u-loading slot="loading"></u-loading>
                        <template #loading>
                            <u-loading></u-loading>
                        </template>
                    </u-image>
                </view>
            </view>
            <!--  邀请说明  -->
            <view
                v-if="state?.rule_desc?.enabled"
                class="bg-white rounded-lg m-[30rpx] p-[20rpx]"
            >
                <view class="font-medium">
                    {{ state?.rule_desc?.name }}
                </view>
                <view class="mt-[20rpx]">
                    {{ state?.rule_desc?.data }}
                </view>
            </view>
            <!--  保存按钮  -->
            <view class="container-bottom bg-white">
                <view class="mx-[30rpx] my-[30rpx]">
                    <u-button
                        type="primary"
                        shape="circle"
                        size="default"
                        :customStyle="{
                            padding: '0 30rpx',
                            height: '82rpx'
                        }"
                        @click="handleSave"
                    >
                        <!-- #ifndef H5 -->
                        保存图片到相册
                        <!-- #endif -->
                        <!-- #ifdef H5 -->
                        长按保存图片到相册
                        <!-- #endif -->
                    </u-button>
                </view>
            </view>
        </template>
        <template #error>
            <u-empty
                font-size="28"
                icon-size="300"
                :text="state.status_text"
                :src="EmptyErrorImage"
            ></u-empty>
        </template>
    </page-status>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->

    <InvitePoster
        ref="posterRef"
        :options="state.poster_params"
        @success="
            (url) => {
                state.poster_url = url
            }
        "
    ></InvitePoster>
</template>

<script lang="ts" setup>
import { getDecorate } from '@/api/shop'
import { nextTick, onMounted, reactive, shallowRef } from 'vue'
import { PageStatusEnum } from '@/enums/appEnums'
import { useUserStore } from '@/stores/user'
import { storeToRefs } from 'pinia'

import EmptyErrorImage from '@/packages/static/empty/error.png'
import InvitePoster from '@/packages/pages/invite_poster/component/invite-poster.vue'
import UEmpty from '@/uni_modules/vk-uview-ui/components/u-empty/u-empty.vue'
import { onPullDownRefresh } from '@dcloudio/uni-app'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const userStore = useUserStore()
const { userInfo } = storeToRefs(userStore)

const posterRef = shallowRef()
const state = reactive<{
    status: string // 页面状态
    status_text: string // 页面状态描述
    poster_params: any // 海报装修数据
    rule_desc: any // 规则描述
    poster_url: string // 海报链接
}>({
    status: PageStatusEnum.LOADING,
    status_text: '服务器请求错误，请稍后再试',
    poster_params: {
        code: { x: '', y: '' },
        data: {
            content: '邀请您前来体验',
            x: '',
            y: ''
        },
        default: 1,
        defaultUrl1: '',
        defaultUrl2: '',
        poster: 1,
        posterUrl: '',
        showData: 1
    },
    rule_desc: {
        enabled: 1,
        name: '邀请说明',
        data: ''
    },
    poster_url: ''
})

const getData = async () => {
    await uni.showLoading({
        title: '生成中'
    })
    try {
        // 海报装修
        const data = await getDecorate({ id: 5 })
        // 装修数据
        const arrayData = JSON.parse(data.data)
        state.poster_params = arrayData[0]?.content || {}
        state.rule_desc = arrayData[1]?.content || {}
        await nextTick()
        // 页面状态
        state.status = PageStatusEnum.NORMAL
        // 绘画
        await posterRef.value.initPosterData()
    } catch (error) {
        uni.hideLoading()
        state.status_text = error as any
        state.status = PageStatusEnum.ERROR
        console.log('获取邀请海报装修失败', error)
    }
}

const handleSave = () => {
    // #ifndef H5
    uni.saveImageToPhotosAlbum({
        filePath: state.poster_url,
        success: () => {
            uni.$u.toast('保存成功')
        },
        fail: (err) => {
            uni.$u.toast('保存失败')
            console.log(err)
        }
    })
    // #endif
    // #ifdef H5
    uni.$u.toast('请长按图片保存')
    // #endif
}

onMounted(async () => {
    await getData()
})

onPullDownRefresh(async () => {
    getData()
    uni.stopPullDownRefresh()
})
</script>

<style lang="scss" scoped>
.container-bottom {
    position: fixed;
    bottom: 0;
    left: 0rpx;
    right: 0rpx;
    padding-bottom: env(safe-area-inset-bottom);
}
</style>
