<template>
    <view class="fail-box">
        <!--    头部操作    -->
        <view class="flex justify-between items-center">
            <view class="fail-box__tag">绘画失败</view>
            <view class="flex items-center">
                <image
                    class="w-[32rpx] h-[32rpx] ml-[34rpx] cursor-pointer"
                    :src="IconReload"
                    alt="重新生成"
                    @click="onReDrawing(value)"
                />
                <image
                    class="w-[32rpx] h-[32rpx] ml-[34rpx]"
                    :src="IconDelete"
                    alt="删除"
                    @click="deleteDrawing([value.id])"
                />
            </view>
        </view>
        <image class="w-[300rpx] h-[300rpx] mt-[100rpx]" :src="ImageError" />
        <view class="text-xl mt-[40rpx]"> 绘画失败 </view>
        <view
            class="mt-[10px] text-base text-[#999999] px-[30px] h-[320rpx] break-words"
        >
            错误信息：{{ value.fail_reason }}
        </view>
        <div class="flex justify-between items-center mt-[auto] text-[#999999] text-base">
            <span>时间：{{ value.create_time }}</span>
            <u-tag :text="value.draw_model" mode="light" size="mini" />
        </div>
    </view>
</template>

<script lang="ts" setup>
import ImageError from '@/packages/static/images/drawing/error.png'
import IconDelete from '@/static/images/common/icon_delete.png'
import IconReload from '@/static/images/common/icon_reload.png'

import { inject } from 'vue'
import type { DrawingFormType } from '@/api/drawing'

const pageIndex = inject<number>('pageIndex')
const drawForm = inject<DrawingFormType>('drawForm')
const deleteDrawing = inject<(options: number[]) => void>('deleteDrawing')

const props = withDefaults(
    defineProps<{
        value?: any
    }>(),
    {
        value: {}
    }
)

const onReDrawing = async (drawing: any) => {
    try {
        pageIndex!.value = 0
        // await feedback.confirm('确认要重新生成当前绘画吗？')
        drawForm!.prompt = drawing.prompt
        drawForm!.image_id = ''
        drawForm!.other = drawing.prompt_other
        drawForm!.scale = drawing.scale
        drawForm!.model = drawing.model
        drawForm!.style = drawing.style
        drawForm!.quality = drawing.quality
        drawForm!.no_content = drawing.no_content
        drawForm!.image_base = drawing.image_base
    } catch (error) {
        console.log('重新生成', error)
    }
}
</script>

<style lang="scss" scoped>
.fail-box {
    display: inline-block;
    width: 100%;
    height: 100%;
    padding: 15px 20px;
    box-sizing: border-box;
    text-align: center;

    &__tag {
        padding: 4px 8px;
        font-size: 14px;
        border-radius: 4px;
        color: #f63210;
        background: #ffefeb;
    }
}
</style>
