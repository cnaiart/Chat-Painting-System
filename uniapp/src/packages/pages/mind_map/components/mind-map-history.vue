<template>
    <z-paging
        ref="paging"
        class="bg-page"
        v-model="recordLists"
        @query="queryList"
        :fixed="false"
    >
        <view class="p-[20rpx] pb-0">
            <view class="flex justify-between">
                <view>
                    <!-- <text class="text-muted" @click="emit('history')">
                        <u-icon name="arrow-left" />
                        返回
                    </text> -->
                    <text class="text-lg font-medium ml-[20rpx]">
                        共{{ totalCount }}条记录
                    </text>
                </view>
                <view @click="cleanLogLock()">清空记录</view>
            </view>
            <view class="mt-[20rpx]" v-for="item in recordLists" :key="item.id">
                <view class="p-[30rpx] bg-white rounded-[20rpx]">
                    <view class="text-xl font-medium line-clamp-1">
                        帮我生成：{{ item.ask }}
                    </view>
                    <view
                        v-for="(text, index) in item.reply"
                        :key="index"
                        @click="emit('view', text)"
                    >
                        <view
                            class="whitespace-pre-line line-clamp-5 my-[20rpx]"
                        >
                            {{ text }}
                        </view>
                        <view class="flex items-center">
                            <view class="mr-auto text-muted text-sm">
                                {{ item.create_time }}
                            </view>
                            <view
                                class="flex bg-page px-[26rpx] py-[10rpx] rounded-full"
                                @click.stop="emit('view', text)"
                            >
                                <u-icon name="eye" />
                                <span class="text-xs ml-[10rpx]"> 查看</span>
                            </view>
                            <view
                                class="flex bg-page px-[26rpx] py-[10rpx] rounded-full ml-[20rpx]"
                                @click.stop="cleanLogLock(item.id)"
                            >
                                <u-icon name="trash" />
                                <span class="text-xs ml-[10rpx]"> 删除</span>
                            </view>
                        </view>
                    </view>
                </view>
            </view>
        </view>
        <view class="back-btn">
            <u-button
                type="primary"
                :custom-style="{
                    width: '100%',
                    height: '82rpx',
                    fontSize: '30rpx',
                    margin: '0'
                }"
                shape="circle"
                @click.stop="emit('history')"
            >
                返回思维导图
            </u-button>
        </view>
    </z-paging>
</template>

<script lang="ts" setup>
import { ref, shallowRef } from 'vue'
import { cleanChatRecord, getChatRecord } from '@/api/chat'
import { useLockFn } from '@/hooks/useLockFn'
const emit = defineEmits<{
    (event: 'history'): void
    (event: 'view', value: string): void
}>()

const paging = shallowRef()

const recordLists = ref<any[]>([])
const totalCount = ref('')
const queryList = async (pageNo: number, pageSize: number) => {
    const { lists = [], count } = await getChatRecord({
        type: 4,
        page_no: pageNo,
        page_size: pageSize
    })

    totalCount.value = count
    recordLists.value = lists
    paging.value.complete(lists)
}

const { lockFn: cleanLogLock } = useLockFn(async (id?: number) => {
    if (!recordLists.value.length) return
    const modal = await uni.showModal({
        title: '温馨提示',
        content: `确定${id ? '删除' : '清空'}记录？`
    })
    if (modal.cancel) return

    await cleanChatRecord({
        type: 4,
        id
    })
    paging.value?.reload()
})
</script>

<style scoped>
.back-btn {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    width: 260rpx;
    z-index: 99;
}
</style>
