<template>
    <h3 class="text-base font-bold mt-[15px]">
        <span>图片质量</span>
    </h3>

    <div class="mt-[10px]">
        <div
            v-for="item in typeList"
            :key="item.value"
            class="size-type-option rounded-[4px]"
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
import { useVModels } from '@vueuse/core'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: {
            version: '',
            style: 'default'
        }
    }
)
const { modelValue: value } = useVModels(props, emit)

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
value.value = 'standard'
</script>

<style scoped>
.size-type-option:nth-child(2n) {
    margin-left: 10px;
}
.size-type-option {
    display: inline-block;
    cursor: pointer;
    width: 155px;
    padding: 8px 0;
    text-align: center;
    border: 1px solid #e5e5e5;
}
.size-type-option:hover, .size-type-option__active {
    color: var(--el-color-primary);
    border: 1px solid var(--el-color-primary);
    background-color: var(--el-color-primary-light-9);
}
</style>
