<template>
    <NuxtLayout name="default">
        <template #panel>
            <div
                class="bg-white flex flex-col h-full w-[200px] text-tx-primary"
            >
                <div class="pt-[16px] px-[16px]">
                    <el-page-header content="" @back="$router.back()" />
                    <div class="mt-[16px]">
                        <el-input
                            v-model="searchKeyword"
                            placeholder="请输入关键词搜索"
                        >
                        </el-input>
                    </div>
                    <div class="flex justify-end mt-[16px] mb-[10px]">
                        <ElButton link type="primary" @click="toggleCollapsed">
                            全部{{ isCollapseAllOpen ? '收起' : '展开' }}
                        </ElButton>
                    </div>
                </div>
                <div class="flex-1 min-h-0">
                    <ElScrollbar ref="categoryScrollbarRef">
                        <div class="creation-lists">
                            <el-collapse v-model="collapseOpen">
                                <el-collapse-item
                                    v-for="item in creationLists"
                                    :key="item.id"
                                    :title="item.name"
                                    :name="item.id"
                                >
                                    <div class="px-[16px]">
                                        <div
                                            v-for="model in item.model"
                                            :key="model.id"
                                            class="creation-item"
                                            :class="{
                                                'creation-item--active':
                                                    currentId == model.id
                                            }"
                                            @click="selectModel(model)"
                                        >
                                            <img
                                                class="w-[28px] h-28px"
                                                :src="model.image"
                                                alt=""
                                            />
                                            <div class="ml-[10px]">
                                                {{ model.name }}
                                            </div>
                                        </div>
                                    </div>
                                </el-collapse-item>
                            </el-collapse>
                        </div>
                    </ElScrollbar>
                </div>
            </div>
        </template>
        <div class="h-full sm:flex">
            <div class="h-full w-[400px] flex flex-col rounded-[6px] bg-white">
                <div class="flex p-[20px]">
                    <div class="text-lg font-medium line-clamp-1 mr-auto">
                        {{ modelData.name }}
                    </div>
                    <ElButton link type="primary" @click="insertExample"
                        >插入示例</ElButton
                    >
                </div>
                <div class="flex-1 min-h-0">
                    <ElScrollbar>
                        <div class="p-[20px]">
                            <FormDesigner
                                ref="formDesignerRef"
                                v-model="formData"
                                :form-lists="modelData.form"
                                size="large"
                            />
                        </div>
                    </ElScrollbar>
                </div>

                <div class="px-[20px] pb-[20px]">
                    <!--模型选择-->
                    <ModelPicker v-model="chatModel"></ModelPicker>
                    <!--立即生成-->
                    <div class="flex flex-col justify-center items-center">
                        <ElButton
                            class="create-btn"
                            @click="handleCreate"
                            :loading="isReceiving"
                        >
                            立即生成
                        </ElButton>
                    </div>
                </div>
            </div>
            <div
                class="flex-1 sm:ml-[16px] mt-[16px] sm:mt-0 min-w-0"
                v-loading="pageInfo.loading"
            >
                <ElScrollbar
                    ref="scrollbarRef"
                    v-if="
                        currentCreationHistory.length || pageInfo.lists.length
                    "
                    @scroll="onElScrollbar"
                >
                    <div
                        v-if="currentCreationHistory.length"
                        ref="currentCreationHistoryRef"
                    >
                        <div class="text-lg font-medium mb-[16px]">
                            本次创作结果
                        </div>
                        <template
                            v-for="(item, index) in currentCreationHistory"
                            :key="item.id"
                        >
                            <TheCreateRecordItem
                                v-for="(text, tindex) in item.reply"
                                :key="tindex"
                                class="mb-[16px]"
                                :content="text"
                                :typing="item.loading"
                                :time="item.create_time"
                                :title="item.title"
                                :show-rewrite="!!item.extra && !isReceiving"
                                :record-id="item.id"
                                :index="tindex"
                                @copy="copyContent"
                                @again="again(item)"
                                @rewrite="rewrite(item)"
                            />
                        </template>
                    </div>
                    <div
                        v-show="pageInfo.lists.length"
                        class="overflow-x-hidden"
                    >
                        <div class="flex items-center mb-[16px]">
                            <div class="text-lg font-medium mr-[10px]">
                                创作历史
                            </div>
                            <div
                                class="bg-[#f2f2f2] text-sm px-[15px] py-[2px] rounded-full cursor-pointer"
                                @click="cleanChatLog"
                            >
                                清空记录
                            </div>
                        </div>
                        <div class="items-stretch">
                            <template
                                v-for="(item, index) in pageInfo.lists"
                                :key="item.id"
                            >
                                <div
                                    v-for="(text, tindex) in item.reply"
                                    :key="tindex"
                                    class="mb-[16px]"
                                >
                                    <TheCreateRecordItem
                                        class="cursor-pointer h-full"
                                        :content="text"
                                        :time="item.create_time"
                                        :title="item.title"
                                        :overflow="true"
                                        :record-id="item.id"
                                        :show-voice="appStore.getIsVoiceOpen"
                                        :index="tindex"
                                        @click="
                                            handelRecordPreview(
                                                item.title,
                                                text
                                            )
                                        "
                                        :show-rewrite="!!item.extra"
                                        @copy="copyContent"
                                        @again="again(item)"
                                        @rewrite="rewrite(item)"
                                    />
                                </div>
                            </template>
                        </div>
                    </div>
                </ElScrollbar>
                <div
                    v-else
                    class="h-full flex flex-col items-center justify-center py-[100px]"
                >
                    <img
                        class="w-[120px] h-[120px]"
                        src="@/assets/images/create_record_null.png"
                        alt=""
                    />
                    <div class="my-[16px] font-medium">
                        AI创作结果会在显示这里，现在你只需要
                    </div>
                    <div class="text-tx-regular text-sm">
                        1. 在左侧填好必要的信息，填写越详细，结果越准确哦
                        <br />
                        2.点击立即生成按钮，静待AI妙笔生花，一般在10秒内搞定
                    </div>
                </div>
            </div>
        </div>
        <TheCreateRecordPreview
            ref="createRecordPreviewRef"
            :content="previewData.content"
            :title="previewData.title"
            @copy="copyContent"
        />
        <TheGuidedPopup ref="guidedPopupRef" />
    </NuxtLayout>
