<template>
    <div class="success">
        <!--    头部操作    -->
        <div class="flex justify-between items-center">
            <div class="success__tag">绘画完成</div>
            <div class="flex items-center">
                <!-- #ifdef APP-PLUS -->
                <image
                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                    :src="IconCopy"
                    alt="复制"
                    @click="onCopy(value.prompt_desc)"
                />
                <image
                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                    :src="IconDownload"
                    alt="下载"
                    @click="onFileDownload(value)"
                />
                <image
                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                    :src="IconReload"
                    alt="重新生成"
                    @click="onReDrawing(value)"
                />

                <appSplitCom :imgValue="value">
                    <image
                        v-if="
                            !value.actions?.includes('low_variation') &&
                            value.actions?.length &&
                            !Array.isArray(value.image)
                        "
                        class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                        :src="IconCutout"
                        alt="切片"
                    />
                </appSplitCom>
                <image
                    v-if="appStore.getDrawSquareConfig.is_allow_share"
                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                    :src="IconShare"
                    alt="分享至广场"
                    @click="onShareSquare(value)"
                />
                <image
                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                    :src="IconDelete"
                    alt="删除"
                    @click="onDelete(value)"
                />
                <!-- #endif-->
                <!-- #ifndef APP-PLUS -->
                <img
                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                    :src="IconCopy"
                    alt="复制"
                    @click="onCopy(value.prompt_desc)"
                />
                <img
                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                    :src="IconDownload"
                    alt="下载"
                    @click="onFileDownload(value)"
                />
                <img
                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                    :src="IconReload"
                    alt="重新生成"
                    @click="onReDrawing(value)"
                />
                <img
                    v-if="
                        !value.actions?.includes('low_variation') &&
                        value.actions?.length
                    "
                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                    :src="IconCutout"
                    alt="切片"
                    @click="onSplitPic(value)"
                />
                <img
                    v-if="appStore.getDrawSquareConfig.is_allow_share"
                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                    :src="IconShare"
                    alt="分享至广场"
                    @click="onShareSquare(value)"
                />
                <img
                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                    :src="IconDelete"
                    alt="删除"
                    @click="onDelete(value)"
                />
                <!-- #endif-->
            </div>
        </div>
        <!--    中部图片    -->
        <div class="success__section relative text-sm mt-[15px]">
            <div class="image-bg">
                <Picture
                    :picture="value.image || value.image_url"
                    :lazy-img="value.thumbnail"
                    @share="onShareSquare(value, $event)"
                />
            </div>
            <div class="image-content">
                <p v-if="value.prompt_desc" class="text-white line-clamp-2">
                    {{ value.prompt }}
                </p>
            </div>
        </div>

        <!--    底部信息    -->
        <div class="mt-[20px] success__footer">
            <div class="h-[110rpx]" v-if="value.fail_reason && value.censor_status == 2">
                <p
                    class="line-clamp-2"
                    @click="copy(value.fail_reason)"
                >
                    {{ value.fail_reason }}
                </p>
            </div>
            <template v-else>
                <div>
                    <span>放大图片</span>
                    <template
                        v-if="
                            !value.actions?.includes('low_variation') &&
                            value?.actions?.length
                        "
                    >
                        <span class="opt-btn" @click="onEnlargement(value, 1)"
                            >左上</span
                        >
                        <span class="opt-btn" @click="onEnlargement(value, 2)"
                            >右上</span
                        >
                        <span class="opt-btn" @click="onEnlargement(value, 3)"
                            >左下</span
                        >
                        <span class="opt-btn" @click="onEnlargement(value, 4)"
                            >右下</span
                        >
                    </template>
                    <template v-else> - </template>
                </div>
                <div class="mt-[15px]">
                    <span>变体图片</span>
                    <template
                        v-if="
                            !value.actions?.includes('low_variation') &&
                            value.actions?.length
                        "
                    >
                        <span class="opt-btn" @click="onConversion(value, 1)"
                            >左上</span
                        >
                        <span class="opt-btn" @click="onConversion(value, 2)"
                            >右上</span
                        >
                        <span class="opt-btn" @click="onConversion(value, 3)"
                            >左下</span
                        >
                        <span class="opt-btn" @click="onConversion(value, 4)"
                            >右下</span
                        >
                    </template>
                    <template v-else> - </template>
                </div>
            </template>
            <div
                class="flex justify-between items-center mt-[15px] text-[#999999] text-base"
            >
                <span>时间：{{ value.create_time }}</span>
                <u-tag :text="value.draw_model" mode="light" size="mini" />
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { inject, nextTick, ref, shallowRef } from 'vue'

import ImageError from '@/packages/static/images/drawing/error.png'
import IconCopy from '@/static/images/common/icon_copy.png'
import IconDownload from '@/static/images/common/icon_download.png'
import IconCutout from '@/static/images/common/icon_cutout.png'
import IconReload from '@/static/images/common/icon_reload.png'
import IconShare from '@/static/images/common/icon_share.png'
import IconDelete from '@/static/images/common/icon_delete.png'

import { useSplit } from '../../../hooks/useSplit'
import { useAppStore } from '@/stores/app'
import { saveImageToPhotosAlbum } from '@/utils/file'
import { useCopy } from '@/hooks/useCopy'
//#ifdef APP-PLUS
import appSplitCom from '../../component/app-split-com.vue'
//#endif
import Picture from './picture.vue'

