<template>
    <div class="success">
        <!--    头部操作    -->
        <div class="flex justify-between items-center">
            <div class="success__tag">生成完成</div>
            <div class="flex items-center">
                <image
                    class="w-[32rpx] h-[32rpx] ml-[34rpx] cursor-pointer"
                    :src="IconDownload"
                    alt="下载"
                    @click.stop="onFileDownload(value)"
                />
                <image
                    class="w-[32rpx] h-[32rpx] ml-[34rpx] cursor-pointer"
                    :src="IconReload"
                    alt="重新生成"
                    @click.stop="onReDrawing(value)"
                />
                <image
                    class="w-[32rpx] h-[32rpx] ml-[34rpx] cursor-pointer"
                    :src="IconDelete"
                    alt="删除"
                    @click.stop="onDelete(value)"
                />
            </div>
        </div>
        <!--    中部图片    -->
        <div class="success__section relative text-sm mt-[30rpx]">
            <Picture :picture="value.image" :lazy-img="value.image" />
        </div>

        <!--    底部信息    -->
        <div class="mt-[20rpx] success__footer">
            <div>
                {{ value.template_text || value.prompt }}
            </div>
            <div class="mt-[15px] text-[#999999] text-base">
                时间：{{ value.create_time }}
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { inject } from 'vue'
import { saveImageToPhotosAlbum } from '@/utils/file'

import IconDownload from '@/static/images/common/icon_download.png'
import IconReload from '@/static/images/common/icon_reload.png'
import IconDelete from '@/static/images/common/icon_delete.png'

import Picture from './picture.vue'

import type { QrcodeFormType, PromptParams } from '@/api/qrcode'
const pageIndex = inject<number>('pageIndex')
const qrcodeForm = inject<QrcodeFormType>('qrcodeForm')
const promptParams = inject<PromptParams>('promptParams')
const deleteDrawing = inject<(options: number[]) => void>('deleteDrawing')

const props = withDefaults(
    defineProps<{
        value?: any
    }>(),
    {
        value: ''
    }
)

// 文件下载
const onFileDownload = async (drawing: any) => {
    saveImageToPhotosAlbum(drawing.image)
}

// 重新生成
const onReDrawing = async (params: any) => {
    if (params.prompt_params.length) {
        const arr = params.prompt_params.split(' --')
        arr[0] = arr[0].substring(2)

        arr.forEach((item: string) => {
            const pair: any = item.split(' ')
            // @ts-ignore
            promptParams[pair[0]] = pair[1]
        })
    }

    Object.keys(qrcodeForm!).forEach((key) => {
        //@ts-ignore
        qrcodeForm[key] = params[key]
    })
    pageIndex!.value = 0
}

const onDelete = async (drawing: any) => {
    deleteDrawing!([drawing.id])
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
        cursor: pointer;
        overflow: hidden;
        border-radius: 24rpx;
    }

    &__footer {
        text-align: center;
    }
}
</style>
