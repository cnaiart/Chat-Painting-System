<template>
    <u-popup
        v-model="showModel"
        mode="bottom"
        safe-area-inset-bottom
        :mask="false"
        height="100%"
        z-index="99999"
        :custom-style="{
            background: '#191820'
        }"
    >
        <view class="h-full flex flex-col py-[120rpx] text-white">
            <view class="mt-[80rpx] flex flex-col justify-center items-center">
                <view class="relative">
                    <u-image
                        shape="circle"
                        width="220"
                        height="220"
                        :src="appStore.getChatConfig.chat_logo"
                    />
                    <view
                        v-if="chatStatus == ChatStatus.PLAYING"
                        class="bubble-loading flex justify-center absolute"
                    >
                        <loading size="9rpx" class="mt-[24rpx]" />
                    </view>
                </view>

                <view class="text-3xl mt-[20rpx]">{{
                    appStore.getWebsiteConfig.shop_name
                }}</view>
            </view>
            <view class="flex-1 flex flex-col justify-center items-center">
                <canvas
                    v-show="isRecording"
                    :style="{
                        width: `${canvasOptions.width}px`,
                        height: `${canvasOptions.height}px`
                    }"
                    :canvas-id="canvasOptions.id"
                />
                <image
                    v-if="
                        [
                            ChatStatus.TRANSFER,
                            ChatStatus.PLAYING,
                            ChatStatus.THINKING
                        ].includes(chatStatus)
                    "
                    :src="loadingPath"
                    class="w-[250rpx] h-[250rpx]"
                />
            </view>
            <view>
                <view class="flex justify-center relative">
                    <view>
                        <view class="action-btn" @click="showModel = false">
                            <u-icon name="close" :size="32"></u-icon>
                        </view>
                    </view>
                    <view class="relative flex justify-center items-center">
                        <view
                            class="w-[170rpx] h-[170rpx] rounded-[50%] bg-primary opacity-30 absolute"
                        >
                        </view>
                        <button
                            class="flex justify-center items-center w-[140rpx] h-[140rpx] rounded-[50%] bg-primary text-btn-text relative z-10"
                            @click="startRecord"
                            hover-class="none"
                        >
                            <u-icon v-if="isCanRecord" name="mic" :size="60" />
                            <view v-if="isRecording" class="stop"> </view>
                            <loading
                                v-if="
                                    [
                                        ChatStatus.TRANSFER,
                                        ChatStatus.THINKING,
                                        ChatStatus.INITIALING
                                    ].includes(chatStatus)
                                "
                            />
                            <u-icon
                                v-if="chatStatus == ChatStatus.PLAYING"
                                name="pause-circle-fill"
                                :size="60"
                            />
                        </button>
                    </view>
                </view>
                <view
                    class="flex flex-col justify-center items-center mt-[42rpx] text-xs"
                >
                    <view>
                        {{ statusToTextMap[chatStatus] }}
                    </view>
                </view>
            </view>
        </view>
        <guided-popup ref="guidedPopupRef" />
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
import {
    audioTransfer,
    chatSendText,
    getChatBroadcast,
    getChatRecord
} from '@/api/chat'
import { useAudioPlay } from '@/hooks/useAudioPlay'
import {
    AudioGraphUserOptions,
    useRecorder,
    useRenderAudioGraph
} from '@/hooks/useRecorder'
import { useAppStore } from '@/stores/app'
import { reactive } from 'vue'
import { shallowRef } from 'vue'
import { computed } from 'vue'
import { watch } from 'vue'
import { ref } from 'vue'
import { parse } from 'jsonc-parser'
import config from '@/config'

enum ChatStatus {
    INITIALING,
    //默认
    DEFAULT,
    //录音中
    RECORDING,
    //无音频输入
    RECORDING_EMPTY,
    //录音转文字中
    TRANSFER,
    // 转换的文字为空
    TRANSFER_EMPTY,
    // 转文字出错
    TRANSFER_ERROR,
    // 正在思考中
    THINKING,
    // 思考错误
    THINKING_ERROR,
    // 正在回复中
    PLAYING
}

