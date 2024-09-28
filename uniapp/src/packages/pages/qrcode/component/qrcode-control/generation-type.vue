<template>
    <h3 class="text-base font-bold">
        <span>生成内容</span>
        <span class="text-error">*</span>
    </h3>
    <div class="mt-[20rpx] text-sm">
        <!--    类型选择    -->
        <div class="inline-flex bg-[#f0f2f2] rounded-[28rpx] text-[#333333]">
            <div
                v-for="item in typeArr"
                :key="item.value"
                class="rounded-[28rpx] w-[144rpx] h-[56rpx] leading-[56rpx] flex justify-center"
                :class="{
                    'bg-primary text-btn-text': item.value === modelValue.type
                }"
                @click="modelValue.type = item.value"
            >
                {{ item.label }}
            </div>
        </div>

        <!--    图片模式-上传二维码    -->
        <reference-image v-if="modelValue.type == 2" v-model="modelValue.qr_image" />

        <!--    文字模式-输入内容    -->
        <view class="mt-[30rpx]" v-if="modelValue.type == 1">
            <l-textarea
                v-model="modelValue.qr_content"
                maxlength="100"
                :rows="4"
                :custom-class="{
                    height: '200rpx'
                }"
                placeholder="请输入二维码内容, 或者已备案域名或合法的文本内容"
            ></l-textarea>
        </view>
    </div>
</template>
<script setup lang="ts">
import LTextarea from '@/components/l-textarea/l-textarea.vue'
import ReferenceImage from './components/reference-image.vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: {}
    }
)

const typeArr = [
    {
        label: '图片模式',
        value: 2
    },
    {
        label: '文字模式',
        value: 1
    }
]
</script>
