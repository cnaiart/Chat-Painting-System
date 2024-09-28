<template>
    <div class="left-side flex flex-col justify-between">
        <!--    功能名称    -->
        <div
            style="border-bottom: 1px solid #e5e5e5"
            class="flex justify-between items-center text-xl text-[#101010] font-medium mx-[15px] pb-[12px]"
        >
            <span>艺术二维码</span>

            <el-dropdown @command="changeModel">
                <span class="text-black font-normal">
                    {{ getCurrentModelName }}
                    <el-icon class="el-icon--right">
                        <arrow-down />
                    </el-icon>
                </span>
                <template #dropdown>
                    <el-dropdown-menu>
                        <el-dropdown-item
                            v-for="item in config.draw_model"
                            :key="item.model"
                            :command="item.model"
                        >
                            {{ item.name }}
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </template>
            </el-dropdown>
        </div>

        <ElScrollbar class="flex-1 min-h-0">
            <div class="left-side__main px-[15px] pb-[20px]">

                <!--    生成模式 -- 星月熊   -->
                <template v-if="qrcodeForm.model === QrcodeModeEnum.MEWX">
                    <!--    生成类型    -->
                    <generation-type v-model="qrcodeForm"></generation-type>

                    <generation-mode v-model="qrcodeForm.way">
                        <!--    模版    -->
                        <template #mode>
                            <!--    二维码显示程度    -->
                            <qrcode-display
                                v-model="promptParams.iw"
                            ></qrcode-display>

                            <!--    星月熊二维码风格模版    -->
                            <mewx-template
                                v-model="qrcodeForm.template_id"
                                :template="config?.mewx?.template"
                            ></mewx-template>
                        </template>
                        <!--    自定义    -->
                        <template #custom>
                            <!--    自定义关键词    -->
                            <custom-keyword
                                v-model="qrcodeForm.prompt"
                                :example="config.example"
                            ></custom-keyword>

                            <!--    二维码显示程度    -->
                            <qrcode-display
                                v-model="promptParams.iw"
                            ></qrcode-display>

                            <!--    模型选择    -->
                            <custom-model-picker
                                v-model="qrcodeForm.model_id"
                                :modelList="config.mewx.model"
                            ></custom-model-picker>

                            <!--    版本选择    -->
                            <custom-version-picker
                                v-model="promptParams.v"
                                :versionList="config.mewx.version"
                            ></custom-version-picker>

                            <!--    图片尺寸    -->
                            <custom-picture-size
                                v-model="promptParams.ar"
                            ></custom-picture-size>

                            <!--    码眼选择    -->
                            <custom-code-type
                                v-model="promptParams.shape"
                            ></custom-code-type>
                        </template>
                    </generation-mode>
                </template>

                <!--    生成模式 -- 知数云   -->
                <template v-else>
                    <!--    文字模式-输入内容    -->
                    <h3 class="text-base font-bold mt-[12px]">
                        <span>生成内容</span>
                        <span class="text-error">*</span>
                    </h3>
                    <l-textarea
                        class="mt-[10px]"
                        v-model="qrcodeForm.qr_content"
                        maxlength="100"
                        :rows="4"
                        placeholder="请输入二维码内容, 文本或链接"
                    ></l-textarea>

                    <!--    自定义关键词    -->
                    <h3 class="text-base font-bold mt-[12px]">
                        <span>生成关键词</span>
                        <span class="text-error">*</span>
                    </h3>
                    <custom-keyword
                        v-model="qrcodeForm.prompt"
                        :example="config.example"
                    ></custom-keyword>

                    <!--    图片尺寸    -->
                    <custom-picture-size
                        v-model="qrcodeForm.aspect_ratio"
                    ></custom-picture-size>

                    <!--    码点形状    -->
                    <pixel-style-type
                        v-model="qrcodeForm.pixel_style"
                        :template="config?.zhishuyun_qrcode?.pixel_style"
                    >
                    </pixel-style-type>

                    <!--    码眼选择    -->
                    <custom-code-type
                        v-model="qrcodeForm.marker_shape"
                    ></custom-code-type>

                    <!--    知数云二维码风格模版    -->
                    <zhishuyun-template
                        v-model="qrcodeForm.template_id"
                        v-model:way="qrcodeForm.way"
                        :template="config?.zhishuyun_qrcode?.template"
                    ></zhishuyun-template>
                </template>
            </div>
        </ElScrollbar>

        <div class="left-side__footer text-center px-[15px]">
            <div class="p-[10px] flex justify-center items-center">
                <div class="text-[#999999]">
                    <template v-if="consumptionCount">
                        消耗
                        <span class="text-primary">
                            {{ consumptionCount || 0 }}
                        </span>
                        条绘画条数
                    </template>
                    <template v-else> 会员免费 </template>
                </div>
            </div>

            <el-button
                size="large"
                type="default"
                class="w-full submit-btn !text-btn-text"
                :loading="isReceiving"
                @click="onDrawing()"
            >
                立即生成
            </el-button>
        </div>
    </div>
</template>

<script setup lang="ts">
import GenerationType from './generation-type.vue'
import GenerationMode from './generation-mode.vue'
import QrcodeDisplay from './components/qrcode-display.vue'
import MewxTemplate from './components/mewx-template.vue'
import ZhishuyunTemplate from './components/zhishuyun-template.vue'
import PixelStyleType from './components/pixel-style-type.vue'
import CustomKeyword from './components/custom-keyword.vue'
import CustomModelPicker from './components/custom-model-picker.vue'
import CustomVersionPicker from './components/custom-version-picker.vue'
import CustomPictureSize from './components/custom-picture-size.vue'
import CustomCodeType from './components/custom-code-type.vue'

import { QrcodeModeEnum } from '../../enums/qrcodeEnums'
import { ArrowDown } from '@element-plus/icons-vue'
import LTextarea from '~/components/l-textarea/index.vue'

import type { QrcodeFormType } from '~/api/qrcode'
const config = inject<Record<string, any>>('config')
const qrcodeForm = inject<QrcodeFormType>('qrcodeForm')
const promptParams = inject<Record<string, any>>('promptParams')
const consumptionCount = inject<Record<string, any>>('consumptionCount')
const isReceiving = inject<boolean>('isReceiving')
type DrawingHandlerType = (options: {
    params: QrcodeFormType
    isClear: boolean
}) => void
const drawingHandler = inject<DrawingHandlerType>('drawingHandler')

const getCurrentModelName = computed(() => {
    const index = config.draw_model.findIndex(
        (item) => item.model === qrcodeForm.model
    )
    if (index == -1) {
        return '找不到模型'
    }
    consumptionCount.value = config.draw_model[index]?.balance
    return config.draw_model[index]?.name
})

const changeModel = (command: string) => {
    qrcodeForm.model = command
    qrcodeForm.template_id = ''
    if (qrcodeForm.model === QrcodeModeEnum.ZHISHUYUN) {
        qrcodeForm.type = 1
    }
}

const generateParamString = (params): string => {
    let paramStr = ''
    for (const key in params) {
        if (params[key] !== '') {
            paramStr += `--${key} ${params[key]} `
        }
    }
    return paramStr.trim()
}

const onDrawing = () => {
    qrcodeForm.prompt_params = generateParamString(promptParams)
    drawingHandler({ params: qrcodeForm, isClear: true })
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
        }
    }
}
</style>
