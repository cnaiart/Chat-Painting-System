<template>
    <template v-if="isMarkdown">
        <ua-markdown :content="content"></ua-markdown>
    </template>
    <template v-else>
        <text
            user-select
            class="whitespace-pre-line leading-[40rpx] select-text"
        >
            {{ content }}
        </text>
    </template>
    <view class="flex items-center" v-if="!loading">
        <view
            v-if="showCopyBtn"
            class="text-content text-sm flex items-center mr-[20rpx] mt-[16rpx]"
            @click="copy(content)"
        >
            <image
                class="w-[26rpx] h-[26rpx] mr-[8rpx]"
                src="@/static/images/common/icon_copy.png"
                alt="复制"
            />
            复制
        </view>
        <template v-if="showVoiceBtn">
            <view
                v-if="!audioPlaying"
                class="text-content text-sm flex items-center mt-[16rpx]"
                @click="play()"
            >
                <u-loading
                    v-if="audioLoading"
                    mode="flower"
                    class="mr-[8rpx]"
                    :size="26"
                ></u-loading>
                <u-icon v-else name="volume" class="mr-[8rpx]" />
                朗读
            </view>
            <view
                v-else
                class="text-content text-sm flex items-center mt-[16rpx]"
                @click="pause"
            >
                <u-icon name="volume" class="mr-[8rpx]" />
                停止
            </view>
        </template>
    </view>
</template>
<script lang="ts">
export default {
    options: {
        virtualHost: true
    },
    externalClasses: ['class']
}
</script>
<script setup lang="ts">
import { getChatBroadcast } from '@/api/chat'
import { useAudioPlay } from '@/hooks/useAudioPlay'
import { useCopy } from '@/hooks/useCopy'

const props = withDefaults(
    defineProps<{
        content: string
        isMarkdown: boolean
        loading?: boolean
        showCopyBtn?: boolean
        showVoiceBtn?: boolean
        recordId?: number | string
        index?: number
    }>(),
    {
        showCopyBtn: false,
        loading: false,
        showVoiceBtn: false
    }
)
const { copy } = useCopy()
const { play, audioPlaying, pause, audioLoading } = useAudioPlay({
    api: getChatBroadcast,
    dataTransform(data) {
        return data.file_url
    },
    params: {
        records_id: props.recordId,
        content: props.index,
        type: 1
    }
})
</script>
