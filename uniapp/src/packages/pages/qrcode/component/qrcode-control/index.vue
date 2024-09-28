<template>
    <view class="h-full flex flex-col">
        <view class="flex-1 min-h-0">
            <!--    生成选择    -->
            <generation-picker
                :modelList="config?.draw_model"
                v-model="qrcodeForm!.model"
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
                    <view class="pb-[200rpx]">
                        <template v-if="qrcodeForm!.model === QrcodeModeEnum.MEWX">
                            <!--    生成类型    -->
                            <generation-type v-model="qrcodeForm"></generation-type>

                            <!--    生成模式    -->
                            <generation-mode v-model="qrcodeForm!.way">
                                <template #prefix>
                                    <!--    自定义关键词    -->
                                    <custom-keyword
                                        v-model="qrcodeForm!.prompt"
                                        :example="config?.example"
                                    ></custom-keyword>
                                </template>

                                <template #common>
                                    <!--    二维码显示程度    -->
                                    <qrcode-display
                                        v-model="promptParams!.iw"
                                    ></qrcode-display>
                                </template>

                                <!--    模版    -->
                                <template #mode>
                                    <!--    星月熊二维码风格模版    -->
                                    <mewx-template
                                        v-model="qrcodeForm!.template_id"
                                        :template="config?.mewx?.template"
                                    ></mewx-template>
                                </template>

                                <!--    自定义    -->
                                <template #custom>
                                    <!--    模型选择    -->
                                    <custom-model-picker
                                        v-model="qrcodeForm!.model_id"
                                        :modelList="config?.mewx.model"
                                    ></custom-model-picker>

                                    <!--    版本选择    -->
                                    <custom-version-picker
                                        v-model="promptParams!.v"
                                        :versionList="config?.mewx.version"
                                    ></custom-version-picker>

                                    <!--    图片尺寸    -->
                                    <custom-picture-size
                                        v-model="promptParams!.ar"
                                    ></custom-picture-size>

                                    <!--    码眼选择    -->
                                    <custom-code-type
                                        v-model="promptParams!.shape"
                                    ></custom-code-type>
                                </template>
                            </generation-mode>
                        </template>
                        <!--    生成模式 -- 知数云   -->
                        <template v-else>
                            <!--    文字模式-输入内容    -->
                            <h3 class="text-base font-bold mb-[24rpx]">
                                <span>生成内容</span>
                                <span class="text-error">*</span>
                            </h3>
                            <l-textarea
                                class="mt-[15px]"
                                v-model="qrcodeForm!.qr_content"
                                maxlength="100"
                                :rows="4"
                                placeholder="请输入二维码内容, 文本或链接"
                            ></l-textarea>

                            <!--    自定义关键词    -->
                            <h3 class="text-base font-bold mt-[24rpx]">
                                <span>生成关键词</span>
                                <span class="text-error">*</span>
                            </h3>
                            <custom-keyword
                                v-model="qrcodeForm!.prompt"
                                :example="config?.example"
                            ></custom-keyword>

                            <!--    图片尺寸    -->
                            <custom-picture-size
                                v-model="qrcodeForm!.aspect_ratio"
                            ></custom-picture-size>

                            <!--    码点形状    -->
                            <pixel-style-type
                                v-model="qrcodeForm!.pixel_style"
                                :template="config?.zhishuyun_qrcode?.pixel_style"
                            >
                            </pixel-style-type>

                            <!--    码眼选择    -->
                            <custom-code-type
                                v-model="qrcodeForm!.marker_shape"
                            ></custom-code-type>

                            <!--    知数云二维码风格模版    -->
                            <zhishuyun-template
                                v-model="qrcodeForm!.template_id"
                                v-model:way="qrcodeForm!.way"
                                :template="config?.zhishuyun_qrcode?.template"
                            ></zhishuyun-template>
                        </template>
                    </view>
                </view>
            </scroll-view>
        </view>

        <view
            class="bg-white z-10 w-full p-[20rpx]"
            style="box-shadow: 0 0 10rpx rgba(0, 0, 0, 0.2)"
        >
            <view class="flex justify-center items-center px-[20rpx] pb-[20rpx]">
                <view class="text-center text-[#999999]">
                    <template v-if="consumptionCount">
                        消耗
                        <span class="text-primary">
                            {{ consumptionCount || 0 }}
                        </span>
                        条绘画条数
                    </template>
                    <template v-else> 会员免费 </template>
                </view>
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
                        :loading="isReceiving!"
                        @click.stop="onDrawing"
                    >
                        立即生成
                    </u-button>
                </view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, nextTick, inject } from 'vue'

import GenerationPicker from './generation-picker.vue'
import GenerationType from './generation-type.vue'
import GenerationMode from './generation-mode.vue'
import QrcodeDisplay from './components/qrcode-display.vue'
import MewxTemplate from './components/mewx-template.vue'
import ZhishuyunTemplate from './components/zhishuyun-template.vue'
import CustomKeyword from './components/custom-keyword.vue'
import CustomModelPicker from './components/custom-model-picker.vue'
import CustomVersionPicker from './components/custom-version-picker.vue'
import CustomPictureSize from './components/custom-picture-size.vue'
import CustomCodeType from './components/custom-code-type.vue'
import pixelStyleType from './components/pixel-style-type.vue'

import { QrcodeModeEnum } from '../../enums/qrcodeEnums'
import type { QrcodeFormType, PromptParams } from '@/api/qrcode'
type DrawingHandlerType = (options: {
    params?: QrcodeFormType
    isClear: boolean
}) => void

const config = inject<Record<string, any>>('config')
const getConfig = inject<() => void>('getConfig')
const pageIndex = inject<number>('pageIndex')
const qrcodeForm = inject<QrcodeFormType>('qrcodeForm')
const promptParams = inject<PromptParams>('promptParams')
const consumptionCount = inject<number>('consumptionCount')
const isReceiving = inject<boolean>('isReceiving')
const drawingHandler = inject<DrawingHandlerType>('drawingHandler')


//下拉状态
const refresherStatus = ref(false)
//下拉刷新
const refresh = async () => {
    refresherStatus.value = true
    await nextTick()
    refresherStatus.value = false
    getConfig!()
}

const refreshDebounce = () => {
    uni.$u.debounce(refresh, 500)
}

const generateParamString = (params: any): string => {
    let paramStr = ''
    for (const key in params) {
        if (params[key] !== '') {
            paramStr += `--${key} ${params[key]} `
        }
    }
    return paramStr.trim()
}

const onDrawing = () => {
    qrcodeForm!.prompt_params = generateParamString(promptParams)
    drawingHandler!({ params: qrcodeForm, isClear: true })
}
</script>