</template>

<script setup lang="ts">
import { useElementSize, watchDebounced } from '@vueuse/core'
import { cloneDeep } from 'lodash-es'
import { ElMessageBox, ElScrollbar } from 'element-plus'
import { chatSendText, cleanChat, getchatLog } from '~/api/chat'
import { getCreationDetail, getCreationLists } from '~/api/create'
import { useAppStore } from '~/stores/app'
import { useUserStore } from '~/stores/user'
import ModelPicker from '~/components/chatting/model-picker.vue'
import { isNewDay } from '~/utils/validate'
import { CHAT_LIMIT_KEY } from '~/enums/cacheEnums'
const userStore = useUserStore()
const searchKeyword = ref('')
const route = useRoute()
const formDesignerRef = shallowRef()
const isCollapseAllOpen = ref(true)
const collapseOpen = ref<any[]>([])
const currentId = ref(Number(route.query.id))
const formData = ref<any>({})
const createRecordPreviewRef = shallowRef()
const currentCreationHistoryRef = shallowRef()
const scrollbarRef = ref<InstanceType<typeof ElScrollbar>>(null)
const appStore = useAppStore()
const router = useRouter()
const guidedPopupRef = shallowRef()
const categoryScrollbarRef = shallowRef<InstanceType<typeof ElScrollbar>>()
const previewData = reactive({
    title: '',
    content: ''
})

// 聊天模型
const chatModel = ref<string>('')

const {
    data: creationLists,
    pending,
    refresh
} = await useAsyncData(
    () =>
        getCreationLists({
            keyword: searchKeyword.value
        }),
    {
        default() {
            return []
        }
    }
)
const { pauseAll } = useAudioPlay()
const defaultForm = [
    {
        name: 'WidgetTextarea',
        title: '多行文本',
        id: 'question',
        props: {
            title: '内容描述',
            placeholder: '',
            rows: 8,
            maxlength: 400,
            autosize: false,
            isRequired: false
        }
    }
]

const cleanChatLog = async () => {
    await feedback.confirm('确定清空创作记录？')
    await cleanChat({ type: 2, other_id: currentId.value })
    currentCreationHistory.value = []
    resetHistory()
}

const { data: modelData, refresh: modelApiRefresh } = await useAsyncData(
    () =>
        getCreationDetail({
            id: currentId.value
        }),
    {
        default() {
            return {}
        },
        transform(data) {
            if (!data.form) {
                data.form = defaultForm
            }
            return data
        }
    }
)
const pageInfo = reactive({
    pageNo: 0,
    count: 0,
    pageSize: 15,
    loading: true,
    more: true,
    lists: []
})

