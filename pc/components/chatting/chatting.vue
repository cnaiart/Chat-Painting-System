<template>
    <div
        class="chatting flex flex-col flex-1 bg-white py-[20px] w-full min-w-0 rounded-[20px]"
    >
        <!--对话框-->
        <div class="flex-1 flex flex-col min-h-0">
            <div v-if="title" class="text-center">{{ title }}</div>
            <el-scrollbar ref="scrollbarRef" height="100%" @scroll="scroll">
                <slot></slot>
                <div ref="innerRef" class="contentList">
                    <div
                        class="px-[40px] pb-[40px]"
                        v-if="!contentList.length && modelInfo.tips"
                    >
                        <div class="message-contain message--his">
                            <ChatMsgItem
                                v-if="chatType != 0"
                                :type="2"
                                :is-collet="0"
                                :avatar="avatar"
                                :show-collect-btn="false"
                                :show-copy-btn="false"
                                :show-poster="false"
                            >
                                <template #rob>
                                    <chat-content :content="modelInfo.tips" />
                                </template>
                            </ChatMsgItem>
                        </div>
                    </div>
                    <div
                        v-for="(item, index) in contentList"
                        :key="index"
                        class="px-[40px] pb-[40px]"
                    >
                        <!-- <div
                            class="text-center text-info"
                            v-if="timeFormat(item, index)"
                        ></div> -->
                        <!-- rob message -->
                        <div
                            class="message-contain message--his"
                            v-if="item.type === 2"
                        >
                            <ChatMsgItem
                                :avatar="item.from_avatar"
                                :type="2"
                                :loading="item.loading"
                                :stopping="!!item.content"
                                :show-rewrite-btn="
                                    index === contentList.length - 1
                                "
                                :time="item.create_time"
                                :is-collet="item.is_collect"
                                @collect="collect(item.id, item)"
                                @copy-content="copyContent(item.content)"
                                @close="emit('close', index)"
                                @poster="handleDrawPoster(item.id)"
                                @rewrite="rewrite"
                            >
                                <template #rob>
                                    <template
                                        v-if="
                                            item.loading && !item.content.length
                                        "
                                    >
                                        <div class="text-primary">
                                            等待回复中...
                                        </div>
                                    </template>
                                    <div
                                        v-for="(text, index) in item.content"
                                        :key="index"
                                        class="mb-[15px] last-of-type:mb-0"
                                        :class="{
                                            'pt-[15px] border-t border-solid border-br-light':
                                                index > 0
                                        }"
                                    >
                                        <chat-content
                                            :loading="item.loading"
                                            :content="text"
                                            :use-markdown="
                                                !!appStore.config?.chat
                                                    ?.is_markdown
                                            "
                                            :index="index"
                                            :record-id="item.id"
                                            :show-copy-btn="!item.loading"
                                            :show-voice-btn="
                                                !item.loading &&
                                                appStore.getIsVoiceOpen
                                            "
                                            @copy="copyContent(text)"
                                        />
                                    </div>
                                </template>
                            </ChatMsgItem>
                        </div>
                        <!--my msg-->
                        <div>
                            <div
                                class="message-contain message--my"
                                v-if="item.type === 1"
                            >
                                <ChatMsgItem
                                    :avatar="item.from_avatar"
                                    :type="1"
                                    :show-poster="false"
                                    @copy-my-content="copyContent(item.content)"
                                >
                                    <template #my>
                                        <chat-content :content="item.content" />
                                    </template>
                                </ChatMsgItem>
                            </div>
                        </div>
                    </div>
                </div>
            </el-scrollbar>
        </div>
        <div class="lg:px-[50px] mx-[10px]">
            <div
                v-if="contentList.length"
                class="flex-1 flex justify-center items-center"
            >
                <ElButton
                    v-if="!sendDisabled"
                    color="#f6f6f6"
                    :round="true"
                    style="--el-button-disabled-text-color: #999"
                    @click="emit('contentPost', '继续', chatModel)"
                >
                    <Icon name="el-icon-VideoPlay" :size="18" />
                    <span class="ml-[5px]">继续</span>
                </ElButton>
                <ElButton
                    v-else
                    color="#f6f6f6"
                    :round="true"
                    style="--el-button-disabled-text-color: #999"
                    @click="emit('close')"
                >
                    <Icon name="el-icon-VideoPause" :size="18" />
                    <span class="ml-[5px]">停止</span>
                </ElButton>
            </div>
            <div class="flex items-center">
                <!--模型选择-->
                <ModelPicker v-model="chatModel"></ModelPicker>
                <NetworkSwitch
                    v-model="chatNetwork"
                    class="m-[10px]"
                    v-if="chatType == 1"
                ></NetworkSwitch>
            </div>

            <!--输入框-->
            <div class="flex items-end">
                <div
                    class="flex h-full items-end flex-1 rounded-sm overflow-hidden"
                >
                    <ElInput
                        v-model="inputContent"
                        :input-style="{
                            backgroundColor: 'var(--el-bg-color-page)'
                        }"
                        class="min-h-full py-[4px]"
                        placeholder="请输入内容（Shift + Enter）= 换行"
                        type="textarea"
                        :autosize="{
                            maxRows: 4
                        }"
                        resize="none"
                        @compositionstart="isInputChinese = true"
                        @compositionend="isInputChinese = false"
                        @keydown="handleInputEnter"
                        @click="handleClick"
                    />
                </div>
                <ElButton
                    class="ml-2"
                    type="primary"
                    @click="contentPost"
                    size="large"
                    :disabled="sendDisabled"
                >
                    <template #default> 发送 </template>
                </ElButton>
                <ElButton
                    type="primary"
                    link
                    class="ml-4"
                    @click="cleanChatLog"
                >
                    <template #default> 清空对话 </template>
                </ElButton>
            </div>
        </div>
        <DialogPoster ref="posterRef" />
    </div>
