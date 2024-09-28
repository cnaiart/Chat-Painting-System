<template>
    <l-painter
        ref="painterRef"
        :isCanvasToTempFilePath="false"
        custom-style="position: fixed; left: 200%;"
    >
        <l-painter-view
            :css="`
            border-radius: 14rpx;
            overflow: hidden;
            background-color: #F2F3F6;
            `"
        >
            <l-painter-view
                :css="`
                width: 100%;
                `"
            >
                <l-painter-image
                    :src="imgContent?.thumbnail || imgContent?.image"
                    css="width: 100%;object-fit:contain;"
                />
            </l-painter-view>

            <l-painter-view
                css="margin-top: 20rpx;padding-left: 20rpx;padding-right: 20rpx;margin-bottom: 20rpx;"
            >
                <l-painter-view>
                    <l-painter-text
                        css="display: block;font-weight: bold;font-size: 32rpx;text-align: left;color: #101010;lineClamp: 2;"
                        :text="imgContent?.original_prompts?.prompt"
                    />
                </l-painter-view>
                <l-painter-view>
                    <l-painter-text
                        css="font-size: 28rpx;text-align: left;color: #101010;margin-top:20rpx;lineClamp: 1;"
                        :text="imgContent?.original_prompts?.prompt_en"
                    />
                </l-painter-view>
            </l-painter-view>

            <l-painter-view
                css="height:10rpx;border-top-width:1px;border-top-style:dashed;border-top-color:#D6D3D3;"
            >
            </l-painter-view>

            <l-painter-view>
                <l-painter-view
                    css="margin-top: 20rpx;height: 220rpx; padding-left: 20rpx;padding-right: 20rpx;margin-bottom: 20rpx;display:block;width:60%"
                >
                    <l-painter-view css="display:flex;align-items: center;">
                        <l-painter-image
                            css="width:68rpx;height:68rpx;border-radius: 50%;"
                            :src="userStore.userInfo.avatar"
                        />
                        <l-painter-text
                            css=";font-size: 28rpx;text-align: left;color: #666666;margin-left: 20rpx;"
                            :text="userStore.userInfo.nickname"
                        />
                    </l-painter-view>
                    <l-painter-view css="margin-top:20rpx">
                        <l-painter-text
                            css="font-weight: bold;font-size: 32rpx;text-align: left;color: #101010;"
                            :text="appStore.getWebsiteConfig.shop_name"
                        />
                    </l-painter-view>
                    <l-painter-view css="margin-top:20rpx;margin-bottom:20rpx">
                        <l-painter-text
                            :css="`font-weight: bold;font-size: 30rpx;text-align: left;color: ${primaryColor};`"
                            :text="splitDomain(appStore.config.current_domain)"
                        />
                    </l-painter-view>
                </l-painter-view>

                <l-painter-view
                    css="position: absolute;top:20rpx;right:20px;background-color:#fff;padding:15rpx;border-radius: 10rpx;"
                >
                    <!--  H5二维码  -->
                    <!-- #ifdef H5 || APP-PLUS -->
                    <l-painter-qrcode
                        :text="QRCodeLink"
                        css="width: 180rpx; height: 180rpx"
                    />
                    <!-- #endif -->
                    <!--  小程序二维码  -->
                    <!-- #ifdef MP -->
                    <l-painter-image
                        :src="wxQRCode"
                        css="width: 180rpx; height: 180rpx"
                    />
                    <!-- #endif -->
                </l-painter-view>
            </l-painter-view>
        </l-painter-view>
    </l-painter>
    <u-popup
        v-model="showPoster"
        mode="bottom"
        :closeable="true"
        closeIconColor="#000"
        :safe-area-inset-bottom="true"
        height="90%"
        border-radius="14"
        :customStyle="{
            background: 'none'
        }"
        @close="$emit('close')"
    >
        <view class="flex flex-col relative h-full">
            <view class="title text-xl font-medium text-center py-[30rpx]">
                生成海报
            </view>
            <view class="mb-[120rpx] flex-1 min-h-0">
                <scroll-view scroll-y="true" class="h-full">
                    <view class="px-[20rpx]">
                        <!-- #ifndef H5 -->
                        <image
                            class="w-full"
                            mode="widthFix"
                            :src="imgSrc"
                        ></image>
                        <!-- #endif -->
                        <!-- #ifdef H5 -->
                        <img class="w-full" :src="imgSrc" />
                        <!-- #endif -->
                    </view>
                </scroll-view>
            </view>
            <view
                class="w-full absolute bottom-0 px-[30rpx] py-[20rpx] flex justify-center"
            >
                <!-- #ifdef H5 -->
                <u-button
                    class="flex-1 mr-2"
                    type="primary"
                    shape="circle"
                    size="default"
                    plain
                    :customStyle="{
                        padding: '0 30rpx',
                        height: '82rpx'
                    }"
                    @click="clickCopy"
                >
                    复制链接
                </u-button>
                <!-- #endif -->
                <u-button
                    type="primary"
                    class="flex-1"
                    shape="circle"
                    size="default"
                    :customStyle="{
                        padding: '0 30rpx',
                        height: '82rpx'
                    }"
                    @click="handleSave"
                >
                    <!-- #ifndef H5 -->
                    下载海报
                    <!-- #endif -->
                    <!-- #ifdef H5 -->
                    长按保存图片到相册
                    <!-- #endif -->
                </u-button>
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import img from '@/packages/static/images/drawing/error.png'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'
import { getMnpQrCode } from '@/api/app'
import { useCopy } from '@/hooks/useCopy'
import { splitDomain } from '@/utils/util'
import { generateSharePath } from '@/mixins/share'
import { useThemeStore } from '@/stores/theme'
const { copy } = useCopy()

const emits = defineEmits(['close'])

const appStore = useAppStore()
const userStore = useUserStore()
const { primaryColor } = useThemeStore()

//弹框显示/隐藏
const showPoster = ref(false)
//海报ref
const painterRef = ref()
//图片内容
const imgContent: any = ref({})
//微信小程序二维码
const wxQRCode: any = ref('')
//非微信小程序二维码链接
const QRCodeLink = ref('')
//生成的图片
const imgSrc = ref('')

const open = (row: any) => {
    showPoster.value = true
    imgContent.value = row
    initPosterData()
}

const initPosterData = async () => {
    uni.showLoading({
        title: '生成中'
    })
    try {
        // #ifdef MP-WEIXIN
        const { result } = await getMnpQrCode({
            user_sn: userStore.userInfo.sn,
            page: 'pages/index/index'
        })
        wxQRCode.value = result
        // #endif

        let domain
        // #ifdef H5
        // domain = window.origin
        // QRCodeLink.value = `${domain}/mobile/pages/index/index?user_sn=${userStore.userInfo.sn}`
        // #endif
        // #ifdef APP-PLUS
        // domain = import.meta.env.VITE_APP_BASE_URL + ''
        // if (domain.charAt(domain.length - 1) === '/') {
        //     domain = domain.slice(0, -1)
        // }
        // QRCodeLink.value = `${domain}/mobile/pages/index/index?user_sn=${userStore.userInfo.sn}`
        // #endif

        // #ifndef MP-WEIXIN
        QRCodeLink.value = await generateSharePath()
        // #endif
        setTimeout(() => {
            handleDrawCanvas()
        }, 500)
    } catch (error) {
        uni.hideLoading()
        console.log(error)
    }
}

//复制
const clickCopy = async () => {
    copy(await generateSharePath())
}

const handleDrawCanvas = async () => {
    // 生成图片
    painterRef.value?.canvasToTempFilePathSync({
        fileType: 'png',
        // 如果返回的是base64是无法使用 saveImageToPhotosAlbum，需要设置 pathType为url
        quality: 1,
        // #ifdef MP
        pathType: 'url',
        // #endif
        // #ifdef H5
        pathType: 'base64',
        // #endif
        success: (res: any) => {
            // console.log('生产结果', res)
            uni.hideLoading()
            imgSrc.value = res.tempFilePath
        },
        fail: (error: any) => {
            console.log(error)
            uni.hideLoading()
            uni.$u.toast('调用海报错误', error)
        }
    })
}

const handleSave = () => {
    // #ifndef H5
    uni.saveImageToPhotosAlbum({
        filePath: imgSrc.value,
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

defineExpose({
    open
})
</script>

<style scoped lang="scss"></style>
