<template>
    <h3 class="text-base font-bold mt-[40rpx]">
        <span>图片质量</span>
    </h3>

    <div class="mt-[20rpx]">
        <div
            v-for="item in typeList"
            :key="item.value"
            class="size-type-option"
            :class="{
                'size-type-option__active': item.value === value,
            }"
            @click="value = item.value"
        >
            {{ item.label }}
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: 'standard'
    }
)
const value = computed({
    get: () => {
        return props.modelValue
    },
    set: (val) => {
        emit('update:modelValue', val)
    }
})

const typeList: any = [
    {
        value: 'standard',
        label: '标准'
    },
    {
        value: 'hd',
        label: 'HD-高清'
    }
]
</script>

<style scoped>
.size-type-option:nth-child(2n) {
    margin-left: 20rpx;
}
.size-type-option {
    display: inline-block;
    cursor: pointer;
    width: 200rpx;
    padding: 12rpx 0;
    text-align: center;
    border-radius: 8rpx;
    border: 1px solid #e5e5e5;
}
.size-type-option:hover, .size-type-option__active {
    color: var(--color-primary);
    border: 1px solid var(--color-primary);
    background-color: var(--color-primary-light-9);
}
</style>
