<template>
    <page-meta :page-style="$theme.pageStyle"> </page-meta>
    <view class="bg-white h-full flex flex-col">
        <view
            class="h-[100rpx] px-[20rpx] flex items-center border-0 border-b border-solid border-light"
        >
            <u-icon
                name="list-dot"
                size="40"
                @click="showControlPopup = true"
            />
            <view
                v-if="!showHistory"
                class="px-[24rpx] py-[12rpx] text-muted"
                @click="handleShowHistory"
            >
                <u-icon name="clock" size="32" />
                <span class="mx-[10rpx]">生成记录</span>
            </view>
            <view
                v-else
                class="px-[24rpx] py-[12rpx] flex-1 text-center text-xl"
                @click="handleShowHistory"
            >
                <span class="mx-[10rpx]">生成记录</span>
            </view>
            <view
                class="ml-auto"
                @click="exportFile"
                v-if="descInput && !showHistory"
            >
                <view class="bg-page rounded-md px-[24rpx] py-[12rpx]">
                    <u-icon name="download" size="32" />
                    <span class="mx-[10rpx]">导出文件</span>
                    <u-icon name="arrow-down" />
                </view>
            </view>
        </view>
        <view class="flex-1 min-h-0 relative" v-if="!showHistory">
            <div class="w-full h-full" ref="svgWrapRef">
                <svg ref="svgRef" class="w-full h-full"></svg>
            </div>
            <view
                v-if="!descInput.length"
                class="absolute inset-0 flex flex-col justify-center items-center bg-white pb-[100rpx]"
            >
                <image
                    class="w-[300rpx] h-[300rpx]"
                    src="@/packages/static/empty/create_record.png"
                    alt=""
                />
                <view class="my-[32rpx] font-medium"> 生成结果会在显示这 </view>

                <view class="text-content text-sm px-[100rpx]">
                    在左侧填好必要的信息，
                    点击【生成】按钮，静待生成结果，一般在30秒内搞定
                </view>

                <view class="mt-[40rpx]">
                    <u-button
                        type="primary"
                        :custom-style="{ width: '460rpx' }"
                        shape="circle"
                        @click="showControlPopup = true"
                    >
                        点击填写
                    </u-button>
                </view>
            </view>
        </view>
        <view class="flex-1 min-h-0" v-else>
            <MindMapHistory
                @view="handlePreview"
                @history="handleShowHistory"
            />
        </view>
        <u-action-sheet
            :list="exportOptions"
            @click="handleExport"
            v-model="showExportPopup"
        ></u-action-sheet>
        <ControlPopup
            ref="controlPopupRef"
            v-model:show="showControlPopup"
            @update="descUpdate"
        />
        <u-popup
            v-model="exportImgState.show"
            closeable
            mode="center"
            border-radius="14"
        >
            <view
                v-if="client != 'isApp'"
                class="text-center py-[20rpx] text-xl font-bold"
                >长按保存图片</view
            >
            <!-- <view
                v-if="client == 'isApp'"
                class="text-center py-[20rpx] text-xl font-bold"
                >预览图片后长按保存</view
            > -->
            <view class="w-[680rpx]">
                <img
                    @click="onPreview"
                    class="w-full"
                    :src="exportImgState.url"
                    alt=""
                />
            </view>
            <view class="px-[30rpx] pb-[10rpx]" v-if="client == 'isApp'">
                <u-button
                    type="primary"
                    shape="circle"
                    size="default"
                    :customStyle="{
                        padding: '0 30rpx',
                        height: '82rpx'
                    }"
                    @click="appSave"
                >
                    保存图片到相册
                </u-button>
            </view>
        </u-popup>
    </view>
</template>

<script setup lang="ts">
import { watchThrottled } from '@vueuse/core'
import { nextTick, onMounted, ref, shallowRef } from 'vue'
import { Transformer } from 'markmap-lib'
import { Markmap } from 'markmap-view'
import ControlPopup from './components/control-popup.vue'
import MindMapHistory from './components/mind-map-history.vue'
// import { downloadHtml2Image } from '@/utils/download'
import { isMiniProgram } from '@/utils/env'
import { useUserStore } from '@/stores/user'
import wechat from '@/utils/wechat'
// import { useRouter } from 'uniapp-router-next-zm'
import { useRouter } from 'uniapp-router-next'
import { html2Image } from '@/utils/html2canvas'
import { reactive } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { base64ToPath } from '@/utils/imgPath.js'
import '@dcloudio/uni-webview-js'

