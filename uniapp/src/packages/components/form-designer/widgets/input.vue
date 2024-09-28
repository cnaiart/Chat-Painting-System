<template>
    <view class="widget-input">
        <u-input
            class="flex-1 min-w-0"
            v-model="value"
            :placeholder="placeholder"
            height="70"
            placeholder-style="color: #999"
            v-bind="$attrs"
        >
        </u-input>
        <view v-if="showWordLimit" class="text-xs text-muted ml-[20rpx]">
            {{ value?.length }} / {{ $attrs.maxlength }}
        </view>
    </view>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(
    defineProps<{
        modelValue: string
        placeholder?: string
        showWordLimit?: boolean
    }>(),
    {
        modelValue: '',
        placeholder: '请输入',
        showWordLimit: true
    }
)
const emit = defineEmits<{
    (event: 'update:modelValue', value: any): void
}>()
const value = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})
</script>
<style lang="scss" scoped>
.widget-input {
    border-radius: 10rpx;
    padding: 10rpx 20rpx;
    flex: 1;
    display: flex;
    align-items: center;
    @apply bg-page;
    .u-input {
        :deep(.u-input__input) {
            @apply text-base;
        }
    }
}
</style>
