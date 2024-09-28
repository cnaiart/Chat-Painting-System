<template>
    <view class="chat-record-item">
        <view :class="`chat-record-item__${type}`">
            <view>
                <u-icon
                    class="rounded-full overflow-hidden"
                    :name="type == 'left' ? avatar : userStore.userInfo.avatar"
                    :size="60"
                />
            </view>
            <view
                class="min-w-0 flex flex-col"
                :class="{ 'justify-end': type == 'right' }"
            >
                <div v-if="time" class="ml-[25rpx] mb-[20rpx] text-muted">
                    {{ time }}
                </div>
                <view :class="`chat-record-item__${type}-content`">
                    <view
                        class="mb-[20rpx] flex"
                        :class="{
                            'justify-end': type === 'right'
                        }"
                        v-if="audio"
                    >
                        <audio-play
                            :url="audio"
                            :reverse="type === 'right'"
                            :bg-color="'#fff'"
                        ></audio-play>
                    </view>
                    <view>
                        <template v-if="isArray(content)">
                            <view
                                v-for="(item, index) in content"
                                :key="index"
                                class="mb-[20rpx] last-of-type:mb-0"
                                :class="{
                                    'pt-[20rpx] border-t border-solid border-light border-0':
                                        index > 0
                                }"
                            >
                                <text-item
                                    :is-markdown="isMarkdown"
                                    :content="item"
                                    :loading="loading"
                                    :index="index"
                                    :record-id="recordId"
                                    :show-copy-btn="
                                        showCopyBtn && type === 'left'
                                    "
                                    :show-voice-btn="appStore.getIsVoiceOpen"
                                />
                            </view>
                        </template>
                        <template v-else>
                            <text-item
                                :is-markdown="isMarkdown"
                                :content="content"
                                :loading="loading"
                                :show-copy-btn="showCopyBtn && type === 'left'"
                            />
                        </template>

                        <view
                            class="flex items-center text-muted text-sm mt-[16rpx]"
                            v-if="loading"
                        >
                            <u-loading mode="flower"></u-loading>
                            <view class="ml-[10rpx]">加载中，请稍等</view>
                        </view>
                    </view>
                </view>

                <view
                    v-if="type == 'right'"
                    class="flex items-center justify-end pr-[20rpx] pt-[10rpx]"
                    @click="copy(content)"
                >
                    <image
                        class="w-[26rpx] h-[26rpx]"
                        src="@/static/images/icon/icon_copy.png"
                    ></image>
                    <text class="text-xs text-muted ml-[8rpx]">复制</text>
                </view>
                <slot name="footer"></slot>
                <view v-if="!loading && type === 'left'">
                    <view class="my-[16rpx] flex justify-end text-muted">
                        <view
                            v-if="showRewriteBtn"
                            class="text-xs flex items-center rounded-full ml-[20rpx]"
                            @click="emit('rewrite')"
                        >
                            <u-icon
                                name="reload"
                                class="mr-[8rpx]"
                                :size="30"
                            />
                            重写
                        </view>
                        <view
                            v-if="showCollectBtn"
                            class="text-xs flex items-center rounded-full ml-[20rpx]"
                            :class="{
                                'text-primary': isCollect,
                                'text-muted': !isCollect
                            }"
                            @click="handleCollect(recordId)"
                        >
                            <u-icon
                                :name="isCollect ? 'star-fill' : 'star'"
                                class="mr-[8rpx]"
                                :size="30"
                            />
                            <text class="text-muted">收藏</text>
                        </view>
                        <view
                            v-if="showPosterBtn"
                            class="text-xs flex items-center rounded-full ml-[20rpx]"
                            @click="emit('click-poster', recordId)"
                        >
                            <u-icon
                                name="photo"
                                class="mr-[8rpx]"
                                :size="30"
                            ></u-icon>
                            生成海报
                        </view>
                    </view>
                </view>

                <!--  生成海报  -->
            </view>
        </view>
    </view>
</template>

<script lang="ts" setup>
import { cancelCollectChatRecord, collectChatRecord } from '@/api/chat'
import { useCopy } from '@/hooks/useCopy'
import { useLockFn } from '@/hooks/useLockFn'
import { useUserStore } from '@/stores/user'
import { useAppStore } from '@/stores/app'
import TextItem from './text-item.vue'
import { isArray } from 'lodash-es'
import { computed } from 'vue'
// import
const props = withDefaults(
    defineProps<{
        recordId?: number | string
        type: 'left' | 'right'
        content: string
        showCopyBtn?: boolean
        showCollectBtn?: boolean
        showRewriteBtn?: boolean
        showPosterBtn?: boolean
        loading?: boolean
        index?: number
        isCollect?: number
        avatar: string
        time?: string
        audio?: string
    }>(),
    {
        showCollectBtn: true,
        showCopyBtn: true,
        showRewriteBtn: false,
        showPosterBtn: false,
        content: '',
        loading: false,
        time: ''
    }
)

const emit = defineEmits<{
    (event: 'close'): void
    (event: 'rewrite'): void
    (event: 'update', value: any): void
    (event: 'click-poster', value?: number | string): void
}>()
const userStore = useUserStore()
const appStore = useAppStore()
const { copy } = useCopy()

const { lockFn: handleCollect } = useLockFn(async (id: number | string) => {
    if (props.isCollect) {
        await cancelCollectChatRecord({
            collect_id: props.isCollect
        })
        emit('update', { index: props.index, value: 0 })
    } else {
        await collectChatRecord({
            records_id: id
        })
        emit('update', { index: props.index, value: 1 })
    }
})

const isMarkdown = computed(() => {
    return appStore.getChatConfig.is_markdown && props.type == 'left'
})
</script>

<style lang="scss" scoped>
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
.chat-record-item {
    padding: 0 20rpx;
    margin-bottom: 40rpx;
    &__left,
    &__right {
        display: flex;
        align-items: flex-start;
        min-height: 80rpx;
        &-content {
            display: inline-block;
            padding: 20rpx;
            max-width: 100%;
            border-radius: 10rpx;
            position: relative;
            min-width: 70rpx;
            min-height: 80rpx;
            &::before {
                content: '';
                display: block;
                width: 0;
                height: 0;
                position: absolute;
                top: 24rpx;
                border: 16rpx solid transparent;
            }
        }
        .text-typing {
            display: inline-block;
            vertical-align: -8rpx;
            height: 34rpx;
            width: 6rpx;
            background-color: $u-type-primary;
            animation: typingFade 0.4s infinite alternate;
        }
    }
    &__right {
        flex-direction: row-reverse;
    }
    &__left-content {
        margin-left: 25rpx;
        background-color: $u-bg-color;
        &::before {
            left: -30rpx;
            border-right-color: $u-bg-color;
        }
    }
    &__right-content {
        color: #fff;
        background-color: #4073fa;
        margin-right: 20rpx;
        &::before {
            right: -30rpx;
            border-left-color: #4073fa;
        }
    }
}
</style>
