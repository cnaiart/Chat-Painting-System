<template>
    <view class="flex justify-between mt-[20rpx] text-base">
        <view class="flex items-center text-muted" @click="getDrawingExample">
            <view class="flex items-center text-[#8e8e8e] hover:text-primary">
                <u-icon name="reload"></u-icon>
                <text class="text-muted ml-[10rpx]">随便试试</text>
            </view>
            <view class="ml-[10rpx] text-black truncate w-[500rpx]">
                <text
                    class="ml-[20rpx]"
                    v-for="(item, index) in exampleLists"
                    :key="index"
                    @click.stop="appendKeywords(item.prompt_en)"
                >
                    {{ item.prompt }}
                </text>
            </view>
        </view>
    </view>
</template>
<script setup lang="ts">
import { drawingExample } from '@/api/drawing'
import { computed, ref } from 'vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: ''
    }
)

// 示例列表
const exampleLists = ref<any[]>([])

// 获取示例列表
const getDrawingExample = async () => {
    try {
        exampleLists.value = await drawingExample()
    } catch (error) {
        console.log('请求绘画示例失败=>', error)
    }
}

// 添加关键词
const appendKeywords = (content: string) => {
    emit('update:modelValue', content)
}

getDrawingExample()

defineExpose({
    getDrawingExample
})
</script>
