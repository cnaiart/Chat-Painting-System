<template>
    <u-popup
        v-model="showModel"
        mode="bottom"
        safe-area-inset-bottom
        :mask="false"
        height="100%"
        :custom-style="{
            background:
                'linear-gradient(180deg,rgba(18,19,23,.08),rgba(18,19,23,.98) 96%)'
        }"
    >
        <view
            class="h-full flex flex-col-reverse recorder"
            @touchend="stopRecord"
            @touchcancel="stopRecord"
        >
            <view class="px-[60rpx] pb-[140rpx]">
                <view class="px-[60rpx]">
                    <view class="bubble-text relative">
                        <u-input
                            class="flex-1"
                            v-show="userInput || (!isTransfer && !isRecording)"
                            type="textarea"
                            v-model="userInput"
                            placeholder="请输入您的问题"
                            maxlength="-1"
                            :cursor-spacing="120"
                            :auto-height="true"
                            confirm-type="send"
                            :fixed="true"
                        />
                        <view class="wave" v-if="!userInput && isRecording">
                            <view
                                class="wave-item"
                                v-for="(item, index) in 10"
                                :key="item"
                                :style="{
                                    '--delay': `${index / 10}s`
                                }"
                            >
                            </view>
                        </view>
                        <view v-if="isTransfer" class="loading">
                            <u-loading
                                mode="flower"
                                color="#fff"
                                :size="40"
                            ></u-loading>
                        </view>
                    </view>
                </view>
                <view class="text-white mt-[40rpx] text-center text-xs">
                    <template v-if="isTransfer"> 文字转换中... </template>
                    <template v-else>
                        {{
                            isRecording
                                ? '松开手指，转换文字'
                                : '点击气泡可编辑文字'
                        }}
                    </template>
                </view>
                <view class="flex justify-between text-white">
                    <view
                        class="action-btn bg-content"
                        @click="showModel = false"
                    >
                        <u-icon name="close" :size="32"></u-icon>
                    </view>
                    <view
                        class="action-btn bg-success"
                        @click="emit('success', userInput)"
                    >
                        <u-icon name="checkmark" :size="34"></u-icon>
                    </view>
                </view>
                <view class="flex justify-center">
                    <view class="relative flex justify-center items-center">
                        <view
                            class="w-[170rpx] h-[170rpx] rounded-[50%] bg-primary opacity-30 absolute"
                        >
                        </view>
                        <button
                            class="flex justify-center items-center w-[130rpx] h-[130rpx] rounded-[50%] bg-primary text-btn-text relative z-10"
                            @longpress="startRecord"
                            hover-class="none"
                        >
                            <!-- 添加空格的目的是：touchend事件有时候不触发 -->
                            &nbsp;
                            <u-icon v-if="!isRecording" name="mic" :size="60" />
                            <loading v-else> </loading>
                            &nbsp;
                        </button>
                    </view>
                </view>
            </view>
        </view>
    </u-popup>
</template>
<script lang="ts">
export default {
    options: {
        styleIsolation: 'shared'
    }
}
</script>
<script setup lang="ts">
import { audioTransfer } from '@/api/chat'
import { useRecorder } from '@/hooks/useRecorder'
import { onUnmounted } from 'vue'
import { shallowRef } from 'vue'
import { computed } from 'vue'
import { watch } from 'vue'
import { ref } from 'vue'
const props = defineProps<{
    show: boolean
}>()

const emit = defineEmits<{
    (event: 'update:show', show: boolean): void
    (event: 'success', text: string): void
}>()

const showModel = computed({
    get() {
        return props.show
    },
    set(value) {
        emit('update:show', value)
    }
})
const userInput = ref('')
const isTransfer = ref(false)
const { start, isRecording, stop, authorize, close } = useRecorder({
    async onstop(result) {
        if (result.duration < 1000) {
            uni.$u.toast('说话时间太短')
            return
        }
        isTransfer.value = true
        try {
            const data: any = await audioTransfer(result.tempFilePath, {
                type: 2
            })
            userInput.value += data.text
        } finally {
            isTransfer.value = false
        }
    }
})
const startRecord = async () => {
    if (isRecording.value || isTransfer.value) {
        return
    }
    try {
        await start()
    } catch (error) {
        uni.$u.toast(error)
    }
}

const stopRecord = () => {
    if (isRecording.value) {
        stop()
    }
}

watch(showModel, (value) => {
    if (!value) {
        stopRecord()
    } else {
        userInput.value = ''
        // startRecord()
    }
})

defineExpose({
    authorize: authorize,
    startRecord,
    stopRecord,
    closeRecord: close
})
</script>

<style lang="scss" scoped>
@keyframes dot-loading {
    0% {
        background-color: #fff;
    }
    50% {
        background: #fff;
        opacity: 0.6;
    }

    100% {
        background: rgba(256, 256, 256, 0.3);
    }
}
@keyframes wave {
    0%,
    40%,
    100% {
        transform: scaleY(1);
    }
    20% {
        transform: scaleY(0.4);
    }
}
.recorder {
    .bubble-text {
        position: relative;
        padding: 30rpx;
        border-radius: 24rpx;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 120rpx;
        @apply bg-primary;
        &::before {
            content: ' ';
            width: 10px;
            height: 10px;
            position: absolute;
            left: 50%;
            bottom: -5px;
            -webkit-transform: translateX(-50%) rotate(45deg);
            transform: translateX(-50%) rotate(45deg);
            @apply bg-primary;
        }
        :deep() {
            .u-input__textarea {
                --line-height: 40rpx;
                --line-num: 6;
                height: auto;
                min-height: var(--line-height) !important;
                max-height: calc(var(--line-height) * var(--line-num));
                font-size: 28rpx;
                box-sizing: border-box;
                padding: 0;
                line-height: var(--line-height);
                @apply text-white;
                .uni-textarea-textarea {
                    max-height: calc(var(--line-height) * var(--line-num));
                    overflow-y: auto !important;
                }
            }
        }
    }
    .dot {
        .dot-item {
            width: 20rpx;
            height: 20rpx;
            border-radius: 50%;
            animation: dot-loading 1s linear infinite;
            &:nth-child(1) {
                background: #fff;
            }
            &:nth-child(2) {
                background: #fff;
                opacity: 0.6;
                margin-left: 6px;
                animation-delay: 0.2s;
            }
            &:nth-child(3) {
                background: rgba(256, 256, 256, 0.3);
                margin-left: 6px;
                animation-delay: 0.4s;
            }
        }
    }
    .wave {
        display: flex;
        .wave-item {
            display: inline-block;
            width: 6rpx;
            height: 30rpx;
            background-color: #fff;
            margin: 0 5rpx;
            border-radius: 2rpx;
            animation: wave 1.2s infinite ease-in-out;
            animation-delay: var(--delay);
        }
    }
    .action-btn {
        width: 76rpx;
        height: 76rpx;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
}
</style>
