<template>
    <view class="fail-box">
        <!--    头部操作    -->
        <view class="flex justify-between items-center">
            <view class="fail-box__tag">生成失败</view>
            <view class="flex items-center">
                <image
                    class="w-[32rpx] h-[32rpx]"
                    :src="IconDelete"
                    alt="删除"
                    @click="deleteDrawing([id])"
                />
            </view>
        </view>
        <image class="w-[300rpx] h-[300rpx] mt-[100rpx]" :src="ImageError" />
        <view class="text-xl mt-[40rpx]"> 生成失败 </view>
        <view class="mt-[10px] text-base text-[#999999] px-[30px] h-[60px]  break-words">
            错误信息：{{ value }}
        </view>
    </view>
</template>

<script lang="ts" setup>
import ImageError from '@/packages/static/images/drawing/error.png'
import IconDelete from '@/static/images/common/icon_delete.png'

import { inject } from 'vue'
const deleteDrawing = inject<(options: number[]) => void>('deleteDrawing')

const props = withDefaults(
    defineProps<{
        value?: any
        id: number
    }>(),
    {
        value: '',
        id: 0
    }
)
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
