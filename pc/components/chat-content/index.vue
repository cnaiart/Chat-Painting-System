<template>
    <div class="chat-content">
        <!-- Text Message -->
        <template v-if="MsgMap['TEXT'] === type">
            <div class="chat-text">
                <ClientOnly>
                    <Markdown
                        v-if="useMarkdown"
                        :content="content"
                        :line-numbers="lineNumbers"
                        :typing="loading"
                    ></Markdown>
                    <div
                        v-else
                        class="whitespace-pre-line"
                        :class="{
                            'wait-typing': loading
                        }"
                    >
                        {{ content }}
                    </div>
                </ClientOnly>
            </div>
            <ElButton
                v-if="showCopyBtn"
                class="mt-2"
                link
                @click="emit('copy')"
            >
                <template #icon>
                    <Icon name="el-icon-CopyDocument" />
                </template>
                复制
            </ElButton>
            <template v-if="showVoiceBtn">
                <ElButton v-if="audioPlaying" class="mt-2" link @click="pause">
                    <template #icon>
                        <Icon name="local-icon-audio_voice" />
                    </template>
                    停止
                </ElButton>
                <ElButton
                    v-else
                    class="mt-2"
                    link
                    @click="play"
                    :loading="audioLoading"
                >
                    <template #icon>
                        <Icon name="local-icon-audio_voice" />
                    </template>
                    朗读
                </ElButton>
            </template>
        </template>
    </div>
</template>

<script lang="ts" setup>
import { E_Msg } from '@/enums/appEnums'
import { getChatBroadcast } from '~/api/chat'

const props = defineProps({
    useMarkdown: {
        type: Boolean,
        default: false
    },
    lineNumbers: {
        type: Boolean,
        default: true
    },
    loading: {
        type: Boolean,
        default: false
    },
    type: {
        type: Number,
        default: 1
    },
    content: {
        type: [String],
        default: ''
    },
    index: {
        type: [Number]
    },
    recordId: {
        type: [String, Number]
    },
    showVoiceBtn: {
        type: Boolean,
        default: false
    },
    showCopyBtn: {
        type: Boolean,
        default: false
    }
})

const { recordId, index } = toRefs(props)
const params = reactive({
    records_id: recordId,
    content: index,
    type: 1
})
const emit = defineEmits(['copy'])
const { play, audioPlaying, pause, audioLoading } = useAudioPlay({
    api: getChatBroadcast,
    dataTransform(data) {
        return data.file_url
    },
    params: params
})
const MsgMap = computed(() => {
    return Object.freeze({ ...E_Msg })
})
</script>

<style lang="scss" scoped>
@mixin typing {
    display: inline-block;
    content: '';
    width: 5px;
    height: 14px;
    transform: translate(4px, 2px) scaleY(1.3);
    background-color: #1a202c;
    animation: blink 0.6s infinite;
}
.wait-typing::after {
    @include typing;
}

@keyframes blink {
    from,
    to {
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
}
@keyframes typingFade {
    0% {
        opacity: 0;
    }
    50% {
        opacity: 100%;
    }
    100% {
        opacity: 100%;
    }
}
.chat-content {
    .goods-message {
        width: 300px;
        .goods-image {
            min-width: 80px;
            min-height: 80px;
        }

        .goods-price {
            color: #ff2c3c;
        }
    }
    .chat-text {
        .typing {
            display: inline-block;
            vertical-align: -4px;
            height: 18px;
            width: 3px;
            background-color: var(--el-color-primary);
            animation: typingFade 0.6s infinite alternate;
        }
    }
}
</style>
