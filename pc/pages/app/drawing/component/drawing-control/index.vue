<template>
    <div class="left-side flex flex-col justify-between">
        <!--    生成选择    -->
        <generation-picker
            class="px-[15px]"
            v-model="drawForm.model"
            v-model:balance="balance"
        ></generation-picker>

        <ElScrollbar class="flex-1 min-h-0">
            <div class="left-side__main pb-[20px] px-[15px]">
                <!--    描述词    -->
                <prompt-txt2img v-model="drawForm.prompt"></prompt-txt2img>

                <!--    关键词示例    -->
                <example-prompt v-model="drawForm.prompt"></example-prompt>

                <!--    上传参考图    -->
                <reference-image
                    v-if="DrawModeEnum.DALLE3 !== drawForm.model"
                    v-model="drawForm.image_base"
                ></reference-image>

                <!--    本地SD组件    -->
                <template v-if="drawForm.model === DrawModeEnum.SD">
                    <!--    图片尺寸    -->
                    <sd-picture-size v-model="drawForm.scale"></sd-picture-size>

                    <!--    模型选择    -->
                    <sd-model-picker
                        v-model="drawForm.engine"
                        :model-list="sdData"
                    ></sd-model-picker>
                </template>

                <!--    意间SD组件    -->
                <template v-else-if="drawForm.model === DrawModeEnum.YJ_SD">
                    <!--    图片尺寸    -->
                    <yj-picture-size v-model="drawForm.scale"></yj-picture-size>

                    <!--    模型选择    -->
                    <yj-model-picker
                        v-model="drawForm"
                        :data="yjData.detail"
                    ></yj-model-picker>
                </template>

                <template v-else-if="drawForm.model === DrawModeEnum.DALLE3">
                    <!--    图片尺寸    -->
                    <dalle3-picture-size
                        v-model="drawForm.scale"
                    ></dalle3-picture-size>

                    <!--    风格选择    -->
                    <dalle3-style-picker
                        v-model="drawForm.style"
                    ></dalle3-style-picker>

                    <!--    图片质量    -->
                    <dalle3-size-type
                        v-model="drawForm.quality"
                    ></dalle3-size-type>
                </template>

                <!--    MJ 组件    -->
                <template v-else>
                    <!--    图片尺寸    -->
                    <picture-size v-model="drawForm.scale"></picture-size>

                    <!--    模型选择    -->
                    <model-picker v-model="drawForm"></model-picker>

                    <!--    忽略的元素    -->
                    <negative-prompt
                        v-model="drawForm.no_content"
                    ></negative-prompt>

                    <!--    其它参数    -->
                    <other-prompt v-model="drawForm.other"></other-prompt>
                </template>
            </div>
        </ElScrollbar>

        <div class="left-side__footer text-center">
            <div class="p-[10px] flex justify-between items-center">
                <div
                    class="text-[#999999]"
                    :class="{
                        'm-auto': !appStore?.getDrawConfig?.disclaimer_status
                    }"
                >
                    <template v-if="balance">
                        消耗
                        <span class="text-primary">
                            {{ balance || 0 }}
                        </span>
                        条绘画条数
                    </template>
                    <template v-else> 会员免费 </template>
                </div>
                <el-popover
                    v-if="appStore?.getDrawConfig?.disclaimer_status"
                    placement="top"
                    title=""
                    :width="200"
                    effect="dark"
                    trigger="hover"
                    :content="appStore?.getDrawConfig?.disclaimer_content"
                >
                    <template #reference>
                        <div
                            class="flex items-center cursor-pointer text-[#999999]"
                        >
                            <el-icon><QuestionFilled /></el-icon>
                            <span class="ml-1">免责声明</span>
                        </div>
                    </template>
                </el-popover>
            </div>
            <div class="mx-[10px]">
                <el-button
                    size="large"
                    type="default"
                    class="w-full submit-btn"
                    :loading="isReceiving"
                    @click="onDrawing()"
                >
                    立即生成
                </el-button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { QuestionFilled } from '@element-plus/icons-vue'

import GenerationPicker from './generation-picker.vue'
import PromptTxt2img from './prompt-txt2img.vue'
import ExamplePrompt from './example-prompt.vue'
import ReferenceImage from './reference-image.vue'

// MJ
import NegativePrompt from './negative-prompt.vue'
import OtherPrompt from './other-prompt.vue'
import PictureSize from './picture-size.vue'
import ModelPicker from './model-picker.vue'

// 意间爱-SD
import YjModelPicker from './yj-model-picker.vue'
import YjPictureSize from './yj-picture-size.vue'

// DALLE3
import Dalle3PictureSize from './dalle3-picture-size.vue'
import Dalle3StylePicker from './dalle3-style-picker.vue'
import Dalle3SizeType from './dalle3-size-type.vue'

// SD
import SdModelPicker from './sd-model-picker.vue'
import SdPictureSize from './sd-picture-size.vue'

enum DrawModeEnum {
    SD = 'sd',
    YJ_SD = 'yijian_sd',
    MDD_MJ = 'mddai_mj',
    ZSY_MJ_FAST = 'zhishuyun_fast',
    ZSY_MJ_RELAX = 'zhishuyun_relax',
    DALLE3 = 'dalle3'
}

import { useAppStore } from '~/stores/app'
const appStore = useAppStore()

import type { DrawingFormType } from '~/api/drawing'
type DrawingHandlerType = (options: {
    drawing: DrawingFormType
    isClear: boolean
}) => void

const drawForm = inject<DrawingFormType>('drawForm')
const isReceiving = inject<boolean>('isReceiving')
const drawingHandler = inject<DrawingHandlerType>('drawingHandler')

const balance = ref<number>(0)

import { useConfigEffect, yjData, sdData } from '../../hooks/useConfigEffect'
watch(
    () => drawForm.model,
    (value) => {
        console.log('改变了')
        if (value === DrawModeEnum.SD) {
            drawForm.scale = '1024x1024'
            useConfigEffect().getSdData()
        } else if (value === DrawModeEnum.YJ_SD) {
            drawForm.scale = '2'
            drawForm.style = 'default'
            useConfigEffect().getYjSdData()
        } else if (value === DrawModeEnum.DALLE3) {
            drawForm.scale = '1024x1024'
            drawForm.style = 'vivid'
        } else {
            drawForm.scale = '1:1'
            drawForm.style = 'default'
        }
    },
    { deep: false, immediate: false }
)

const onDrawing = () => {
    drawForm.action = 'generate'
    drawingHandler({ drawing: drawForm, isClear: true })
}
</script>

<style lang="scss" scoped>
.left-side {
    height: 100%;

    &__footer {
        .submit-btn {
            font-size: 16px;
            height: 52px !important;
            border-radius: 60px !important;
            background: linear-gradient(
                90deg,
                var(--gradient-1) 0%,
                var(--gradient-2) 100%
            );
            @apply text-btn-text;
        }
    }
}
</style>
