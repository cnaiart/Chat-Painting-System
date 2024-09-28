<template>
    <view class="audio" @click="togglePlay" :class="{ reverse }">
        <view
            :style="{
                transform: `rotate(${reverse ? '135deg' : '-45deg'})`
            }"
        >
            <view
                class="wifi-symbol"
                :class="{
                    playing: isPlaying
                }"
            >
                <view class="wifi-circle first"></view>
                <view class="wifi-circle second"></view>
                <view class="wifi-circle third"></view>
            </view>
        </view>
        <view class="duration">{{
            duration ? Math.round(duration) + '"' : ''
        }}</view>
    </view>
</template>
<script setup lang="ts">
import { ref, shallowRef, watch } from 'vue'
import { useAudio } from '@/hooks/useAudio'
const props = withDefaults(
    defineProps<{
        url: string
        color?: string
        bgColor?: string
        reverse?: boolean
    }>(),
    {
        color: '#000',
        bgColor: '#fff',
        reverse: false
    }
)

const { pause, play, duration, isPlaying, setUrl } = useAudio()
const togglePlay = () => {
    if (isPlaying.value) {
        pause()
    } else {
        play()
    }
}
watch(
    () => props.url,
    (value) => {
        if (value) {
            setUrl(value)
        }
    },
    {
        immediate: true
    }
)
</script>

<style lang="scss" scoped>
.audio {
    padding: 0 20rpx;
    background-color: v-bind(bgColor);
    display: inline-flex;
    height: 58rpx;
    align-items: center;
    border-radius: 100rpx;
    min-width: 120rpx;
    &.reverse {
        flex-direction: row-reverse;
        // . {
        //     transform: rotateZ(135deg);
        // }
    }
    .wifi-symbol {
        margin: 10rpx;
        position: relative;
        width: 30rpx;
        height: 30rpx;
        box-sizing: border-box;
        overflow: hidden;

        z-index: 999;
    }

    .wifi-circle {
        border: 4rpx solid v-bind(color);
        border-radius: 50%;
        position: absolute;
        left: 0;
        top: 0;
        transform: scale(0.9) rotate(45deg) translateX(-50%);
        transform-origin: center center;
    }
    .first {
        width: 6rpx;
        height: 6rpx;
        background: v-bind(color);
        opacity: 1;
    }
    .second {
        width: 25rpx;
        height: 25rpx;
    }
    .third {
        width: 40rpx;
        height: 40rpx;
    }
    .duration {
        color: v-bind(color);
    }
    .playing {
        .second {
            opacity: 0;
            animation: fadeInOut 1s infinite 0.2s;
        }

        .third {
            opacity: 0;
            animation: fadeInOut 1s infinite 0.4s;
        }
    }

    @keyframes fadeInOut {
        0% {
            opacity: 0; /*初始状态 透明度为0*/
        }
        100% {
            opacity: 1; /*结尾状态 透明度为1*/
        }
    }
}
</style>