const creationHistoryRefresh = async () => {
    if (pageInfo.more || pageInfo.pageNo === 0) {
        pageInfo.pageNo += 1
    } else {
        return
    }
    pageInfo.loading = true
    try {
        const data = await getchatLog({
            other_id: currentId.value,
            page_size: pageInfo.pageSize,
            page_no: pageInfo.pageNo,
            type: 2
        })
        const { page_no, page_size, count } = data
        pageInfo.count = count
        if (page_no * page_size > count) {
            pageInfo.more = false
        }

        const transformData = data.lists.map((item: any) => {
            let title = ''
            if (isArray(item.ask)) {
                const ask = item?.ask[0] || {}
                title = `${ask.title}：${ask.value}`
            } else {
                title = item.ask
            }
            return {
                ...item,
                title
            }
        })
        if (pageInfo.pageNo === 1) {
            pageInfo.lists = []
        }

        pageInfo.lists.push(...transformData)
    } catch (e) {
        console.log('聊天记录错误', e)
    } finally {
        pageInfo.loading = false
    }
}

const resetHistory = () => {
    Object.assign(pageInfo, {
        pageNo: 0,
        count: 0,
        pageSize: 15
    })
    creationHistoryRefresh()
}

const onElScrollbar = ({ scrollTop }) => {
    if (pageInfo.loading) return
    const { clientHeight, scrollHeight } = scrollbarRef.value.wrapRef
    if (scrollTop + clientHeight >= scrollHeight - 100) {
        console.log('触底了')
        creationHistoryRefresh()
    }
}

const handelRecordPreview = (title: string, content: string) => {
    previewData.title = title
    previewData.content = content
    createRecordPreviewRef.value?.open()
}
const selectModel = (model: any) => {
    currentId.value = model.id
    router.push({
        path: '',
        query: {
            id: model.id
        }
    })
}
const again = (item: any) => {
    console.log(item)
    formData.value = cloneDeep(item.extra)
}
const { lockFn: rewrite } = useLockFn(async (item: any) => {
    if (item.extra) {
        formData.value = item.extra
        await nextTick()
        handleCreate()
    }
})
watchDebounced(
    searchKeyword,
    (value) => {
        refresh()
    },
    {
        debounce: 500
    }
)
const insertExample = () => {
    modelData.value?.form.forEach((item: any) => {
        if (item.props.placeholder && !item.props.defaultValue) {
            formData.value[item.id] = item.props.placeholder
        }
    })
}

const setFormDataDefault = () => {
    modelData.value?.form.forEach((item: any) => {
        if (item.props.defaultValue) {
            formData.value[item.id] = cloneDeep(item.props.defaultValue)
        } else {
            formData.value[item.id] = undefined
        }
    })
}
const toggleCollapsed = () => {
    if (isCollapseAllOpen.value) {
        isCollapseAllOpen.value = false
        collapseOpen.value = []
    } else {
        collapseOpen.value = creationLists.value.map((item: any) => item.id)
        isCollapseAllOpen.value = true
    }
}

const currentCreationHistory = ref<any[]>([])

