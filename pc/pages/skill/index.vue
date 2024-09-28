<template>
    <div>
        <NuxtLayout name="default">
            <template #panel>
                <!-- <TheSession /> -->
                <ElScrollbar class="w-[240px] bg-white" ref="scrollbarRef">
                    <div class="p-[16px]">
                        <input
                            class="bg-[#f8f8f8] w-full leading-[32px] rounded-[3px] outline-none px-[10px]"
                            v-model="keyword"
                            placeholder="请输入关键词搜索"
                        />
                        <DropDown
                            v-for="(item, index) in sidebarList"
                            :title="item.name"
                            :length="item.skill.length"
                            :key="index"
                        >
                            <template #menu>
                                <div
                                    class="bg-[#F8F8F8] flex items-center p-[10px] mt-2 cursor-pointer"
                                    :class="{
                                        actived: selectval == item1.id,
                                        unactived: selectval != item1.id
                                    }"
                                    v-for="(item1, index1) in item.skill"
                                    :key="index1"
                                    @click="handleselected(item1)"
                                >
                                    <ElImage
                                        :src="item1.image"
                                        class="w-[34px] h-[34px] rounded-[8px]"
                                    ></ElImage>
                                    <div class="ml-2 flex-1">
                                        <div class="font-bold">
                                            {{ item1.name }}
                                        </div>
                                        <ClientOnly>
                                            <OverflowTooltip
                                                teleported
                                                class="text-tx-secondary"
                                                :content="item1.describe"
                                                :line="2"
                                            >
                                            </OverflowTooltip>
                                        </ClientOnly>
                                    </div>
                                </div>
                            </template>
                        </DropDown>
                    </div>
                </ElScrollbar>
            </template>
            <div class="h-full">
                <Chatting
                    v-model:model="chatParams.model"
                    class="h-full"
                    ref="chattingRef"
                    :title="chatTitle"
                    :selectval="selectval"
                    :avatar="currentIcon"
                    :chat-type="3"
                    :send-disabled="isReceiving"
                    :content-list="chatContentList"
                    @close="chatClose"
                    @content-post="contentPost"
                    @clean-chat-log="cleanChatLog"
                    @top="handleTop"
                >
                    <div
                        class="h-[40px] py-[10px] text-[#999] text-center cursor-pointer"
                        @click.stop="handleTop"
                    >
                        <template v-if="hasMoreDialogueRecords && userStore.isLogin">
                            {{
                                dialogueRecordsLoading
                                    ? '正在加载中...'
                                    : '-- 点击加载 --'
                            }}
                        </template>
                    </div>
                </Chatting>
            </div>
            <TheGuidedPopup ref="guidedPopupRef" />
        </NuxtLayout>
    </div>
</template>
<script setup lang="ts">
import { ElImage, ElInput, ElMessageBox, ElScrollbar } from 'element-plus'
import { getSkillList } from '@/api/skill'
import { getchatLog, getChat, cleanChat, chatSendText } from '@/api/chat'
import { useUserStore } from '~~/stores/user'
import feedback from '~~/utils/feedback'
import { useAppStore } from '~/stores/app'
import { watchDebounced } from '@vueuse/core'
import { isNewDay } from '~/utils/validate'
import { CHAT_LIMIT_KEY } from '~/enums/cacheEnums'
const router = useRouter()
const route = useRoute()
const appStore = useAppStore()
const userStore = useUserStore()
//当前选中的侧边栏
const selectval = ref(0)
//聊天框标题
const chatTitle = ref('')
const currentIcon = ref('')
//侧边栏列表
const sidebarList = ref([])

const currentId = ref(Number(route.query.id))
const chatParams = reactive({
    model: ''
})
//滚动条ref
const scrollbarRef = ref<InstanceType<typeof ElScrollbar>>()

//选择侧边栏列表
const handleselected = ({ id, title, image }: any) => {
    router.push({
        path: '/skill',
        query: {
            id
        }
    })
    // 切换对话重置分页数据 Start
    selectval.value = Number(id)
    chatLogParams.page_no = 1
    chatContentList.value = []
    contentListHeight = 0
    // 切换对话重置分页数据 End
    chatTitle.value = title
    currentIcon.value = image
    nextTick(async () => {
        await getChatList()
        chattingRef.value.scrollToBottom()
    })
}
const guidedPopupRef = shallowRef()
//对话记录列表
const chatContentList: any = ref([])
//对话框ref
const chattingRef = ref(null)
const keyword = ref()
//侧边栏列表数据
const getSidebarList = async () => {
    sidebarList.value = await getSkillList({
        keyword: keyword.value
    })
}

const chatLogParams = reactive({
    type: 3,
    page_type: 1,
    page_no: 1,
    page_size: 15
})
const hasMoreDialogueRecords = ref<boolean>(false)
const dialogueRecordsLoading = ref<boolean>(false)

/**
 * 1.上拉加载
 * 2.切换分类时重制上拉
 * 3.对话以后加载(最新一条就行)
 * flag = 1的时候表示对话结束了，获取最新一条数据
 **/
