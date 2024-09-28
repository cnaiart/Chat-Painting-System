<template>
    <view class="chat-scroll-view h-full flex flex-col">
        <view class="flex-1 min-h-0">
            <z-paging
                ref="pagingRef"
                v-model="chatList"
                use-chat-record-mode
                :auto="false"
                :safe-area-inset-bottom="true"
                :auto-clean-list-when-reload="false"
                :show-chat-loading-when-reload="true"
                :paging-style="{ bottom: keyboardIsShow ? 0 : bottom }"
                :default-page-size="20"
                @query="queryList"
                @keyboardHeightChange="keyboardHeightChange"
                @hidedKeyboard="hidedKeyboard"
            >
                <!-- 顶部提示文字 -->

                <!-- style="transform: scaleY(-1)"必须写，否则会导致列表倒置（必须写在for循环标签上，不得写在容器上）！！！ -->
                <!-- 注意不要直接在chat-item组件标签上设置style，因为在微信小程序中是无效的，请包一层view -->

                <template #top>
                    <slot name="top" />
                </template>
                <view class="scroll-view-content pb-[20rpx]" ref="contentRef">
                    <view
                        v-for="(item, index) in chatList"
                        :key="`${item.id} + ${index} + ''`"
                        style="transform: scaleY(-1)"
                    >
                        <view class="chat-record mt-[20rpx] pb-[40rpx]">
                            <chat-record-item
                                :record-id="item.id"
                                :type="item.type == 1 ? 'right' : 'left'"
                                :content="item.content"
                                :loading="item.loading"
                                :audio="item.voice_file"
                                :index="index"
                                :time="item.type == 2 ? item.create_time : ''"
                                :is-collect="item.is_collect"
                                :avatar="avatar"
                                :showRewriteBtn="index === 0"
                                :showPosterBtn="true"
                                :showCopyBtn="true"
                                @rewrite="rewrite(index)"
                                @update="(e: any) => chatList[e.index].is_collect = e.value"
                                @click-poster="handleDrawPoster"
                            ></chat-record-item>
                        </view>
                    </view>
                </view>
                <template #empty>
                    <slot name="empty" />
                </template>
                <template #bottom>
                    <view class="send-area">
                        <view class="float-btn">
                            <view
                                v-if="chatList.length && !isReceiving"
                                class="px-[20rpx] py-[10rpx] text-xs flex items-center"
                                @click="sendLock('继续')"
                            >
                                <u-icon
                                    name="play-circle"
                                    class="mr-[8rpx]"
                                    size="36"
                                />
                                继续
                            </view>
                            <view
                                v-if="isReceiving"
                                class="px-[20rpx] py-[10rpx] text-xs flex items-center"
                                @click="chatClose()"
                            >
                                <u-icon
                                    name="pause-circle"
                                    class="mr-[8rpx]"
                                    size="36"
                                />
                                停止
                            </view>
                        </view>
                        <view class="mb-[20rpx] flex items-center">
                            <view class="mr-auto">
                                <network-switch
                                    v-model="network"
                                    v-if="type == 1"
                                ></network-switch>
                            </view>
                            <view class="flex text-content items-center">
                                <!-- <view
                                    class="text-xs flex items-center mr-[20rpx]"
                                    @click="triggerVoiceShow"
                                    v-if="type == 1 && appStore.getIsVoiceChat"
                                >
                                    <u-icon
                                        name="volume"
                                        class="mr-[4rpx]"
                                        size="28"
                                    />
                                    在线语音
                                </view> -->
                                <view
                                    class="text-xs flex items-center"
                                    @click="cleanChatLock"
                                >
                                    <u-icon
                                        name="trash"
                                        class="mr-[4rpx]"
                                        size="28"
                                    />
                                    清空
                                </view>
                            </view>
                        </view>
                        <view
                            class="send-area__content bg-page"
                            :class="[
                                safeAreaInsetBottom
                                    ? 'safe-area-inset-bottom'
                                    : ''
                            ]"
                        >
                            <view class="flex-1 min-w-0 relative">
                                <view
                                    v-if="showPressBtn"
                                    class="absolute left-[-10rpx] top-[-15rpx] bottom-[-15rpx] bg-primary text-btn-text right-[0] z-[9999] flex items-center justify-center rounded-[12rpx]"
                                    @longpress="handleLongpress"
                                    @touchend="touchEnd"
                                    @touchcancel="touchEnd"
                                >
                                    按住说话
                                </view>
                                <u-input
                                    type="textarea"
                                    v-model="userInput"
                                    :placeholder="placeholder"
                                    maxlength="-1"
                                    :auto-height="true"
                                    confirm-type="send"
                                    :adjust-position="false"
                                    :fixed="false"
                                    adjust-keyboard-to="bottom"
                                    @click="handleClick"
                                    @focus="scrollToBottom"
                                />
                            </view>
                            <view class="ml-[20rpx] my-[-12rpx]">
                                <view v-if="userInput">
                                    <u-button
                                        type="primary"
                                        :custom-style="{
                                            width: '100rpx',
                                            height: '52rpx',
                                            margin: '0'
                                        }"
                                        size="mini"
                                        :disabled="isReceiving"
                                        @click.stop="sendLock()"
                                    >
                                        发送
                                    </u-button>
                                </view>
                                <view v-else-if="appStore.getIsVoiceTransfer">
                                    <view
                                        v-if="showPressBtn"
                                        class="text-content"
                                        @click="triggerRecordShow"
                                    >
                                        <u-icon name="more-circle" :size="52" />
                                    </view>
                                    <view
                                        v-else
                                        class="text-content"
                                        @click="triggerRecordShow"
                                    >
                                        <u-icon name="mic" :size="52" />
                                    </view>
                                </view>
                            </view>
                        </view>
                    </view>
                </template>
            </z-paging>
        </view>

        <guided-popup ref="guidedPopupRef" />
        <!--#ifdef APP-PLUS-->
        <appChat
            @onmessage="appOnmessage"
            @onclose="appOnclose"
            @onstart="appOnstart"
            ref="appChatRef"
        ></appChat>
        <!--#endif-->
        <recorder
            ref="recorderRef"
            v-model:show="showRecorder"
            @success="sendLock"
        />
    </view>
    <online-voice
        v-model:show="showOnlineVoice"
        :data="{
            model: currentModel,
            type: type,
            other_id: otherId,
            network: network
        }"
        @update="pagingRef?.reload()"
    />

    <!--  生产对话海报  -->
    <dialog-poster ref="posterRef"></dialog-poster>
    <view v-if="type == 1 && appStore.getIsVoiceChat">
        <dragon-button :size="184" :yEdge="160">
            <view class="p-[20rpx]" @click="triggerVoiceShow">
                <view class="flex justify-center mb-[-20rpx] relative z-[99]">
                    <image
                        class="w-[70rpx] h-[70rpx] mb-[10rpx]"
                        :src="loadingPath"
                    />
                </view>
                <view>
                    <u-button
                        hover-class="none"
                        :custom-style="{
                            width: '100%',
                            height: '58rpx',
                            fontSize: '24rpx',
                            background: '#28C840',
                            'box-shadow': '0 3px 10px #00000033'
                        }"
                        type="primary"
                        shape="circle"
                    >
                        在线语音
                    </u-button>
                </view>
            </view>
        </dragon-button>
    </view>
