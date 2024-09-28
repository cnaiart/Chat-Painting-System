<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <z-paging
        ref="pagingRef"
        :auto="false"
        v-model="creationHistory"
        auto-show-back-to-top
        @query="queryList"
    >
        <template #top>
            <view class="flex items-center p-[20rpx]" v-show="modelData.name">
                <u-image
                    :src="modelData.image"
                    width="42"
                    height="42"
                ></u-image>
                <view
                    class="flex-1 min-w-0 text-xl font-medium line-clamp-1 mx-[20rpx]"
                >
                    {{ modelData.name }}
                </view>
                <view
                    class="text-sm"
                    v-if="creationHistory.length"
                    @click="handelDeleteHistory"
                >
                    清空记录
                </view>
            </view>
        </template>
        <view class="px-[20rpx]">
            <view v-for="item in creationHistory" :key="item.id">
                <view
                    class="mt-[20rpx]"
                    v-for="(text, index) in item.reply"
                    :key="index"
                >
                    <creation-history-item
                        :title="item.title"
                        :time="item.create_time"
                        :content="text"
                        :index="index"
                        :overflow="true"
                        :record-id="item.id"
                        :show-rewrite="item.extra"
                        :showVoice="appStore.getIsVoiceOpen"
                        @copy="copy"
                        @delete="handelDeleteHistory"
                        @click-title="handleShowPreview($event, item.title)"
                        @rewrite="rewrite(item)"
                    />
                </view>
            </view>
        </view>
        <template #empty>
            <view class="flex flex-col justify-center items-center pb-[300rpx]">
                <image
                    class="w-[300rpx] h-[300rpx]"
                    src="@/packages/static/empty/create_record.png"
                    alt=""
                />
                <view class="my-[32rpx] text-muted"> 暂无数据～ </view>
            </view>
        </template>
    </z-paging>
    <u-popup v-model="showPreview" mode="bottom" border-radius="14" closeable>
        <view class="h-[85vh] flex flex-col">
            <view
                class="p-[30rpx] pr-[60rpx] text-xl font-medium border-0 border-b border-light border-solid"
            >
                <view class="line-clamp-1"> 标题详情 </view>
            </view>
            <view class="flex-1 min-h-0">
                <scroll-view class="h-full" scroll-y>
                    <view class="p-[30rpx]">
                        <view
                            class="flex-1 min-w-0 text-lg whitespace-pre-wrap"
                        >
                            {{ previewContent.title }}
                        </view>
                    </view>
                </scroll-view>
            </view>
        </view>
    </u-popup>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>

<script setup lang="ts">
import { getCreationDetail, getChatRecord, cleanChatRecord } from '@/api/chat'
import { onLoad } from '@dcloudio/uni-app'
import { reactive, ref } from 'vue'
import { shallowRef } from 'vue'
import { useCopy } from '@/hooks/useCopy'
import { isArray, isString } from 'lodash-es'
import { useAppStore } from '@/stores/app'
import TextItem from '@/components/chat-record-item/text-item.vue'
import CreationHistoryItem from '@/packages/components/creation-history-item/creation-history-item.vue'
import { useRouter } from 'uniapp-router-next'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
// import { useRouter } from 'uniapp-router-next-zm'

const { copy } = useCopy()
const appStore = useAppStore()
const router = useRouter()
const pageOptions = reactive({
    id: 0,
    type: 2
})
const modelData = ref<any>({})
const getData = async () => {
    const data = await getCreationDetail({
        id: pageOptions.id
    })
    modelData.value = data
}
const showPreview = ref(false)
const previewContent = reactive({
    title: '',
    text: ''
})
const handleShowPreview = (text: string, title: string) => {
    previewContent.text = text
    previewContent.title = title
    showPreview.value = true
}
const creationHistory = ref<any[]>([])

const pagingRef = shallowRef()
const queryList = async (pageNo: number, pageSize: number) => {
    // 此处请求仅为演示，请替换为自己项目中的请求
    const data = await getChatRecord({
        other_id: pageOptions.id,
        type: pageOptions.type,
        page_no: pageNo,
        page_size: pageSize
    })
    const lists = data.lists.map((item: any) => {
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
    pagingRef.value?.complete(lists)
}

const handelDeleteHistory = async () => {
    const showModal = await uni.showModal({
        title: '温馨提示',
        content: '删除的记录无法恢复，确定？'
    })
    if (showModal.cancel) return
    await cleanChatRecord({
        type: pageOptions.type,
        other_id: pageOptions.id
    })
    pagingRef.value?.reload()
}
const rewrite = async (item: any) => {
    await router.navigateBack()
    uni.$emit('createRewrite', item.extra)
}
onLoad(async (options) => {
    pageOptions.id = Number(options?.id)
    await getData()
    pagingRef.value?.reload(true)
})
</script>

<style lang="scss"></style>
