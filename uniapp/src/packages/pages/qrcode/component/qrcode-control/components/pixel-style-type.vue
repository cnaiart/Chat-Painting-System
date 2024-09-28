<template>
    <view class="mt-[24px]">
        <view class="text-base font-bold">码点形状</view>
        <view class="flex flex-wrap mt-[20rpx]">
            <view
                v-for="(item, index) in template"
                :key="index"
                class="pixel flex justify-center items-center"
                :class="{
                    'pixel-active': modelValue == item.value
                }"
                @click="emit('update:modelValue' , item.value)"
            >
                <img class="w-[40rpx] h-[40rpx]" :src="item.preview_img" />
                <span class="ml-[14rpx]">{{ item.name }} </span>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        template: any
        modelValue?: any
    }>(),
    {
        template: [],
        modelValue: 'random'
    }
)
</script>

<style lang="scss" scoped>
.pixel {
    transition: all 0.5s;
    user-select: none;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-right: 10px;
    width: 80px;
    height: 40px;
    border-radius: 4px;
    border: 1px solid #dcdfe6;
}
.pixel:hover {
    border: 1px solid var(--color-primary);
    background-color: var(--color-primary-light-9);
}
.pixel-active {
    border: 1px solid var(--color-primary);
}
</style>