</template>
<script lang="ts">
export default {
    options: {
        virtualHost: true,
        styleIsolation: 'shared'
    }
}
</script>
<script lang="ts" setup>
import { chatSendText, cleanChatRecord, getChatRecord } from '@/api/chat'
import { useLockFn } from '@/hooks/useLockFn'
import { useUserStore } from '@/stores/user'

import { useRouter } from 'uniapp-router-next'
// import { useRouter } from 'uniapp-router-next-zm'
import { onUnmounted, watch, onMounted } from 'vue'

import { ref, shallowRef } from 'vue'
import { useSessionList } from '@/pages/index/components/useSessionList'
import { useAppStore } from '@/stores/app'
import { RequestErrMsgEnum } from '@/enums/requestEnums'
import { useAudioPlay } from '@/hooks/useAudioPlay'
import { CHAT_LIMIT_KEY } from '@/enums/constantEnums'
import { isNewDay } from '@/utils/validate'
import { useRecorder } from '@/hooks/useRecorder'
import OnlineVoice from './components/online-voice.vue'
import { onHide, onShow } from '@dcloudio/uni-app'
import { useAudio } from '@/hooks/useAudio'
import { vibrate } from '@/utils/device/vibrate'
import config from '@/config'
//#ifdef APP-PLUS
import appChat from './components/app-chat'