const props = defineProps<{
    show: boolean
    data: Record<string, any>
}>()

const emit = defineEmits<{
    (event: 'update:show', show: boolean): void
    (event: 'update'): void
}>()

const statusToTextMap = reactive({
    [ChatStatus.INITIALING]: '正在初始化对话...',
    [ChatStatus.DEFAULT]: '点击开始说话',
    [ChatStatus.RECORDING]: '我在听，您请说...',
    [ChatStatus.RECORDING_EMPTY]: '我好像没太听清，你可以再说一遍',
    [ChatStatus.TRANSFER]: '正在加载中，请稍后...',
    [ChatStatus.TRANSFER_EMPTY]: '您好像没有说话，点击按钮重试',
    [ChatStatus.TRANSFER_ERROR]: '出错了，请重试',
    [ChatStatus.THINKING]: '稍等，让我想一想',
    [ChatStatus.THINKING_ERROR]: '出错了，请重试',
    [ChatStatus.PLAYING]: '正在回复，点击按钮打断'
})
const loadingUrl = 'resource/image/api/default/voice.gif'
let loadingPath = `${config.baseUrl}${loadingUrl}`
//#ifdef H5
loadingPath = `${
    config.baseUrl === '/' ? `${location.origin}/` : config.baseUrl
}${loadingUrl}`
//#endif
const appStore = useAppStore()
const guidedPopupRef = shallowRef()
const showModel = computed({
    get() {
        return props.show
    },
    set(value) {
        emit('update:show', value)
    }
})
const isCanRecord = computed(() => {
    return [
        ChatStatus.DEFAULT,
        ChatStatus.TRANSFER_ERROR,
        ChatStatus.TRANSFER_EMPTY,
        ChatStatus.TRANSFER_ERROR,
        ChatStatus.THINKING_ERROR
    ].includes(chatStatus.value)
})
const isAutoStop = ref(false)
const chatStatus = ref(ChatStatus.INITIALING)
const startTimer = ref(0)
const hasVoice = ref(false)
const recordTimer = ref(0)
const { start, stop, isRecording, authorize, close } = useRecorder({
    onstart() {
        hasVoice.value = false
        startTimer.value = Date.now()
        chatStatus.value = ChatStatus.RECORDING
    },
    async onstop(result) {
        draw(null, 0)
        stopRender()
        if (!isAutoStop.value) {
            if (!isCanRecord.value) {
                chatStatus.value = ChatStatus.DEFAULT
            }

            return
        }
        isAutoStop.value = false
        chatStatus.value = ChatStatus.TRANSFER
        try {
            const res: any = await audioTransfer(result.tempFilePath, {
                type: 3
            })
            if (!res.text) {
                chatStatus.value = ChatStatus.TRANSFER_EMPTY
                return
            }
            chat(res.text, res.file)
        } catch (error) {
            chatStatus.value = ChatStatus.TRANSFER_ERROR
        }
    },
    ondata(result) {
        render(result)
        const now = Date.now()

        // 过滤掉杂音
        if (result.powerLevel > 8) {
            clearTimeout(recordTimer.value)
            chatStatus.value = ChatStatus.RECORDING
            hasVoice.value = true
            startTimer.value = now
            //有音频输入，延时1s停止录音
            recordTimer.value = setTimeout(() => {
                isAutoStop.value = true
                stop()
            }, 2000)
        }
        // 5s内没有语音输入
        if (now - startTimer.value >= 5000) {
            if (!hasVoice.value) {
                if (chatStatus.value == ChatStatus.RECORDING_EMPTY) {
                    //第二个5s无声音输入，停止录音
                    stop()
                    chatStatus.value = ChatStatus.TRANSFER_EMPTY
                    return
                }
                chatStatus.value = ChatStatus.RECORDING_EMPTY
                startTimer.value = now
            }
        }
    }
})