import type { DrawingFormType } from '@/api/drawing'
type DrawingHandlerType = (options?: {
    drawing?: DrawingFormType
    isClear?: boolean
}) => void

const pageIndex = inject<number>('pageIndex')
const drawForm = inject<DrawingFormType>('drawForm')
const deleteDrawing = inject<(options: number[]) => void>('deleteDrawing')
const drawingHandler = inject<DrawingHandlerType>('drawingHandler')

const appStore = useAppStore()
const props = withDefaults(
    defineProps<{
        value?: any
    }>(),
    {
        value: ''
    }
)

const splitLoading = ref<boolean>(false)

const { copy } = useCopy()
// 复制文字
const onCopy = async (content: string) => {
    await copy(content)
}

// 放大图片
const onEnlargement = (drawing: any, index: number) => {
    const params = drawing
    params.action = 'upsample' + index
    params.model = drawing.model
    console.log(params)
    drawingHandler!({ drawing: params, isClear: false })
}

// 变体图片
const onConversion = (drawing: any, index: number) => {
    const params = drawing
    params.action = 'variation' + index
    params.model = drawing.model
    drawingHandler!({ drawing: params, isClear: false })
}

// 文件下载
const onFileDownload = async (drawing: any) => {
    if (Array.isArray(drawing.image)) {
        uni.$u.toast('请点击自己想要保存的图片后长按保存～')
        return
    }
    saveImageToPhotosAlbum(drawing.image)
}

// 一键切图
const onSplitPic = async (drawing: any) => {
    if (splitLoading.value) {
        return
    }
    if (Array.isArray(drawing.image)) {
        uni.$u.toast('已经切片完成了～')
        return
    }
    uni.showLoading({ title: '切图中' })
    splitLoading.value = true
    try {
        const { getImages } = useSplit(drawing.image || drawing.image_url, {
            columns: 2,
            rows: 2
        })
        const data: any[] = await getImages()
        drawing.image = data
    } catch (error) {
        console.log('一键切图失败=>', error)
    } finally {
        uni.hideLoading()
        splitLoading.value = false
    }
}

// #ifdef APP-PLUS
// 本地路径转base64
const convertLocalPathToBase64 = (path: string) => {
    return new Promise((resolve, reject) => {
        plus.io.resolveLocalFileSystemURL(
            path,
            (entry: any) => {
                entry.file(
                    (file: any) => {
                        const reader = new (plus.io as any).FileReader()
                        reader.onloadend = function (e: any) {
                            resolve(e.target.result)
                        }
                        reader.onerror = function () {
                            reject(
                                new Error('Failed to convert image to base64')
                            )
                        }
                        reader.readAsDataURL(file)
                    },
                    (error: any) => {
                        reject(error)
                    }
                )
            },
            (error) => {
                reject(error)
            }
        )
    })
}
// #endif

const onShareSquare = async (drawing: any, base64?: string) => {
    const params = Object.assign({}, drawing)
    if (base64) {
        params.is_base64 = 1
        // #ifdef APP-PLUS
        params.base64 = await convertLocalPathToBase64(base64)
        uni.$emit('shareSquare', params)
        // #endif
        // #ifdef APP-PLUS
        params.base64 = base64
        // #endif
    }
    // #ifndef APP-PLUS
    uni.$emit('shareSquare', params)
    // #endif
}

// 重新生成
const onReDrawing = async (drawing: any) => {
    pageIndex!.value = 0
    // const res = await uni.showModal({
    //     title: '温馨提示',
    //     content: '是否重新生成当前绘画?',
    //     confirmColor: '#FFC94D'
    // })
    // if (res.confirm) {
    drawForm!.prompt = drawing.prompt
    drawForm!.image_id = ''
    drawForm!.other = drawing.prompt_other
    drawForm!.scale = drawing.scale
    drawForm!.model = drawing.model
    drawForm!.no_content = drawing.no_content
    // drawForm.version = drawing.version
    drawForm.style = drawing.style
    drawForm.quality = drawing.quality
    // drawForm.engine = drawing.engine
    drawForm!.image_base = drawing.image_base
    // }
}

const onDelete = async (drawing: any) => {
    // const res = await uni.showModal({
    //     title: '温馨提示',
    //     content: '是否删除当前绘画?',
    //     confirmColor: '#FFC94D'
    // })
    // if (res.confirm) {
    deleteDrawing([drawing.id])
    // }
}
</script>

<style lang="scss" scoped>
.success {
    display: inline-block;
    width: 100%;
    height: 100%;
    padding: 15rpx 30rpx;
    box-sizing: border-box;

    &__tag {
        padding: 8rpx 16rpx;
        font-size: 28rpx;
        border-radius: 8rpx;
        color: #23b571;
        background: #e3fff2;
    }

    &__section {
        overflow: hidden;
        border-radius: 24rpx;
        .image-bg {
            user-select: none;
            //pointer-events: none;
        }
        .image-content {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 20rpx;
            //border-radius: 6px;
            background-color: rgba(0, 0, 0, 0.5);
        }
    }

    &__footer {
        .opt-btn:hover {
            color: var(--color-primary);
            background: var(--color-primary-light-9);
        }
        .opt-btn {
            transition: all 0.3s;
            font-size: 28rpx;
            margin-left: 14rpx;
            padding: 8rpx 28rpx;
            border-radius: 8rpx;
            color: #333333;
            background: #f2f3f6;
        }
    }
}
</style>
