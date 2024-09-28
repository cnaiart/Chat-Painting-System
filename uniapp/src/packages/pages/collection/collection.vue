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
        ref="paging"
        v-model="collectData"
        @query="queryList"
        :fixed="false"
        use-page-scroll
    >
        <view
            class="m-[30rpx] bg-white"
            v-for="(item, index) in collectData"
            :key="item.id"
        >
            <view class="p-[30rpx]">
                <view class="flex">
                    <view
                        class="w-[40rpx] h-[40rpx] bg-[#4073FA] text-white text-center leading-[40rpx] flex-shrink-0"
                    >
                        问
                    </view>
                    <view class="ml-[20rpx]">
                        <view v-if="item.voice_input">
                            <audio-play
                                :url="item.voice_input"
                                :bg-color="$theme.primaryColor"
                            />
                        </view>
                        <text>
                            {{ item?.ask }}
                        </text>
                    </view>
                </view>
                <view
                    class="flex mt-[30rpx]"
                    v-for="(text, tindex) in item?.reply"
                    :key="tindex"
                >
                    <view
                        class="w-[40rpx] h-[40rpx] bg-[#FBBC2D] text-center leading-[40rpx] flex-shrink-0"
                    >
                        答
                    </view>
                    <view class="flex-1 ml-[20rpx] min-w-0">
                        <view v-if="item.voice_output">
                            <audio-play
                                :bg-color="$theme.primaryColor"
                                :url="item.voice_output"
                            />
                        </view>
                        <view>
                            <u-read-more
                                toggle
                                :text-indent="0"
                                :shadow-style="{
                                    'z-index': '9999',
                                    backgroundImage:
                                        'linear-gradient(-180deg, rgba(255, 255, 255, 0) 0%, #fff 80%)',
                                    paddingTop: '300rpx',
                                    marginTop: '-300rpx'
                                }"
                            >
                                <!-- @vue-ignore -->
                                <TextItem :content="text" is-markdown="true" />
                            </u-read-more>
                            <view class="flex">
                                <view
                                    class="text-sm text-muted"
                                    @click="copy(text)"
                                >
                                    复制
                                </view>
                            </view>
                        </view>
                    </view>
                </view>
                <view
                    class="flex justify-end items-center text-sm text-muted"
                    @click="handleCollect(index)"
                >
                    <u-icon name="star-fill" color="#FBBC2D" size="36" />
                    <span class="ml-[10rpx]">取消收藏</span>
                </view>
            </view>
        </view>
    </z-paging>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>

<script lang="ts" setup>
import { ref, reactive, shallowRef } from 'vue'
import { getCollectChatRecordLists, cancelCollectChatRecord } from '@/api/chat'
import { useCopy } from '@/hooks/useCopy'
import TextItem from '@/components/chat-record-item/text-item.vue'
import { onPageScroll, onReachBottom } from '@dcloudio/uni-app'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
const paging = shallowRef()

const { copy } = useCopy()
const collectData = ref<any[]>([])

const queryList = async (pageNo: number, pageSize: number) => {
    const { lists } = await getCollectChatRecordLists({
        page_no: pageNo,
        page_size: pageSize
    })
    lists.forEach((item: any) => {
        item.show = false
    })
    collectData.value = lists
    paging.value.complete(lists)
}

const handleCollect = async (index: number): Promise<void> => {
    const id = collectData.value[index].id
    try {
        await cancelCollectChatRecord({ collect_id: id })
        paging.value.reload()
    } catch (err) {
        console.log('取消收藏报错=>', err)
    }
}

onPageScroll(({ scrollTop }) => {
    paging.value.updatePageScrollTop(scrollTop)
})

onReachBottom(() => {
    paging.value.pageReachBottom()
})
</script>

<style scoped></style>