const appChatRef = shallowRef()
//#endif

const loadingUrl = 'resource/image/api/default/bubble.gif'
let loadingPath = `${config.baseUrl}${loadingUrl}`
//#ifdef H5
loadingPath = `${
    config.baseUrl === '/' ? `${location.origin}/` : config.baseUrl
}${loadingUrl}`
//#endif

const props = withDefaults(
    defineProps<{
        type: number
        otherId?: number
        tips?: string
        bottom?: string
        placeholder?: string
        currentModel?: string | number
        safeAreaInsetBottom: boolean
        showAdd?: boolean
        avatar?: string
    }>(),
    {
        tips: '',
        placeholder: '请输入内容',
        currentModel: '',
        safeAreaInsetBottom: false,
        showAdd: false,
        bottom: '0'
    }
)

const emit = defineEmits<{
    (event: 'update:modelValue', value: any[]): void
    (event: 'reader', value: any): void
}>()
const appStore = useAppStore()
const router = useRouter()
const userStore = useUserStore()
const contentRef = shallowRef()
const guidedPopupRef = shallowRef()
const network = ref(false)
const showPressBtn = ref(false)
const showRecorder = ref(false)
const showOnlineVoice = ref(false)

const { sessionActive, sessionAdd, currentSession, sessionEdit } =
    useSessionList()
const chatList = ref<any[]>([])

const userInput = ref('')
const newUserInput = ref('')
const { authorize } = useRecorder({})
const { pauseAll } = useAudio()
const handleClick = () => {
    if (!userStore.isLogin) {
        return toLogin()
    }
}

const pagingRef = shallowRef()

const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists = [], count } = await getChatRecord({
            type: props.type,
            other_id: props.type !== 1 ? props.otherId : undefined,
            category_id: props.type === 1 ? props.otherId : undefined,
            page_size: pageSize / 2,
            page_no: pageNo
        })

        pagingRef.value?.complete(lists.reverse())
        if (count === 0) {
            setTimeout(() => {
                pagingRef.value?.scrollToTop(false)
            }, 200)
        } else if (pageSize === 1) {
            setTimeout(() => {
                scrollToBottom()
            }, 100)
        }
    } catch (error) {
        pagingRef.value?.complete(false)
    }
}

const keyboardIsShow = ref(false)

const keyboardHeightChange = (res: any) => {
    if (res.height > 0) {
        keyboardIsShow.value = true
    } else {
        keyboardIsShow.value = false
    }
}

const hidedKeyboard = () => {
    keyboardIsShow.value = false
}

const handleLongpress = async () => {
    await recorderRef.value.startRecord()
    showRecorder.value = true
    vibrate(100)
}

watch(
    () => props.otherId,
    (value) => {
        setTimeout(() => {
            if (value) {
                pagingRef.value?.reload()
            } else {
                pagingRef.value?.complete([])
                setTimeout(() => {
                    pagingRef.value?.scrollToTop(false)
                }, 100)
            }
        }, 10)
    },
    {
        immediate: true
    }
)

const triggerRecordShow = async () => {
    //#ifdef APP-PLUS
    uni.$u.toast('相关功能正在开发中')
    return Promise.reject()
    //#endif
    if (showPressBtn.value) {
        showPressBtn.value = false
    } else {
        await getRecordAuth()
        pauseAll()
        showPressBtn.value = true
    }
}

const triggerVoiceShow = async () => {
    //#ifdef APP-PLUS
    uni.$u.toast('相关功能正在开发中')
    return Promise.reject()
    //#endif
    await getRecordAuth()
    pauseAll()
    showOnlineVoice.value = true
}

const getRecordAuth = async () => {
    if (!userStore.isLogin) {
        toLogin()
        return Promise.reject()
    }
    try {
        await authorize()
    } catch (error) {
        uni.$u.toast(error)
        return Promise.reject()
    }
}