</template>
<script setup lang="ts">
import { ElInput, ElButton, ElScrollbar } from 'element-plus'
import {
    cleanChat,
    collectRecords,
    getCreationDetail,
    getSkillDetail
} from '@/api/chat'
import { useUserStore } from '~~/stores/user'
import feedback from '~~/utils/feedback'
import { useElementSize, useVModel } from '@vueuse/core'
import { cancelCollect } from '~~/api/my'
import { useAppStore } from '~/stores/app'
import DialogPoster from './dialog-poster.vue'
import ModelPicker from './model-picker.vue'

const appStore = useAppStore()
const userStore = useUserStore()
const { isLogin, toggleShowLogin } = userStore
const posterRef = shallowRef()
const emit = defineEmits([
    'loading',
    'contentPost',
    'cleanChatLog',
    'close',
    'update:model',
    'update:network',
    'top'
])
const props = defineProps({
    contentList: {
        type: Array as any,
        default: () => []
    },
    selectval: {
        type: [String, Number],
        default: ''
    },
    chatType: {
        type: Number,
        default: 0
    },
    title: {
        type: String,
        default: ''
    },
    sendDisabled: {
        type: Boolean
    },
    avatar: {
        type: String
    },
    model: {
        type: String
    },
    network: {
        type: Boolean
    }
})

//输入框输入内容
const inputContent = ref('')
//对话框ref
const innerRef = ref<HTMLDivElement>(null)

//滚动条ref
const scrollbarRef = ref<InstanceType<typeof ElScrollbar>>(null)

// 聊天模型
const chatModel = useVModel(props, 'model', emit)
const chatNetwork = useVModel(props, 'network', emit)

//对话框滚动
const scroll = (value) => {
    refresh(value)
}

const modelInfo = ref<any>({})
const getChatModel = async () => {
    switch (props.chatType) {
        case 2:
            modelInfo.value = await getCreationDetail({
                id: props.selectval
            })
            break
        case 3:
            modelInfo.value = await getSkillDetail({
                id: props.selectval
            })
    }
}

