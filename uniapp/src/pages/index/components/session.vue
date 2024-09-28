<template>
    <view class="bg-[#333] text-white flex items-center px-[20rpx] py-[24rpx]">
        <u-icon name="list" :size="40" @click="show = true" />
        <view class="flex-1 line-clamp-1 ml-[20rpx]">{{ currentSession }}</view>
        <view class="text-xs flex items-center" @click="sessionAdd">
            <u-icon name="plus-circle" class="mr-[8rpx]" size="36" />
            新建
        </view>
    </view>
</template>
<script setup lang="ts">
import { computed } from 'vue'
import { useSessionList } from './useSessionList'
const props = defineProps({
    modelValue: {
        type: Boolean
    }
})
const emit = defineEmits<{
    (event: 'update:modelValue', value: boolean): void
}>()
const show = computed({
    get: () => {
        return props.modelValue
    },
    set: (value) => {
        emit('update:modelValue', value)
    }
})
const { currentSession, sessionAdd } = useSessionList()
</script>
