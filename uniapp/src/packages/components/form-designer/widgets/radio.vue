<template>
    <view class="widget-radio">
        <view class="radio-group">
            <view
                class="radio-item"
                v-for="(item, index) in options"
                :key="index"
                @click="value = item"
                :class="{
                    'radio-item--active': value === item
                }"
            >
                {{ item }}
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { computed, onBeforeMount, ref } from 'vue'

const props = withDefaults(
    defineProps<{
        defaultValue: string
        modelValue: string
        options: string[]
    }>(),
    {
        modelValue: '',
        options: () => []
    }
)

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const value = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})
onBeforeMount(() => {
    if (props.defaultValue) {
        value.value = props.defaultValue
    }
})
</script>
<style lang="scss" scoped>
.widget-radio {
    .radio-group {
        display: flex;
        flex-wrap: wrap;
        .radio-item {
            line-height: 72rpx;
            height: 72rpx;
            margin-bottom: 20rpx;
            margin-right: 20rpx;
            border-radius: 14rpx;
            padding: 0 30rpx;
            @apply line-clamp-1  text-main bg-page;
            &--active {
                @apply bg-primary-light-9 text-primary;
            }
        }
    }
}
</style>