const canvasOptions = reactive<AudioGraphUserOptions>({
    id: 'audio-canvas',
    width: 100,
    height: 40,
    minHeight: 5
})
const { render, stopRender, draw } = useRenderAudioGraph(canvasOptions)

let streamReader: any = null
const recordId = ref(0)

const { play, pause, destroy } = useAudioPlay({
    api: getChatBroadcast,
    dataTransform(data) {
        return data.file_url
    },
    params: reactive({
        records_id: recordId,
        content: 0,
        type: 3
    }),
    onstart() {
        chatStatus.value = ChatStatus.PLAYING
    },
    onstop() {
        destroy()
        showModel.value && start()
    }
})

const chat = (text: string, filePath: string) => {
    chatStatus.value = ChatStatus.THINKING
    try {
        chatSendText(
            {
                ...props.data,
                voice_file: filePath,
                question: text
            },
            {
                onstart(reader) {
                    streamReader = reader
                },
                onmessage(value) {
                    value
                        .trim()
                        .split('data:')
                        .forEach(async (text) => {
                            const dataJson = parse(text)
                            if (!dataJson) return
                            const { event, data, code } = dataJson
                            if (event === 'error' && code === 101) {
                                chatStatus.value = ChatStatus.DEFAULT
                                guidedPopupRef.value?.open()
                            } else if (event === 'error') {
                                chatStatus.value = ChatStatus.THINKING_ERROR
                                console.log(dataJson)
                                uni.$u.toast(data)
                            }
                            if (event === 'finish') {
                                setTimeout(async () => {
                                    try {
                                        const list = await getChatList()
                                        recordId.value =
                                            list[list.length - 1].id

                                        showModel.value && (await play())
                                        emit('update')
                                    } catch (error) {
                                        chatStatus.value =
                                            ChatStatus.THINKING_ERROR
                                    }
                                }, 100)
                            }
                        })
                },
                onclose() {}
            }
        )
    } catch (error) {
        chatStatus.value = ChatStatus.THINKING_ERROR
    }
}

const getChatList = async () => {
    const data = await getChatRecord({
        type: props.data.type,
        other_id: props.data.type == 1 ? undefined : props.data.other_id,
        category_id: props.data.type == 1 ? props.data.other_id : undefined,
        page_no: 1,
        page_size: 1
    })
    return data.lists || []
}
const chatClose = () => {
    //#ifdef H5
    streamReader?.cancel()
    //#endif
    //#ifdef MP-WEIXIN
    streamReader?.abort()
    //#endif
}
const startRecord = async () => {
    if (isRecording.value) {
        stop()
        return
    }
    if (isCanRecord.value) {
        start()
        return
    }

    if (chatStatus.value == ChatStatus.PLAYING) {
        pause()
    }
}

watch(showModel, async (value) => {
    if (!value) {
        if (isRecording.value) {
            stop()
        }
        chatClose()
        // close()
        pause()
        emit('update')
        destroy()
    } else {
        chatStatus.value = ChatStatus.INITIALING
        // #ifdef H5
        await authorize()
        // #endif
        chatStatus.value = ChatStatus.DEFAULT
        draw(null, 0)
    }
})
</script>

<style lang="scss" scoped>
.action-btn {
    position: absolute;
    width: 76rpx;
    height: 76rpx;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #999;
    top: 50%;
    left: 64rpx;
    transform: translateY(-50%);
}
.stop {
    width: 44rpx;
    height: 44rpx;
    border-radius: 10rpx;
    @apply bg-btn-text;
}
.bubble-loading {
    width: 60rpx;
    height: 60rpx;
    background: url(../../../static/images/common/bubble_bg.png) no-repeat;

    background-size: cover;
    right: 0;
    top: 0;
    transform: translate(20%, -40%);
}
</style>
