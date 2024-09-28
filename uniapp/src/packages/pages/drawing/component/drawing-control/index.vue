<template>
    <view class="h-full flex flex-col">
        <view class="flex-1 min-h-0">
            <!--    生成选择    -->
            <generation-picker
                ref="generationPickerRef"
                v-model="drawForm.model"
                v-model:balance="consumptionCount"
            />

            <scroll-view
                class="h-full"
                style="border-top: 1px #f2f2f2 solid"
                :scroll-y="true"
                :refresher-enabled="true"
                :scroll-anchoring="true"
                :refresher-triggered="refresherStatus"
                @refresherpulling="refreshDebounce"
            >
                <view class="h-full p-[30rpx] relative">
                    <view class="pb-[100px]">
                        <!--    描述词    -->
                        <prompt-txt2img
                            v-model="drawForm.prompt"
                            :keydownRef="keywordSuggestionRef"
                        />

                        <!--    关键词示例    -->
                        <example-prompt
                            ref="examplePromptRef"
                            v-model="drawForm.prompt"
                        />

                        <!--    上传参考图    -->
                        <reference-image
                            v-if="DrawModeEnum.DALLE3 !== drawForm.model"
                            v-model="drawForm.image_base"
                        />

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

                        <!--    DALLE3 组件    -->
                        <template v-else-if="drawForm!.model === DrawModeEnum.DALLE3">
                            <!--    图片尺寸    -->
                            <dalle3-picture-size
                                v-model="drawForm!.scale"
                            ></dalle3-picture-size>

                            <!--    风格选择    -->
                            <dalle3-style-picker
                                v-model="drawForm!.style"
                            ></dalle3-style-picker>

                            <!--    图片质量    -->
                            <dalle3-size-type
                                v-model="drawForm!.quality"
                            ></dalle3-size-type>
                        </template>

                        <!--    MJ 组件    -->
                        <template v-else>
                            <!--    模型选择    -->
                            <ModelPicker
                                v-model:version="drawForm.version"
                                v-model:style="drawForm.style"
                            />

                            <!--    图片尺寸    -->
                            <picture-size v-model="drawForm.scale"></picture-size>

                            <!--    忽略的元素    -->
                            <negative-prompt
                                v-model="drawForm.no_content"
                            ></negative-prompt>

                            <!--    其它参数    -->
                            <other-prompt
                                v-model="drawForm.other"
                            ></other-prompt>
                        </template>
                    </view>
                </view>
            </scroll-view>
        </view>

        <view
            class="bg-white z-10 w-full p-[20rpx]"
            style="box-shadow: 0 0 10rpx rgba(0, 0, 0, 0.2)"
        >
            <view class="flex justify-between items-center pb-[20rpx]">
                <view
                    class="text-center text-[#999999]"
                    :class="{
                        'm-auto': !appStore?.getDrawConfig?.disclaimer_status
                    }"
                >
                    <template v-if="consumptionCount">
                        消耗
                        <span class="text-primary">
                            {{ consumptionCount || 0 }}
                        </span>
                        条绘画条数
                    </template>
                    <template v-else> 会员免费 </template>
                </view>
                <DisclaimerModal
                    v-if="appStore?.getDrawConfig?.disclaimer_status"
                    :content="appStore?.getDrawConfig?.disclaimer_content"
                >
                    <view class="flex items-center text-[#999999]">
                        <u-icon name="question-circle"></u-icon>
                        <text class="ml-[4rpx]">免责声明</text>
                    </view>
                </DisclaimerModal>
            </view>
            <!--    生成    -->
            <view class="flex w-full h-[82rpx] bg-[#f0f2f2] rounded-[999px] text-[#333333]">
                <view
                    class="w-[50%] h-[82rpx] flex justify-center items-center"
                    @click="pageIndex = 1"
                >
                    生成记录
                </view>
                <view class="w-[50%]">
                    <u-button
                        type="primary"
                        :custom-style="{
                            width: '100%',
                            height: '82rpx',
                            fontSize: '30rpx',
                            margin: '0'
                        }"
                        shape="circle"
                        :loading="isReceiving"
                        @click.stop="onDrawing"
                    >
                        立即生成
                    </u-button>
                </view>
            </view>
        </view>

        <KeywordSuggestion
            ref="keywordSuggestionRef"
            v-model="drawForm.prompt"
        />
    </view>
</template>

<script setup lang="ts">
import { inject, nextTick, ref, shallowRef, watch } from 'vue'

import KeywordSuggestion from './components/keyword-suggestion.vue'
const keywordSuggestionRef =
    shallowRef<InstanceType<typeof KeywordSuggestion>>()

// MJ
import GenerationPicker from './generation-picker.vue'
import PromptTxt2img from './prompt-txt2img.vue'
import ExamplePrompt from './example-prompt.vue'
import ReferenceImage from './reference-image.vue'
import NegativePrompt from './negative-prompt.vue'
import OtherPrompt from './other-prompt.vue'
import PictureSize from './picture-size.vue'
import ModelPicker from './model-picker.vue'
import DisclaimerModal from './disclaimer-modal.vue'

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

import { useAppStore } from '@/stores/app'
const appStore = useAppStore()

import type { DrawingFormType } from '@/api/drawing'
type DrawingHandlerType = (options?: {
    drawing?: DrawingFormType
    isClear?: boolean
}) => void

const pageIndex = inject<number>('pageIndex')
const consumptionCount = inject<number>('consumptionCount')
const drawForm = inject<DrawingFormType | undefined>('drawForm')
const isReceiving = inject<boolean>('isReceiving')
const drawingHandler = inject<DrawingHandlerType>('drawingHandler')

//生成选择ref
const generationPickerRef = shallowRef()
//实例ref
const examplePromptRef = shallowRef()

//下拉状态
const refresherStatus = ref(false)

//下拉刷新
const refresh = async () => {
    refresherStatus.value = true
    await nextTick()

    refresherStatus.value = false
    await examplePromptRef.value.getDrawingExample()
}

const refreshDebounce = () => {
    uni.$u.debounce(refresh, 500)
}

import { useConfigEffect } from '../../hooks/useConfigEffect'
const { yjData, getYjSdData, sdData, getSdData } = useConfigEffect()
watch(
    () => drawForm?.model,
    (value) => {
        if (value === DrawModeEnum.SD) {
            drawForm!.scale = '1024x1024'
            getSdData()
        } else if (value === DrawModeEnum.YJ_SD) {
            drawForm!.scale = '2'
            drawForm!.style = 'default'
            getYjSdData()
        } else if (value === DrawModeEnum.DALLE3) {
            drawForm!.scale = '1024x1024'
            drawForm!.style = 'vivid'
        } else {
            drawForm!.scale = '1:1'
            drawForm!.style = 'default'
        }
    },
    { deep: false, immediate: false }
)

const onDrawing = () => {
    drawForm!.action = 'generate'
    drawingHandler!({ drawing: drawForm, isClear: true })
}
</script>
