<template>
    <view class="mt-[30rpx]">
        <view class="text-base">二维码风格</view>
        <view class="style-container overflow-hidden">
            <view
                v-for="(item, index) in template"
                :key="index"
                class="float-left style-item"
                :class="{
                    'style-active': item.value == modelValue
                }"
                @click="handleClick(item.value)"
            >
<!--                <image-->
<!--                    class="w-[200rpx] h-[200rpx]"-->
<!--                    :src="item.preview_img"-->
<!--                    mode="aspectFill"-->
<!--                    alt="风格模版"-->
<!--                />-->
                <view class="text-center py-[6rpx]">
                    {{ item.name }}
                </view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
    (event: 'update:way', value: number): void
}>()
const props = withDefaults(
    defineProps<{
        template?: any
        modelValue?: any
        way: any
    }>(),
    {
        template: [],
        modelValue: '',
        way: 1
    }
)

const handleClick = (val: string) => {
    if (val != props.modelValue) {
        emit('update:modelValue', val)
        emit('update:way', 2)
    } else {
        emit('update:modelValue', '')
        emit('update:way', 1)
    }
}
</script>

<style scoped>
.style-container .style-item {
    margin-top: 20rpx;
    margin-right: 20rpx;
    border-radius: 8rpx;
    width: 216rpx;
    padding: 4rpx;
    border: 2rpx solid #e5e5e5;
}
.style-container .style-item:nth-child(3n) {
    margin-right: 0px;
}
.style-container .style-active {
    border: 2rpx solid;
    @apply border-primary;
}
</style>