const recorderRef = shallowRef()
const touchEnd = () => {
    recorderRef.value?.stopRecord()
}

// 海报实力
const posterRef = shallowRef()
const handleDrawPoster = async (recordId: number) => {
    const result = chatList.value.filter((item: any) => {
        return item.id == recordId
    })
    if (result.length != 2) {
        uni.$u.toast('上下文数据不对～')
        return
    }
    posterRef.value.initPosterData({
        title: result[1].content,
        content: result[0].content
    })
}

const { lockFn: rewrite } = useLockFn(async (index: number) => {
    console.log(index)
    if (isReceiving.value) return
    const last = chatList.value[index]
    const userInput = chatList.value.findLast(({ id }) => id === last.id)

    if (userInput) {
        await cleanChatRecord({
            type: props.type,
            id: last.id
        })
        // eslint-disable-next-line vue/no-mutating-props
        chatList.value.splice(index, 2)
        sendLock(userInput.content)
    }
})
const { lockFn: cleanChatLock } = useLockFn(async () => {
    if (!userStore.isLogin) return toLogin()

    const modal = await uni.showModal({
        title: '温馨提示',
        content: '确定清空对话？'
    })
    if (modal.cancel) return
    chatClose()
    await cleanChatRecord({
        type: props.type,
        other_id: props.type !== 1 ? props.otherId : undefined,
        category_id: props.type === 1 ? props.otherId : undefined
    })
    pagingRef.value?.reload()
})

const scrollToBottom = async () => {
    pagingRef.value?.scrollToBottom(false)
}

const isReceiving = ref(false)
let streamReader: any = null

const chatClose = () => {
    //#ifdef H5
    streamReader?.cancel()
    //#endif
    //#ifdef MP-WEIXIN
    streamReader?.abort()
    //#endif
    //#ifdef APP-PLUS
    appChatRef.value.stop()
    //#endif
    setTimeout(() => {
        userInput.value = ''
    })
}
const chatContent = ref<any>({})
const { pauseAll: pauseAllVoice } = useAudioPlay()
const { lockFn: sendLock } = useLockFn(async (text: string) => {
    showRecorder.value = false
    if (!userStore.isLogin) {
        return toLogin()
    }
    if (isReceiving.value) return
    if (userStore.userInfo.is_chat_limit && isNewDay(true, CHAT_LIMIT_KEY)) {
        const res = await uni.showModal({
            title: '对话上限提示',
            content: '已超过会员对话上限次数，继续对话将会消耗账户的对话余额',
            confirmText: '继续',
            cancelText: '关闭'
        })
        if (res.cancel) return
    }
    const inputValue = text || userInput.value
    if (!inputValue) return uni.$u.toast(props.placeholder)
    if (props.type == 1) {
        if (sessionActive.value === 0) {
            await sessionAdd()
        }
        if (currentSession.value === '新的会话') {
            await sessionEdit(sessionActive.value, inputValue)
        }
    }
    pagingRef.value.addChatRecordData({
        type: 1,
        content: inputValue
    })
    chatContent.value = {
        type: 2,
        loading: true,
        content: [] as string[],
        id: Date.now()
    }
    pagingRef.value.addChatRecordData(chatContent.value)
    newUserInput.value = userInput.value
    userInput.value = ''
    //#ifdef APP-PLUS
    isReceiving.value = true
    appChatRef.value.getParamsData({
        model: props.currentModel,
        question: inputValue,
        type: props.type,
        other_id: props.otherId,
        network: network.value
    })
    //#endif
    //#ifndef APP-PLUS
    try {
        isReceiving.value = true
        await chatSendText(
            {
                model: props.currentModel,
                question: inputValue,
                type: props.type,
                other_id: props.otherId,
                network: network.value
            },
            {
                onstart(reader) {
                    streamReader = reader
                    pauseAllVoice()
                    userInput.value = ''
                    emit('reader', reader)
                },
                onmessage(value) {
                    value
                        .trim()
                        .split('data:')
                        .forEach(async (text) => {
                            if (text !== '') {
                                try {
                                    const dataJson = JSON.parse(text)

                                    const { event, data, code, index } =
                                        dataJson

                                    if (event === 'error' && code === 101) {
                                        userInput.value = newUserInput.value
                                        guidedPopupRef.value?.open()
                                        return
                                    } else if (event === 'error') {
                                        uni.$u.toast(data)
                                        userInput.value = newUserInput.value
                                        return
                                    }

                                    if (data) {
                                        if (!chatContent.value.content[index]) {
                                            chatContent.value.content[index] =
                                                ''
                                        }
                                        chatContent.value.content[index] += data
                                    }

                                    if (event === 'finish') {
                                        chatContent.value.loading = false
                                        return
                                    }
                                } catch (error) {}
                            }
                        })
                },
                onclose() {
                    isReceiving.value = false
                    setTimeout(() => {
                        pagingRef.value?.reload()
                    }, 600)
                }
            }
        )
    } catch (error: any) {
        console.log('发送消息失败=>', error)
        if (error.errMsg !== RequestErrMsgEnum.ABORT) {
            chatList.value.splice(0, 2)
        }
        userInput.value = newUserInput.value
        isReceiving.value = false
    }
    //#endif
})

