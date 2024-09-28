<template>
    <view
        class="flex items-center justify-between text-xs text-content pb-[20rpx]"
        v-if="chatModel.modelList.length"
    >
        <view
            class="inline-flex justify-center items-center rounded-[30px] h-[60rpx] px-[24rpx]"
            style="box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.1)"
            @click="chatModel.show = true"
        >
            <text class="text-[#415058] mr-[6px] flex">
                <text class="line-clamp-1">
                    {{ chatModel.current.alias }} /
                    <text
                        v-if="!chatModel?.current?.member_free"
                        class="flex-1 min-w-0 text-muted"
                    >
                        <template v-if="chatModel?.current?.balance">
                            <text>消耗</text>
                            <text class="text-primary">
                                {{ chatModel?.current?.balance }}
                            </text>
                            <text>条对话次数</text>
                        </template>
                        <template v-else> 免费 </template>
                    </text>
                    <text v-else class="text-muted"> 会员免费 </text>
                </text>
            </text>
            <u-icon name="arrow-down" size="24rpx"></u-icon>
        </view>
    </view>
    <u-popup
        v-model="chatModel.show"
        mode="bottom"
        border-radius="14"
        :safe-area-inset-bottom="true"
        height="70%"
        closeable
    >
        <view class="p-[20rpx] text-lg font-bold"> 选择模型 </view>
        <view class="pb-[120rpx]">
            <view
                class="flex justify-center items-center py-[10rpx] px-[24rpx] text-[#415058]"
                v-for="item in chatModel.modelList"
                :key="item.key"
                :class="{
                    '!text-primary': modelKey == item.key
                }"
                @click="handleChoiceModel(item)"
            >
                <view class="mr-[6px] flex-1 min-w-0">
                    {{ item.alias }} /
                    <text
                        class="text-muted"
                        :class="{
                            '!text-primary': modelKey == item.key
                        }"
                    >
                        <text v-if="!item.member_free">
                            <template v-if="item.balance">
                                <text>消耗</text>
                                <text class="text-primary">
                                    {{ item.balance }}
                                </text>
                                <text>条对话次数</text>
                            </template>
                            <template v-else> 免费 </template>
                        </text>
                        <text v-else> 会员免费 </text>
                    </text>
                </view>
                <view
                    class="ml-[10rpx]"
                    :class="{
                        'opacity-0': modelKey !== item.key
                    }"
                >
                    <u-icon name="checkmark" size="24rpx"></u-icon>
                </view>
            </view>
        </view>
    </u-popup>
</template>
<script setup lang="ts">
import { getChatModelApi } from '@/api/chat'
import { computed, reactive, watch } from 'vue'
const props = defineProps<{
    modelValue: any
}>()

const emit = defineEmits<{
    (event: 'update:modelValue', value: any): void
}>()

const modelKey = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})

// 聊天模型数据
const chatModel = reactive({
    show: false,
    current: {
        balance: 1,
        key: '',
        member_free: false,
        model: '',
        default: false
    } as any,
    modelList: [] as any[]
})

// 获取聊天模型数据
const getChatModelFunc = async () => {
    try {
        const data = await getChatModelApi()
        chatModel.modelList = data
        chatModel.current = data.find((item: any) => item.default) || data[0]
    } catch (error) {
        console.log('获取聊天模型数据错误=>', error)
    }
}
getChatModelFunc()

// 选择聊天模型
const handleChoiceModel = (item: any) => {
    chatModel.current = item
    chatModel.show = false
}

watch(
    () => chatModel.current,
    (value) => {
        modelKey.value = value?.key
    }
)
</script>