const userStore = useUserStore()
const transformer = new Transformer()
const showControlPopup = ref(false)
const showExportPopup = ref(false)
const showHistory = ref(false)
const descInput = ref('')
const svgRef = shallowRef<SVGElement>()
const svgWrapRef = shallowRef<HTMLDivElement>()
const controlPopupRef = shallowRef<InstanceType<typeof ControlPopup>>()
const router = useRouter()
const client = ref('')
let markmap: Markmap | null = null

const renderMarkMap = () => {
    const { root } = transformer.transform(descInput.value)
    markmap?.setData(root)
    markmap?.fit()
}

const exportOptions = ref([
    {
        text: '导出HTML',
        key: 'html'
    },
    {
        text: '导出PNG',
        key: 'png'
    },
    {
        text: '导出JPG',
        key: 'jpg'
    }
])

const exportFile = () => {
    // if (client.value == 'isApp') {
    //     uni.$u.toast('app端暂不支持导出文件')
    //     return
    // }
    showExportPopup.value = true
}
const appImgBase64 = ref('')
const exportImgState = reactive({
    url: '',
    show: false
})
const handleShowHistory = async () => {
    if (!userStore.isLogin) return toLogin()
    showHistory.value = !showHistory.value

    await nextTick()
    if (!showHistory.value) {
        createMarkmap()
        renderMarkMap()
    }
}

const toLogin = () => {
    if (isMiniProgram) {
        return wechat.miniProgram.navigateTo({ url: '/pages/login/login' })
    } else {
        return router.navigateTo({ path: '/pages/login/login' })
    }
}
const handleExport = (index: number) => {
    const key = exportOptions.value[index].key
    switch (key) {
        case 'html':
            if (isMiniProgram) {
                uni.$u.toast('小程序端暂不支持导出html文件')
            } else if (client.value == 'isApp') {
                uni.$u.toast('app端暂不支持导出html文件')
            } else {
                exportHtml()
            }

            break
        case 'png':
            exportImg('png')
            break
        case 'jpg':
            exportImg('jpeg')
            break
    }
}

const exportHtml = () => {
    const html = `<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,target-densitydpi=high-dpi,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <style>*{margin: 0;padding:0}html,body,.markmap{width: 100%;height:100%;overflow: hidden;}</style>
  <body>
    ${svgWrapRef.value?.innerHTML}
  </body>
</html>
`
    const blob = new Blob([html], { type: 'text/html' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = 'markmap.html'
    link.click()
    URL.revokeObjectURL(url)
}

const exportImg = async (type: 'png' | 'jpeg') => {
    const data = await html2Image(
        svgWrapRef.value!,
        { type },
        {
            backgroundColor: '#fff',
            scale: window.devicePixelRatio * 1.5
        }
    )
    exportImgState.show = true
    if (client.value == 'isApp') {
        exportImgState.url = await base64ToPath(data)
        appImgBase64.value = data
    } else {
        exportImgState.url = data
    }
}

//app保存
const appSave = () => {
    uni.webView.postMessage({
        data: {
            base64: appImgBase64.value
        }
    })
}

//预览图片
const onPreview = () => {
    uni.previewImage({
        current: 0,
        urls: [exportImgState.url],
        success: () => {
            exportImgState.show = false
        }
    })
}

const handlePreview = async (value: string) => {
    showHistory.value = false
    controlPopupRef.value?.changDescInput(value)
    await nextTick()
    createMarkmap()
    renderMarkMap()
}

const descUpdate = async (value: string) => {
    showHistory.value = false
    await nextTick()
    descInput.value = value
}

const createMarkmap = () => {
    markmap?.destroy()
    if (svgRef.value) {
        markmap = Markmap.create(svgRef.value)
    }
}

onLoad((options: any) => {
    client.value = options.client
})

onMounted(() => {
    createMarkmap()
})

watchThrottled(
    descInput,
    () => {
        renderMarkMap()
    },
    {
        throttle: 500
    }
)
</script>
<style lang="scss">
page {
    height: 100%;
    overflow: hidden;
}
</style>