const generateTitle = () => {
    if (formData.value.question) {
        return formData.value.question
    }
    const firstForm = modelData.value?.form[0] || {}
    return `${firstForm.props.title}：${formData.value[firstForm.id] || ''}`
}
const isReceiving = ref(false)
let streamReader: ReadableStreamDefaultReader<Uint8Array> | null = null
const chatClose = () => {
    streamReader?.cancel()
    isReceiving.value = false
}
const handleCreate = async () => {
    try {
        await formDesignerRef.value?.validate()
    } catch (error) {
        feedback.msgError('必填项不能为空')
        return
    }
    if (userStore.userInfo.is_chat_limit && isNewDay(true, CHAT_LIMIT_KEY)) {
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
    if (isReceiving.value) return
    try {
        isReceiving.value = true
        const chatId = Date.now()
        chatSendText(
            {
                model: chatModel.value,
                other_id: currentId.value,
                question: formData.value,
                type: 2
            },
            {
                onstart(reader) {
                    streamReader = reader
                    if (currentCreationHistory.value.length) {
                        currentCreationHistory.value = []
                        resetHistory()
                    }
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

                                    const { event, data, code, index } =
                                        dataJson

                                    if (event == 'error' && code === 101) {
                                        guidedPopupRef.value?.open()
                                    } else if (event == 'error') {
                                        feedback.msgError(data)
                                    }
                                    if (event == 'chat') {
                                        let chatIndex =
                                            currentCreationHistory.value.findIndex(
                                                (item) => item.id === chatId
                                            )
                                        if (chatIndex === -1) {
                                            currentCreationHistory.value.push({
                                                loading: true,
                                                create_time: timeFormat(
                                                    new Date(),
                                                    'yyyy-mm-dd hh:MM:ss'
                                                ),
                                                title: formData.value.question
                                                    ? formData.value.question
                                                    : generateTitle(),
                                                reply: [],
                                                extra: cloneDeep(
                                                    formData.value
                                                ),
                                                id: chatId
                                            })
                                            chatIndex =
                                                currentCreationHistory.value
                                                    .length - 1
                                        }

                                        if (data) {
                                            if (
                                                !currentCreationHistory.value[
                                                    chatIndex
                                                ].reply[index]
                                            ) {
                                                currentCreationHistory.value[
                                                    chatIndex
                                                ].reply[index] = ''
                                            }
                                            currentCreationHistory.value[
                                                chatIndex
                                            ].reply[index] += data
                                        }
                                    }

                                    if (event === 'finish') {
                                        const chatIndex =
                                            currentCreationHistory.value.findIndex(
                                                (item) => item.id === chatId
                                            )
                                        currentCreationHistory.value[
                                            chatIndex
                                        ].loading = false
                                        currentCreationHistory.value[
                                            chatIndex
                                        ].reply[index] += data
                                        setFormDataDefault()
                                        return
                                    }
                                } catch (error) {
                                    isReceiving.value = false
                                }
                            }
                        })
                },
                onclose() {
                    userStore.getUser()
                    isReceiving.value = false
                    if (pageInfo.lists.length <= pageInfo.pageSize) {
                        resetHistory()
                    }
                }
            }
        )
        // setFormDataDefault()
    } catch (error) {
        isReceiving.value = false
    }
}
const { copy } = useCopy()
const copyContent = async (content) => {
    await copy(content)
}
watch(
    creationLists,
    (value) => {
        isCollapseAllOpen.value = false
        toggleCollapsed()
    },
    {
        immediate: true
    }
)
const { height } = useElementSize(currentCreationHistoryRef)
watch(height, (value) => {
    if (isReceiving.value) {
        const scrollContainerHeight = scrollbarRef.value?.wrapRef?.offsetHeight
        const scrollTop = value - scrollContainerHeight
        if (scrollTop > 0) {
            scrollbarRef.value?.setScrollTop(scrollTop)
        }
    }
})
watch(currentId, () => {
    chatClose()
    modelApiRefresh()
    resetHistory()
    currentCreationHistory.value = []
    pageInfo.lists = []
})
const scrollCategory = () => {
    const [activeCategory] = document.getElementsByClassName(
        'creation-item--active'
    )
    if (!activeCategory) return
    const activeCategoryRect = activeCategory.getBoundingClientRect()
    const categoryScrollbarRect =
        categoryScrollbarRef.value?.wrapRef.getBoundingClientRect()
    const scrollTop =
        activeCategoryRect.top -
        (categoryScrollbarRect.height / 2 + categoryScrollbarRect.top)
    categoryScrollbarRef.value.setScrollTop(scrollTop)
}

onMounted(async () => {
    await nextTick()
    scrollCategory()
    creationHistoryRefresh()
})

definePageMeta({
    layout: false,
    activePath: '/create',
    auth: true
})
</script>

<style lang="scss" scoped>
.creation-lists {
    .el-collapse {
        --el-collapse-border-color: transparent;
        :deep() {
            .el-collapse-item__header {
                padding: 8px 12px;
            }
            .el-collapse-item__content {
                padding: 2px 0;
            }
        }
    }

    .creation-item {
        box-shadow: 0px 0px 6px 0px rgba(220, 223, 230, 60);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        padding: 10px;
        border-radius: 4px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        &--active {
            &::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                height: 100%;
                width: 3px;
                background-color: var(--el-color-primary);
            }
        }
    }
}
.create-btn {
    border-radius: 100px;
    background: linear-gradient(
        90deg,
        var(--gradient-1) 0%,
        var(--gradient-2) 100%
    );
    width: 100%;
    height: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
    border: none;
    cursor: pointer;
    @apply text-lg text-btn-text;
}
</style>
