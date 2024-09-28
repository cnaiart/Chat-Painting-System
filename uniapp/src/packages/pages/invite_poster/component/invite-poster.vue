<template>
    <l-painter
        ref="painterRef"
        :isCanvasToTempFilePath="false"
        :after-delay="300"
        :css="`width: 540rpx; background-image: url(${getPosterBg});`"
        custom-style="position: fixed; left: 200%; height: 900rpx;"
    >
        <!--  主要视图  -->
        <l-painter-view
            :css="`
                position: relative;
                display: block;
                box-sizing: border-box;
                border-radius: 14rpx;
                width: 100%;
                height: 900rpx;
            `"
        >
            <!--  H5二维码  -->
            <!-- #ifdef H5 || APP-PLUS -->
            <l-painter-qrcode
                :css="`
                    box-sizing: border-box;
                    position: absolute;
                    z-index: 10;
                    top: ${options?.code?.y * 2}rpx;
                    left:${options?.code?.x * 2}rpx;
                    width: 200rpx;
                    height: 200rpx;
                    padding: 10rpx;
                    background-color: #FFFFFF;
                `"
                :text="state.link"
            >
            </l-painter-qrcode>
            <!--  #endif -->
            <!-- #ifdef MP -->
            <!--  小程序二维码  -->
            <l-painter-image
                :src="state.qrcode"
                :css="`
                    box-sizing: border-box;
                    position: absolute;
                    z-index: 10;
                    top: ${options?.code?.y * 2}rpx;
                    left:${options?.code?.x * 2}rpx;
                    width: 200rpx;
                    height: 200rpx;
                    background-color: #FFFFFF;
                `"
            />
            <!--  #endif -->
            <!--  邀请文案  -->
            <l-painter-text
                v-if="options.showData"
                :text="options?.data?.content"
                :css="`
                    position: absolute;
                    z-index: 10;
                    display: block;
                    color: #FFFFFF;
                    font-size: 28rpx;
                    top: ${options?.data?.y * 2}rpx;
                    left:${options?.data?.x * 2}rpx;
                `"
            />
        </l-painter-view>
    </l-painter>
</template>

<script lang="ts" setup>
import { computed, nextTick, reactive, ref } from 'vue'
import { getMnpQrCode } from '@/api/app'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'
import { storeToRefs } from 'pinia'
import appConfig from '@/config'
const { getImageUrl, config } = useAppStore()
const userStore = useUserStore()
const { userInfo } = storeToRefs(userStore)

const painterRef = ref()

const emit = defineEmits<{
    (event: 'fail'): void
    (event: 'success', value: string): void
}>()

const props = withDefaults(
    defineProps<{
        options: any
    }>(),
    {
        options: {
            code: { x: '', y: '' },
            data: {
                content: '邀请您前来体验',
                x: '',
                y: ''
            },
            default: 1,
            defaultUrl1: '',
            defaultUrl2: '',
            posterUrl: '',
            showData: 0
        }
    }
)

const state = reactive({
    qrcode: '',
    link: ''
})

const getPosterBg = computed(() => {
    const data = props?.options
    if (data.default == 1 && data.poster == 1) {
        // 默认海报1
        return getImageUrl(data.defaultUrl1)
    } else if (data.default == 1 && data.poster == 2) {
        // 默认海报2
        return getImageUrl(props?.options.defaultUrl2)
    } else if (data.default == 2) {
        // 自定义海报
        return getImageUrl(data.posterUrl)
    }
})

const initPosterData = async () => {
    try {
        // 如果已经有数据了就不重新请求
        if (!userInfo?.value?.sn) {
            await userStore.getUser()
        }
        // #ifdef MP
        // 获取小程序码
        const { result } = await getMnpQrCode({
            user_sn: userInfo.value.sn,
            page: 'pages/index/index'
        })
        state.qrcode = result
        // #endif
        // #ifdef H5
        const domain = window.origin
        state.link = `${domain}/mobile/pages/index/index?user_sn=${userInfo.value.sn}`
        // #endif
        // #ifdef APP-PLUS
        let domain = appConfig.baseUrl
        if (domain.charAt(domain.length - 1) === '/') {
            domain = domain.slice(0, -1)
        }
        state.link = `${domain}/mobile/pages/index/index?user_sn=${userInfo.value.sn}`
        // #endif
        await nextTick()
        await drawCanvas()
    } catch (error) {
        console.log(error)
    }
}

const drawCanvas = () => {
    try {
        console.log('props', props)
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
                console.log('生成结果', res)
                uni.hideLoading()
                emit('success', res.tempFilePath)
            },
            fail: (error: any) => {
                console.log(error)
                uni.hideLoading()
                uni.$u.toast('调用海报错误', error)
            }
        })
    } catch (error) {
        uni.hideLoading()
        uni.$u.toast('调用海报错误')
    }
}

defineExpose({ drawCanvas, initPosterData })
</script>
