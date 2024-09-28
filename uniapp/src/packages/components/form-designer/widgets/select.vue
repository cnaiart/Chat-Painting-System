<template>
    <view class="widget-select">
        <view class="flex-1" @click="showSelectPopup = true">
            <view
                class="h-[70rpx]"
                :class="!value ? 'text-muted' : 'text-black'"
            >
                {{ value || '请输入' }}
            </view>
        </view>
        <view
            class="text-muted arrow-icon"
            :class="{
                'arrow-icon--top': showSelectPopup
            }"
        >
            <u-icon name="arrow-down" />
        </view>
    </view>
    <u-select
        v-model="showSelectPopup"
        :list="normalizedOptions"
        @confirm="handleSelect"
    />
</template>

<script setup lang="ts">
import { computed, onBeforeMount, ref } from 'vue'

const props = withDefaults(
    defineProps<{
        defaultValue: string
        modelValue: any
        options: string[]
        placeholder?: string
    }>(),
    {
        modelValue: '',
        placeholder: '请输入',
        options: () => []
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
const showSelectPopup = ref(false)
const normalizedOptions = computed(() => {
    return props.options.map((item) => ({
        value: item,
        label: item
    }))
})

const handleSelect = (e: any) => {
    value.value = e[0].value
}

onBeforeMount(() => {
    if (props.defaultValue) {
        value.value = props.defaultValue
    }
})
</script>
<style lang="scss" scoped>
.widget-select {
    border-radius: 10rpx;
    padding: 10rpx 20rpx;
    flex: 1;
    display: flex;
    align-items: center;
    position: relative;
    @apply bg-page;
    .u-input {
        :deep(.u-input__input) {
            @apply text-base;
        }
    }
    .arrow-icon {
        transition: all 0.3s;
        &--top {
            transform: rotateZ((-180deg));
        }
    }
}
</style>
