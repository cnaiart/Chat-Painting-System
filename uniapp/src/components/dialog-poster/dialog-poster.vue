<template>
    <l-painter
        ref="painterRef"
        :isCanvasToTempFilePath="false"
        :css="`width: 640rpx;`"
        custom-style="position: fixed; left: 200%;"
    >
        <l-painter-view
            :css="`
            border-radius: 14rpx;
            overflow: hidden;
            background-color: ${getPosterBgColor};
            `"
        >
            <l-painter-view
                :css="`
                display: block;
                left: 0;
                top: 0;
                position: absolute;
                width: 100%;
                z-index: -1;
                `"
            >
                <l-painter-image :src="posterBg" css="width: 100%;" />
            </l-painter-view>
            <!--  主要视图  -->
            <l-painter-view
                :css="`
                display: block;
                box-sizing: border-box;
                border-radius: 14rpx;
                width: 100%;
                height: 100%;
                padding: 0 40rpx;
            `"
            >
                <!--  中部内容区域  -->
                <l-painter-view
                    :css="`
                    box-sizing: border-box;
                    padding: 20rpx;
                    border-radius: 14rpx;
                    background: #fff;
                    margin-top: 240rpx;
                `"
                >
                    <l-painter-text
                        :text="state?.title"
                        :css="`
                        box-sizing: border-box;
                        display: block;
                        color: #101010;
                        font-size: 34rpx;
                        font-weight: 500;
                        padding-bottom: 20rpx;
                        line-clamp: 2;
                    `"
                    />
                    <l-painter-text
                        :text="state?.content"
                        :css="`
                        box-sizing: border-box;
                        display: block;
                        line-height: 40rpx;
                        color: #333333;
                        font-size: 28rpx;
                        padding-bottom: 20rpx;
                        ${
                            state?.options?.showContentType == 1
                                ? `line-clamp: ${state?.options?.contentNum}`
                                : ''
                        }
                    `"
                    />
                </l-painter-view>

                <!--  底部信息区域  -->
                <l-painter-view
                    :css="`
                    display: block;
                    margin-top: 30rpx;
                    padding-bottom: 40rpx;
                `"
                >
                    <!--  个人信息区域  -->
                    <l-painter-view
                        :css="`display: inline-block; width: 380rpx; margin-top: 40rpx;`"
                    >
                        <l-painter-image
                            :src="userInfo?.avatar"
                            css="width: 110rpx; height: 110rpx; border-radius: 50%;display: inline-block;"
                        />
                        <l-painter-view
                            :css="`
                            width: 270rpx;
                            height: 110rpx;
                            vertical-align: middle;
                            margin-top: 18rpx;
                            padding-left: 20rpx;
                            display: inline-block;
                            box-sizing: border-box;
                       `"
                        >
                            <l-painter-text
                                :text="userInfo?.nickname"
                                :css="`color: ${state?.options?.textColor}; font-size: 26rpx;line-clamp:1;width: 200rpx;`"
                            />
                            <l-painter-text
                                v-if="state?.options?.showData"
                                :text="state?.options?.data"
                                :css="`color: ${state?.options?.textColor}; font-size: 26rpx;line-clamp:1;width: 200rpx;`"
                            />
                        </l-painter-view>
                    </l-painter-view>

                    <!--  扫码区域  -->
                    <l-painter-view
                        :css="`display: inline-block;width: 180rpx; text-align: center;`"
                    >
                        <!--  H5二维码  -->
                        <!-- #ifdef H5 || APP-PLUS -->
                        <l-painter-qrcode
                            :css="`
                            box-sizing: border-box;
                            width: 170rpx;
                            height: 170rpx;
                            padding: 10rpx;
                            border-radius: 8rpx;
                            background-color: #FFFFFF;
                        `"
                            :text="state.invite_link"
                        >
                        </l-painter-qrcode>
                        <!-- #endif -->
                        <!--  小程序二维码  -->
                        <!-- #ifdef MP -->
                        <l-painter-image
                            :src="state.mp_qr_code"
                            :css="`
                            box-sizing: border-box;
                            width: 170rpx;
                            height: 170rpx;
                            border-radius: 8rpx;
                            background-color: #FFFFFF;
                        `"
                        />
                        <!--  邀请文案  -->
                        <!-- #endif -->
                        <l-painter-text
                            text="长按识别二维码"
                            :css="`
                            display: block;
                            color: ${state?.options?.textColor}; 
                            font-size: 24rpx;
                            text-align: center;
                            margin-top: 4rpx;
                        `"
                        />
                    </l-painter-view>
                </l-painter-view>
            </l-painter-view>
        </l-painter-view>
    </l-painter>

    <!--  弹窗海报  -->
    <u-popup
        v-model="state.showPoster"
        mode="center"
        :closeable="true"
        closeIconColor="#FFFFFF"
        :safe-area-inset-bottom="true"
        :customStyle="{
            background: 'none'
        }"
    >
        <view class="pb-[30rpx]">
            <!-- #ifndef H5 -->
            <image
                style="width: 640rpx"
                mode="widthFix"
                :src="state.poster_url"
            ></image>
            <!-- #endif -->
            <!-- #ifdef H5 -->
            <img style="width: 640rpx" :src="state.poster_url" />
            <!-- #endif -->
            <view class="mt-[20rpx]">
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
    </u-popup>
</template>

<script lang="ts" setup>
import { nextTick, reactive, ref, computed } from 'vue'
import { useAppStore } from '@/stores/app'
import { getMnpQrCode } from '@/api/app'
import { getDecorate } from '@/api/shop'
import { useUserStore } from '@/stores/user'
import { storeToRefs } from 'pinia'
import appConfig from '@/config'
const { getImageUrl, config } = useAppStore()
const userStore = useUserStore()
const { userInfo } = storeToRefs(userStore)

const painterRef = ref()
const state = reactive<{
    showPoster: boolean
    title: string // 标题
    content: string // 内容
    mp_qr_code: string // 小程序码
    invite_link: string // H5海报邀请链接
    options: any // 海报装修参数
    poster_url: string // 最终合成的海报露肩
}>({
    showPoster: false,
    title: '',
    content: '',
    mp_qr_code: '',
    invite_link: '',
    options: {
        data: '邀请您前来体验',
        default: 1,
        poster: 1,
        defaultUrl1: '',
        defaultUrl2: '',
        posterUrl: '',
        showData: '1'
    },
    poster_url: ''
})

const posterBg = ref('')

const getPosterBg = () => {
    const data = state?.options
    if (data.default == 1 && data.poster == 1) {
        // 默认海报1
        posterBg.value = getImageUrl(data.defaultUrl1)
    } else if (data.default == 1 && data.poster == 2) {
        // 默认海报2
        posterBg.value = getImageUrl(state?.options.defaultUrl2)
    } else if (data.default == 2) {
        // 自定义海报
        posterBg.value = getImageUrl(data.posterUrl)
    }
}

const getPosterBgColor = computed(() => {
    const data = state?.options
    if (!data) {
        return ''
    }
    console.log(data.bgColor)
    return data.bgColor
})

const initPosterData = async (row: { title: string; content: string }) => {
    await uni.showLoading({
        title: '生成中'
    })
    try {
        // 如果已经有数据了就不重新请求
        if (!userInfo?.value?.sn) {
            await userStore.getUser()
        }
        const { data } = await getDecorate({ id: 6 })
        // console.log(data)
        // 获取小程序码  如果已经有数据了就不重新请求
        // #ifdef MP-WEIXIN
        if (!state.mp_qr_code) {
            const { result } = await getMnpQrCode({
                user_sn: userInfo.value.sn,
                page: 'pages/index/index'
            })
            state.mp_qr_code = result
        }
        // #endif
        // #ifdef H5
        const domain = window.origin
        state.invite_link = `${domain}/mobile/pages/index/index?user_sn=${userInfo.value.sn}`
        // #endif
        // #ifdef APP-PLUS
        let domain = appConfig.baseUrl
        if (domain.charAt(domain.length - 1) === '/') {
            domain = domain.slice(0, -1)
        }
        state.invite_link = `${domain}/mobile/pages/index/index?user_sn=${userInfo.value.sn}`
        // #endif
        state.title = row.title.replace(/\n/g, '')
        state.content = row.content
        // .replace(/\n/g, '')
        state.options = JSON.parse(data)[0]?.content
        getPosterBg()
        await nextTick()
        await handleDrawCanvas()
    } catch (error) {
        uni.$u.toast(error)
        uni.hideLoading()
        console.log('请求生产海报失败', error)
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

const handleDrawCanvas = async () => {
    try {
        // 生成图片
        painterRef.value?.canvasToTempFilePathSync({
            fileType: 'png',
            // 如果返回的是base64是无法使用 saveImageToPhotosAlbum，需要设置 pathType为url
            // #ifdef MP
            pathType: 'url',
            // #endif
            // #ifdef H5
            pathType: 'base64',
            // #endif
            quality: 1,
            success: (res: any) => {
                console.log('生产结果', res)
                uni.hideLoading()
                state.showPoster = true
                state.poster_url = res.tempFilePath
            },
            fail: (error: any) => {
                console.log(error)
                uni.hideLoading()
                uni.$u.toast('调用海报错误', error)
            }
        })
    } catch (error) {
        uni.hideLoading()
        uni.$u.toast('调用海报错误', error)
    }
}

defineExpose({ handleDrawCanvas, initPosterData })
</script>
