<template>
    <div class="create-record-item">
        <div class="flex items-center">
            <div class="mr-auto line-clamp-1 text-lg font-medium">
                {{ title }}
            </div>
        </div>
        <div
            class="mt-[10px]"
            :class="{
                'line-clamp-[8]': overflow
            }"
        >
            <ChatContent
                :content="content"
                :loading="typing"
                :use-markdown="!!appStore.config?.chat?.is_markdown"
            />
        </div>
        <div class="flex items-center flex-none mt-[10px]">
            <div class="text-xs text-tx-secondary mr-auto">
                {{ time }}
            </div>
            <template v-if="showVoice">
                <ElButton
                    v-if="audioPlaying"
                    color="#F2F2F2"
                    round
                    size="small"
                    @click.stop="pause"
                >
                    <template #icon>
                        <Icon name="local-icon-audio_voice" />
                    </template>
                    停止
                </ElButton>
                <ElButton
                    v-else
                    round
                    size="small"
                    color="#F2F2F2"
                    @click.stop="play"
                    :loading="audioLoading"
                >
                    <template #icon>
                        <Icon name="local-icon-audio_voice" />
                    </template>
                    朗读
                </ElButton>
            </template>
            <ElButton
                v-if="showRewrite"
                round
                size="small"
                color="#F2F2F2"
                @click.stop="emit('again')"
            >
                再来一次
            </ElButton>
            <ElButton
                v-if="showRewrite"
                round
                size="small"
                color="#F2F2F2"
                @click.stop="emit('rewrite')"
            >
                重写
            </ElButton>
            <ElButton
                v-if="showCopy"
                round
                size="small"
                color="#F2F2F2"
                @click.stop="emit('copy', content)"
            >
                复制
            </ElButton>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { getChatBroadcast } from '~/api/chat'
import { useAppStore } from '~/stores/app'

const appStore = useAppStore()
const props = withDefaults(
    defineProps<{
        typing?: boolean
        title?: string
        time?: string
        content?: string
        showCopy?: boolean
        showRewrite?: boolean
        overflow?: boolean
        recordId?: string | number
        index: number
        showVoice?: boolean
    }>(),
    {
        typing: false,
        title: '',
        time: '',
        content: '',
        overflow: false,
        showCopy: true,
        showRewrite: false,
        showVoice: false
    }
)

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
const emit = defineEmits<{
    (event: 'copy', text: string): void
    (event: 'rewrite'): void
    (event: 'again'): void
}>()
</script>
<style lang="scss" scoped>
.create-record-item {
    border-radius: 10px;
    padding: 16px;
    @apply bg-white;
}
</style>
