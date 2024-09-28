<template>
    <view class="loading">
        <view class="dot flex items-center">
            <view class="dot-item"></view>
            <view class="dot-item"></view>
            <view class="dot-item"></view>
        </view>
    </view>
</template>
<script setup lang="ts">
import { computed } from 'vue'
import Color from 'color'
const props = withDefaults(
    defineProps<{
        size?: string
        color?: string
        alpha?: number
    }>(),
    {
        size: '22rpx',
        color: '#ffffff',
        alpha: 0.3
    }
)
const lightColor = computed(() => {
    return Color(props.color)!.alpha(props.alpha).rgbaString() || '#fff'
})
</script>
<style lang="scss" scoped>
@keyframes dot-loading {
    0% {
        background-color: v-bind(color);
    }
    50% {
        background: v-bind(color);
        opacity: 0.7;
    }

    100% {
        background: v-bind(lightColor);
    }
}

.dot {
    .dot-item {
        width: v-bind(size);
        height: v-bind(size);
        border-radius: 50%;
        animation: dot-loading 1s linear infinite;
        &:nth-child(1) {
            background: v-bind(color);
        }
        &:nth-child(2) {
            background: v-bind(color);
            opacity: 0.7;
            margin-left: calc(v-bind(size) / 2);
            animation-delay: 0.2s;
        }
        &:nth-child(3) {
            background: v-bind(lightColor);
            margin-left: calc(v-bind(size) / 2);
            animation-delay: 0.4s;
        }
    }
}
</style>
