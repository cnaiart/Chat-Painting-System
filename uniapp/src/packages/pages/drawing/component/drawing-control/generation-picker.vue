<template>
    <u-tabs
        v-if="drawModel.modelList.length"
        :list="drawModel.modelList"
        :is-scroll="true"
        v-model="drawModel.current"
        :active-color="$theme.primaryColor"
        bg-color="transparent"
        :barStyle="{
            background: $theme.primaryColor
        }"
        @change="handleChange"
    ></u-tabs>
</template>
<script setup lang="ts">
import { reactive, watch } from 'vue'
import { drawingModel } from '@/api/drawing'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
    (event: 'update:balance', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        balance?: any
        modelValue?: any
    }>(),
    {
        balance: 0,
        modelValue: ''
    }
)

// 绘画模型
const drawModel = reactive({
    current: 0,
    modelList: [] as any[]
})

watch(
    () => props.modelValue,
    (value) => {
        const index = drawModel.modelList.findIndex((item: any) => {
            return item.model === value
        })
        drawModel.current = index == -1 ? 0 : index
    },
    { immediate: false }
)

// 获取绘画模型数据
const getDrawModel = async () => {
    try {
        const data = await drawingModel()
        drawModel.modelList = data
        const current = data.find((item: any) => item.default) || data[0]
        const index = data.findIndex((item: any) => item.default)
        handleChange(index == '-1' ? 0 : index)
        emit('update:balance', current.balance)
        emit('update:modelValue', current.model)
        console.log(data)
    } catch (error) {
        console.log('获取绘画模型失败=>', error)
    }
}

const handleChange = (index: any) => {
    drawModel.current = index
    emit('update:balance', drawModel.modelList[index].balance)
    emit('update:modelValue', drawModel.modelList[index].model)
}

getDrawModel()

defineExpose({
    getDrawModel
})
</script>

<style scoped>
.record-btn {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 140rpx;
    height: 56rpx;
    border-radius: 30px;
}
</style>