let contentListHeight = 0 // 旧记录高度，新记录高度 - 旧记录高度得到最新刷新定位位置
//获取聊天记录
const getChatList = async (flag?: boolean) => {
    dialogueRecordsLoading.value = true
    hasMoreDialogueRecords.value = true
    try {
        const chatElement = document.getElementsByClassName('contentList')[0]
        const data = await getchatLog({
            other_id: selectval.value,
            ...chatLogParams,
            page_no: flag ? 1 : chatLogParams.page_no,
            page_size: flag ? 1 : chatLogParams.page_size
        })

        const { page_no, page_size } = chatLogParams
        if (
            page_no * page_size > data.count ||
            chatContentList.value.length / 2 >= data.count
        ) {
            hasMoreDialogueRecords.value = false
        }

        const transformData = data?.lists.map((item: any) => {
            if (item.type == 1) {
                return { ...item, from_avatar: userStore.userInfo.avatar }
            } else {
                return { ...item, from_avatar: currentIcon.value }
            }
        })

        if (flag) {
            chatContentList.value.splice(-2, 2)
            chatContentList.value = [...chatContentList.value, ...transformData]
        } else {
            chatContentList.value = [...transformData, ...chatContentList.value]
        }

        // 刷新记录时将滚动条定位到刷新之前的元素那里
        await nextTick()
        const rect = Object.freeze(chatElement?.getBoundingClientRect())
        if (rect && contentListHeight) {
            chattingRef.value?.scrollTo(rect.height - contentListHeight)
        }
        contentListHeight = rect.height
    } catch (e) {
        console.log('聊天记录错误', e)
    } finally {
        dialogueRecordsLoading.value = false
    }
}

const handleTop = () => {
    if (hasMoreDialogueRecords.value && contentListHeight) {
        chatLogParams.page_no += 1
        getChatList()
    }
}

//发送问题
const { pauseAll } = useAudioPlay()
const isReceiving = ref(false)
const newUserInput = ref<string>('')
const contentPost = async (userInput: any) => {
    try {
        isReceiving.value = true
        newUserInput.value = userInput
        if (
            userStore.userInfo.is_chat_limit &&
            isNewDay(true, CHAT_LIMIT_KEY)
        ) {
            try {
                await ElMessageBox.confirm(
                    '已超过会员对话上限次数，继续对话将会消耗账户的对话余额',
                    '对话上限提示',
                    {
                        showClose: false,
                        confirmButtonText: '继续',
                        cancelButtonText: '关闭'
                    }
                )
            } catch (e) {
                return
            }
        }
        chatContentList.value.push({
            type: 1,
            content: userInput,
            from_avatar: userStore.userInfo.avatar
        })
        const result = reactive({
            type: 2,
            loading: true,
            content: [],
            create_time: ' ',
            from_avatar: currentIcon.value,
            id: ''
        })
        chatContentList.value.push(result)
        await chatSendText(
            {
                question: userInput,
                type: 3,
                other_id: selectval.value,
                ...chatParams
            },
            {
                onstart(reader) {
                    streamReader = reader
                    pauseAll()
                },
                onmessage(value) {
                    value
                        .trim()
                        .split('data:')
                        .forEach(async (text) => {
                            if (text !== '') {
                                try {
                                    const dataJson = JSON.parse(text)
                                    const {
                                        id: chatId,
                                        event,
                                        data,
                                        code,
                                        index
                                    } = dataJson

                                    if (event == 'error' && code === 101) {
                                        guidedPopupRef.value?.open()
                                    } else if (event == 'error') {
                                        feedback.msgError(data)
                                        chattingRef.value.setInput(
                                            newUserInput.value
                                        )
                                    }

                                    if (data) {
                                        if (!result.content[index]) {
                                            result.content[index] = ''
                                        }
                                        result.content[index] += data
                                    }

                                    if (event === 'finish') {
                                        result.loading = false
                                        return
                                    }
                                } catch (error) {}
                            }
                        })
                },
                onclose() {
                    isReceiving.value = false
                    setTimeout(async () => {
                        await getChatList(true)
                        await nextTick()
                        chattingRef.value.scrollToBottom()
                    }, 600)
                    userStore.getUser()
                }
            }
        )
    } catch (error) {
        isReceiving.value = false
        chattingRef.value.setInput(newUserInput.value)
        chatContentList.value.splice(chatContentList.value.length - 2, 2)
    }
    nextTick(() => {
        chattingRef.value.scrollToBottom()
    })
}

let streamReader: ReadableStreamDefaultReader<Uint8Array> | null = null
const chatClose = (index?: number) => {
    streamReader?.cancel()
    if (index) {
        chatContentList.value[index].loading = false
    }
    isReceiving.value = false
}

//清空会话
const cleanChatLog = async () => {
    await feedback.confirm('确定清空对话？')
    await cleanChat({ type: 3, other_id: selectval.value })
    chatContentList.value = []
}

//选择侧边栏列表第一个选项
const selectFirst = () => {
    if (!currentId.value) {
        handleselected(sidebarList.value[0].skill[0])
    } else {
        sidebarList.value.forEach((item, index) => {
            item.skill.forEach((item1, index1) => {
                if (item1.id == currentId.value) {
                    handleselected(sidebarList.value[index].skill[index1])
                }
            })
        })
    }
}

const scrollCategory = () => {
    const [activeCategory] = document.getElementsByClassName('actived')
    if (!activeCategory) return
    const activeCategoryRect = activeCategory.getBoundingClientRect()
    const categoryScrollbarRect =
        scrollbarRef.value?.wrapRef.getBoundingClientRect()
    const scrollTop =
        activeCategoryRect.top -
        (categoryScrollbarRect.height / 2 + categoryScrollbarRect.top)
    scrollbarRef.value.setScrollTop(scrollTop)
}

onMounted(async () => {
    await getSidebarList()
    await selectFirst()
    scrollCategory()
})

watchDebounced(
    keyword,
    (value) => {
        getSidebarList()
    },
    {
        debounce: 500
    }
)

definePageMeta({
    layout: false
})
</script>
<style lang="scss" scoped>
.actived {
    border-left: 3px solid var(--el-color-primary);
}
.unactived {
    border-left: 3px solid #f8f8f8;
}
</style>
