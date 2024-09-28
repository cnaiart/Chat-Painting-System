<template>
    <view class="create-record-item">
        <view class="flex items-center">
            <view
                class="text-lg line-clamp-1 font-medium"
                @click="emit('click-title', title)"
            >
                {{ title }}
            </view>
        </view>
        <view class="flex items-center text-xs">
            <view class="text-muted mt-[10rpx] mr-auto">{{ time }}</view>
        </view>

        <view class="mt-[10px] bg-page p-[20rpx] rounded-[14rpx]">
            <view>
                <u-read-more v-if="overflow" :toggle="true">
                    <view class="indent-0 text-main">
                        <text-item
                            :is-markdown="!!appStore.getChatConfig.is_markdown"
                            :content="content"
                        />
                    </view>
                </u-read-more>
                <view v-else>
                    <text-item
                        :is-markdown="!!appStore.getChatConfig.is_markdown"
                        :content="content"
                    />
                </view>
            </view>
            <view class="flex justify-end">
                <template v-if="showVoice">
                    <view
                        v-if="!audioPlaying"
                        class="mt-[20rpx] mr-[20rpx] flex bg-white px-[26rpx] py-[10rpx] rounded-full text-content"
                        @click="play"
                    >
                        <u-loading
                            v-if="audioLoading"
                            mode="flower"
                            :size="26"
                        ></u-loading>
                        <u-icon v-else name="volume" />
                        <span class="text-xs ml-[10rpx]">朗读</span>
                    </view>
                    <view
                        v-else
                        class="mt-[20rpx] mr-[20rpx] flex bg-white px-[26rpx] py-[10rpx] rounded-full text-content"
                        @click="pause"
                    >
                        <u-icon name="volume" />
                        <span class="text-xs ml-[10rpx]">停止</span>
                    </view>
                </template>
                <view
                    v-if="showRewrite"
                    class="mt-[20rpx] mr-[20rpx] flex bg-white px-[26rpx] py-[10rpx] rounded-full text-content"
                    @click.stop="emit('rewrite')"
                >
                    <u-icon name="edit-pen" class="mr-[8rpx]" />
                    <span class="text-xs ml-[10rpx]"> 重写</span>
                </view>
                <view
                    v-if="showCopy"
                    class="mt-[20rpx] flex bg-white px-[26rpx] py-[10rpx] rounded-full text-content"
                    @click.stop="emit('copy', content)"
                >
                    <u-icon name="file-text" />
                    <span class="text-xs ml-[10rpx]"> 复制</span>
                </view>
            </view>
        </view>
    </view>
</template>
<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import TextItem from '@/components/chat-record-item/text-item.vue'
import { useAudioPlay } from '@/hooks/useAudioPlay'
import { getChatBroadcast } from '@/api/chat'
const appStore = useAppStore()
const props = withDefaults(
    defineProps<{
        title?: string
        time?: string
        content?: string
        overflow?: boolean
        showCopy?: boolean
        showRewrite?: boolean
        showVoice?: boolean
        recordId?: number | string
        index?: number
    }>(),
    {
        title: '',
        time: '',
        content: '',
        overflow: false,
        showCopy: true,
        showRewrite: false,
        showVoice: false
    }
)
const emit = defineEmits<{
    (event: 'click-title', text: string): void
    (event: 'copy', text: string): void
    (event: 'delete'): void
    (event: 'rewrite'): void
}>()

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
<style lang="scss">
.create-record-item {
    border-radius: 20rpx;
    padding: 30rpx;
    @apply bg-white;
}
.line-clamp-5 {
    text-overflow: ellipsis;
    @apply line-clamp-[5];
}
</style>
