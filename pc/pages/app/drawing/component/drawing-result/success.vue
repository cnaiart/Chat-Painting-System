<template>
    <div class="success">
        <!--    头部操作    -->
        <div class="flex justify-between items-center">
            <div class="success__tag">绘画完成</div>
            <div class="flex items-center">
                <el-tooltip effect="dark" content="复制" placement="top">
                    <img
                        class="w-[16px] h-[16px] ml-[16px] cursor-pointer"
                        :src="IconCopy"
                        alt="复制"
                        @click="onCopy(value.prompt_desc)"
                    />
                </el-tooltip>
                <el-tooltip effect="dark" content="下载" placement="top">
                    <img
                        class="w-[16px] h-[16px] ml-[16px] cursor-pointer"
                        :src="IconDownload"
                        alt="下载"
                        @click="onFileDownload(value)"
                    />
                </el-tooltip>
                <el-tooltip effect="dark" content="重新生成" placement="top">
                    <img
                        class="w-[16px] h-[16px] ml-[16px] cursor-pointer"
                        :src="IconReload"
                        alt="重新生成"
                        @click="onReDrawing(value)"
                    />
                </el-tooltip>
                <el-tooltip
                    v-if="
                        !value?.actions?.includes('low_variation') &&
                        value?.actions?.length
                    "
                    effect="dark"
                    content="切片"
                    placement="top"
                >
                    <img
                        class="w-[16px] h-[16px] ml-[16px] cursor-pointer"
                        :src="IconCutout"
                        alt="切片"
                        @click="onSplitPic(value)"
                    />
                </el-tooltip>
                <el-tooltip
                    v-if="appStore.getDrawSquareConfig.is_allow_share"
                    effect="dark"
                    content="分享至广场"
                    placement="top"
                >
                    <img
                        class="w-[16px] h-[16px] ml-[16px] cursor-pointer"
                        :src="IconShare"
                        alt="分享至广场"
                        @click="onShareSquare(value)"
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
            <div class="image-bg">
                <Picture
                    :lazy-img="value.thumbnail"
                    :picture="value.image || value.image_url"
                    :loading="splitLoading"
                    @share="onShareSquare(value, $event)"
                ></Picture>
            </div>
            <div class="image-content">
                <el-popover
                    placement="bottom"
                    title=""
                    effect="dark"
                    :width="300"
                    trigger="hover"
                    :content="value.prompt_desc"
                >
                    <template #reference>
                        <p
                            v-if="value.prompt"
                            class="text-white line-clamp-2"
                            @click="copy(value.prompt)"
                        >
                            {{ value.prompt }}
                        </p>
                    </template>
                </el-popover>
            </div>
        </div>

        <!--    底部信息    -->
        <div class="mt-[20px] success__footer">
            <div
                class="h-[56px]"
                v-if="value.fail_reason && value.censor_status == 2"
            >
                <el-popover
                    placement="bottom"
                    title=""
                    effect="dark"
                    :width="300"
                    trigger="hover"
                    :content="value.fail_reason"
                >
                    <template #reference>
                        <p
                            class="line-clamp-2"
                            @click="copy(value.fail_reason)"
                        >
                            {{ value.fail_reason }}
                        </p>
                    </template>
                </el-popover>
            </div>
            <template v-else>
                <div>
                    <span>放大图片</span>
                    <template
                        v-if="
                            !value?.actions?.includes('low_variation') &&
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
                            !value?.actions?.includes('low_variation') &&
                            value?.actions?.length
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
                class="flex justify-between mt-[15px] text-[#999999] text-base"
            >
                <span>时间：{{ value.create_time }}</span>
                <el-tag size="small">{{ value.draw_model }}</el-tag>
            </div>
        </div>
    </div>

    <ShareSquare
        v-if="showShare"
        ref="shareRef"
        @success="sharedIds.push(value.id)"
        @close="showShare = false"
    ></ShareSquare>
</template>