//#ifdef APP-PLUS
const appOnmessage = (value: any) => {
    value
        .trim()
        .split('data:')
        .forEach(async (text: any) => {
            if (text !== '') {
                try {
                    const dataJson = JSON.parse(text)

                    const { event, data, code, index } = dataJson

                    if (event === 'error' && code === 101) {
                        userInput.value = newUserInput.value
                        guidedPopupRef.value?.open()
                        return
                    } else if (event === 'error') {
                        uni.$u.toast(data)
                        userInput.value = newUserInput.value
                        return
                    }

                    if (data) {
                        if (!chatContent.value.content[index]) {
                            chatContent.value.content[index] = ''
                        }
                        chatContent.value.content[index] += data
                    }

                    if (event === 'finish') {
                        chatContent.value.loading = false
                        return
                    }
                } catch (error) {}
            }
        })
}

const appOnclose = (value: any) => {
    isReceiving.value = false
    setTimeout(() => {
        pagingRef.value?.reload()
    }, 600)
}
//#endif
const appOnstart = (value: any) => {
    // console.log(value)
    streamReader = value
    emit('reader', value)
}

const toLogin = () => {
    router.navigateTo({ path: '/pages/login/login' })
}

const setUserInput = (value = '') => {
    userInput.value = value
}

onUnmounted(() => {
    chatClose()
})

watch(
    () => appStore.config,
    () => {
        if (appStore.getIsVoiceTransfer || appStore.getIsVoiceChat) {
            setTimeout(() => {
                //#ifdef H5
                authorize()
                //#endif
            }, 100)
        }
    },
    {
        immediate: true
    }
)

watch(sessionActive, async (value) => {
    if (value) {
        chatClose()
    }
})

defineExpose({
    scrollToBottom,
    setUserInput,
    sendLock,
    rewrite
})
</script>

<style lang="scss" scoped>
.chat-scroll-view {
    .send-area {
        position: relative;
        padding: 20rpx 30rpx;
        background-color: #fff;
        .float-btn {
            position: absolute;
            left: 50%;
            top: -10rpx;
            transform: translate(-50%, -100%);
            z-index: 100;
            border: 1px solid;
            border-radius: 20rpx;
            @apply bg-white border-light;
        }
        &__content {
            border-radius: 16rpx;
            padding: 25rpx 20rpx;
            position: relative;
            display: flex;
            align-items: center;

            :deep() {
                .u-input__textarea {
                    --line-height: 40rpx;
                    --line-num: 4;
                    height: auto;
                    min-height: var(--line-height) !important;
                    max-height: calc(var(--line-height) * var(--line-num));
                    font-size: 28rpx;
                    box-sizing: border-box;
                    padding: 0;
                    line-height: var(--line-height);
                    .uni-textarea-textarea {
                        max-height: calc(var(--line-height) * var(--line-num));
                        overflow-y: auto !important;
                    }
                }
            }
            .send-btn {
                width: 100%;
                position: absolute;
                right: 0rpx;
                bottom: 10rpx;
                z-index: 99;
                padding: 0 20rpx;
            }
        }
    }
}
.chat-bubble {
    width: 70rpx;
    height: 70rpx;
}
</style>
