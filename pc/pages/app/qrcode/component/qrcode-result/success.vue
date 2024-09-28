<template>
    <div class="success">
        <!--    头部操作    -->
        <div class="flex justify-between items-center">
            <div class="success__tag">生成成功</div>
            <div class="flex items-center">
                <el-tooltip effect="dark" content="下载" placement="top">
                    <img
                        class="w-[16px] h-[16px] ml-[16px] cursor-pointer"
                        :src="IconDownload"
                        alt="下载"
                        @click="onFileDownload(value)"
                    />
                </el-tooltip>
                <el-tooltip effect="dark" content="画同款" placement="top">
                    <img
                        class="w-[16px] h-[16px] ml-[16px] cursor-pointer"
                        :src="IconReload"
                        alt="画同款"
                        @click="onReDrawing(value)"
                    />
                </el-tooltip>
                <el-tooltip effect="dark" content="删除" placement="top">
                    <img
                        class="w-[16px] h-[16px] ml-[16px] cursor-pointer"
                        :src="IconDelete"
                        alt="删除"
                        @click="onDelete(value)"
                    />
                </el-tooltip>
            </div>
        </div>
        <!--    中部图片    -->
        <div class="success__section relative text-sm mt-[15px]">
            <Picture :lazy-img="value.image" :picture="value.image"></Picture>
        </div>

        <!--    底部信息    -->
        <div class="mt-[20px] success__footer">
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
import Picture from './picture.vue'
import IconDownload from 'assets/images/drawing/icon_download.png'
import IconReload from 'assets/images/drawing/icon_reload.png'
import IconDelete from 'assets/images/drawing/icon_delete.png'

import { downloadImgFile } from '~/utils/download'

const qrcodeForm = inject('qrcodeForm')
const promptParams = inject('promptParams')
const deleteQrcode = inject<(options: number[]) => void>('deleteQrcode')

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
    downloadImgFile(drawing.image)
}

// 重新生成
const onReDrawing = async (params: any) => {
    try {
        if (params.prompt_params.length) {
            const arr = params.prompt_params.split(' --')
            arr[0] = arr[0].substring(2)

            arr.forEach((item) => {
                const pair = item.split(' ')
                promptParams[pair[0]] = pair[1]
            })
        }

        Object.keys(qrcodeForm).forEach((key) => {
            //@ts-ignore
            qrcodeForm[key] = params[key]
        })
    } catch (error) {
        console.log('重新生成', error)
    }
}

const onDelete = (drawing: any) => {
    deleteQrcode([drawing.id])
}
</script>

<style lang="scss" scoped>
.success {
    display: inline-block;
    width: 100%;
    height: 100%;
    padding: 15px 20px;

    &__tag {
        padding: 4px 8px;
        font-size: 14px;
        border-radius: 4px;
        color: #23b571;
        background: #e3fff2;
    }

    &__section {
        cursor: pointer;
        overflow: hidden;
        border-radius: 12px;
    }

    &__footer {
        text-align: center;
    }
}
</style>