<script lang="ts" setup>
import IconCopy from 'assets/images/drawing/icon_copy.png'
import IconDownload from 'assets/images/drawing/icon_download.png'
import IconCutout from 'assets/images/drawing/icon_cutout.png'
import IconReload from 'assets/images/drawing/icon_reload.png'
import IconShare from 'assets/images/drawing/icon_share.png'
import IconDelete from 'assets/images/drawing/icon_delete.png'

import feedback from '~/utils/feedback'
import { useAppStore } from '~/stores/app'
import { downloadImgFile } from '~/utils/download'
import ShareSquare from '../component/share-square.vue'
import Picture from './picture.vue'

import { useSplit } from '../../hooks/useSplit'

import type { DrawingFormType } from '~/api/drawing'
import { cloneDeep } from 'lodash-es'
type DrawingHandlerType = (options: {
    drawing: DrawingFormType
    isClear: boolean
}) => void

const drawForm = inject<DrawingFormType>('drawForm')
const drawingHandler = inject<DrawingHandlerType>('drawingHandler')
const deleteDrawing = inject<(options: number[]) => void>('deleteDrawing')

const appStore = useAppStore()
const props = withDefaults(
    defineProps<{
        value?: any
    }>(),
    {
        value: ''
    }
)

const showShare = ref<boolean>(false)
const shareRef = shallowRef<any>(null)
const sharedIds = ref<number[]>([])
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
    drawingHandler({ drawing: params, isClear: false })
}

// 变体图片
const onConversion = (drawing: any, index: number) => {
    const params = drawing
    params.action = 'variation' + index
    params.model = drawing.model
    drawingHandler({ drawing: params, isClear: false })
}

// 文件下载
const onFileDownload = async (drawing: any) => {
    if (Array.isArray(drawing.image)) {
        feedback.msgError('切片后请右击保存图片～')
        return
    }
    downloadImgFile(drawing.image)
}

// 一键切图
const onSplitPic = async (drawing: any) => {
    if (splitLoading.value) {
        return
    }
    if (Array.isArray(drawing.image)) {
        feedback.msgWarning('已经切片完成了～')
        return
    }
    splitLoading.value = true
    try {
        const { getImages } = useSplit(drawing.image || drawing.image_url, {
            columns: 2,
            rows: 2
        })
        const data: any = await getImages()
        drawing.image = data
    } catch (error) {
        console.log('一键切图失败=>', error)
    } finally {
        splitLoading.value = false
    }
}

// 重新生成
const onReDrawing = async (drawing: any) => {
    try {
        // await feedback.confirm('确认要重新生成当前绘画吗？')
        drawForm.prompt = drawing.prompt
        drawForm.image_id = ''
        drawForm.other = drawing.prompt_other
        drawForm.scale = drawing.scale
        drawForm.model = drawing.model
        drawForm.no_content = drawing.no_content
        // drawForm.version = drawing.version
        drawForm.style = drawing.style
        drawForm.quality = drawing.quality
        // drawForm.engine = drawing.engine
        drawForm.image_base = drawing.image_base
    } catch (error) {
        console.log('重新生成', error)
    }
}

const onShareSquare = async (drawing: any, base64?: string) => {
    console.log(base64)
    try {
        const params = Object.assign({}, drawing)
        if (base64) {
            params.is_base64 = 1
            params.base64 = base64
        }
        if (sharedIds.value.includes(params.id) || params.is_square) {
            await feedback.confirm('该图片已分享过，是否确认重复分享？')
        }
        showShare.value = true
        await nextTick()
        shareRef.value.open(params)
    } catch (error) {
        console.log(error)
    }
}

const onDelete = (drawing: any) => {
    deleteDrawing([drawing.id])
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
        .image-bg {
            user-select: none;
            //pointer-events: none;
        }
        .image-content {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 10px;
            border-radius: 6px;
            background-color: rgba(0, 0, 0, 0.4);
        }
    }

    &__footer {
        .opt-btn:hover {
            color: var(--el-color-primary);
            background: var(--el-color-primary-light-9);
        }
        .opt-btn {
            transition: all 0.3s;
            cursor: pointer;
            font-size: 14px;
            margin-left: 7px;
            padding: 2px 12px;
            border-radius: 4px;
            color: #333333;
            background: #f2f3f6;
        }
    }
}
</style>