watch(
    () => props.selectval,
    (val) => {
        if (props.chatType != 0) {
            getChatModel()
        }
    },
    {
        immediate: false
    }
)

const handleClick = () => {
    if (!isLogin) {
        toggleShowLogin()
    }
}

//点击回车键
const isInputChinese = ref(false)
const handleInputEnter = (e: any) => {
    if (e.shiftKey && e.keyCode === 13) {
        return
    }
    if (isInputChinese.value) return
    if (e.keyCode === 13) {
        contentPost()
        return e.preventDefault()
    }
}

//发送
const contentPost = () => {
    if (inputContent.value.replace(/(^\s*)|(\s*$)/g, '') == '') {
        feedback.msgError('输入为空！')
        return
    }

    if (props.sendDisabled) return
    if (!isLogin) {
        toggleShowLogin()
    }
    emit('contentPost', inputContent.value, chatModel.value)
    nextTick(() => {
        scrollToBottom()
    })
    cleanInput()
}

//清空输入框
const cleanInput = () => {
    inputContent.value = ''
}

// 设置输入
const setInput = (val: string) => {
    inputContent.value = val
}

//清空会话
const { lockFn: rewrite } = useLockFn(async () => {
    const last = props.contentList[props.contentList.length - 1]
    const userInput = props.contentList.find(({ id }) => id === last.id)

    if (userInput) {
        await cleanChat({
            type: props.chatType,
            id: last.id
        })
        // eslint-disable-next-line vue/no-mutating-props
        props.contentList.splice(props.contentList.length - 2, 2)
        emit('contentPost', userInput.content, chatModel.value)
    }
})
const cleanChatLog = () => {
    emit('cleanChatLog')
}

//收藏会话
const collect = async (id: number, item: any) => {
    if (item.is_collect) {
        await cancelCollect({
            collect_id: item.is_collect
        })
        item.is_collect = 0
    } else {
        await collectRecords({ records_id: id })
        item.is_collect = 1
    }
}

//复制文本
const { copy } = useCopy()
const copyContent = async (content) => {
    await copy(content)
}

//计算滚动到底部高度
const toScrollHeight = () => {
    return (
        scrollbarRef.value.wrapRef.scrollHeight -
        scrollbarRef.value.wrapRef.clientHeight
    )
}

//滚动至顶部加载
const refresh = ({ scrollTop }) => {
    if (scrollTop == 0) {
        console.log('刷新！')
        emit('top')
    }
}
const handleDrawPoster = async (recordId: number) => {
    const result = props.contentList.filter((item: any) => {
        return item.id == recordId
    })
    if (result.length != 2) {
        feedback.msgError('上下文数据不对～')
        return
    }
    posterRef.value.open({
        title: result[0].content,
        content: result[1].content
    })
}

//滚动到底部
const scrollToBottom = () => {
    const scrollH = toScrollHeight()
    scrollbarRef.value!.setScrollTop(scrollH)
}

// 滚动到指定位置
const scrollTo = (top: number) => {
    scrollbarRef.value!.setScrollTop(top)
}

const { height } = useElementSize(innerRef)
watch(height, (value) => {
    if (props.sendDisabled) {
        scrollToBottom()
    }
})

defineExpose({ scrollToBottom, scrollTo, cleanInput, setInput })
</script>
<style lang="scss" scoped>
.chatting {
    background: radial-gradient(
        farthest-side at 100% 0,
        var(--el-color-primary-light-9) 0%,
        #fff 40%
    );
}
.message-contain {
    padding: 10px 0;
}

.message--my {
    display: flex;
    justify-content: flex-end;
}

.message--his {
    display: flex;
    justify-content: flex-start;
}
:deep(.el-textarea__inner) {
    box-shadow: none;
    background-color: var(--el-bg-color-page);
}
</style>
