<template>
    <u-tabs
        v-if="modelList.length"
        :list="modelList"
        :is-scroll="true"
        v-model="current"
        name="name"
        :active-color="$theme.primaryColor"
        bg-color="transparent"
        :barStyle="{
            background: $theme.primaryColor
        }"
        @change="handleChange"
    ></u-tabs>
</template>
<script setup lang="ts">
import { ref, watch } from 'vue'
const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
    (event: 'update:balance', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        balance?: any
        modelValue?: any
        modelList: any
    }>(),
    {
        balance: 0,
        modelValue: '',
        modelList: []
    }
)

const current = ref<number>(0)
const handleChange = (index: any) => {
    current.value = index
    emit('update:balance', props.modelList[index].balance)
    emit('update:modelValue', props.modelList[index].model)
}

watch(
    () => props.modelList,
    (value) => {
        const index = value.findIndex((item: any) => {
            return item.default
        })
        current.value = index == -1 ? 0 : index
        emit('update:balance', props.modelList[current.value].balance)
        emit('update:modelValue', props.modelList[current.value].model)
    },
    { immediate: false }
)
watch(
    () => props.modelValue,
    (value) => {
        const index = props.modelList.findIndex((item: any) => {
            return item.model === value
        })
        current.value = index == -1 ? 0 : index
        emit('update:balance', props.modelList[current.value].balance)
        emit('update:modelValue', props.modelList[current.value].model)
    },
    { immediate: false }
)
</script>
