<template>
    <view
        v-if="userStore.isLogin && !chatModel.loading"
        class="px-[20rpx] py-[15rpx] text-sm flex items-center bg-white"
    >
        <view class="flex-none mr-[20rpx]">
            <view class="flex items-center">
                <u-button
                    type="primary"
                    plain
                    size="medium"
                    :custom-style="{
                        background: 'transparent !important'
                    }"
                    v-if="appStore.getIsShowVip"
                >
                    <router-navigate
                        class="text-primary"
                        to="/packages/pages/open_vip/open_vip"
                    >
                        {{
                            userInfo.is_member && userInfo.member_expired !== 1
                                ? userInfo.member_package_name
                                : '开通会员'
                        }}
                    </router-navigate>
                </u-button>
                <view class="ml-[20rpx]" v-if="!chatModel.modelList.length">
                    <text
                        v-if="
                            userInfo.is_member && userInfo.member_expired !== 1
                        "
                        class="flex-1 min-w-0"
                    >
                        已开通会员，不消耗次数
                    </text>
                    <text v-else>
                        <text>消耗</text>
                        <text class="text-primary"> 1 </text>
                        <text> 条对话次数</text>
                    </text>
                </view>
            </view>
        </view>
        <view
            v-if="chatModel.modelList.length"
            @click="chatModel.show = true"
            class="flex ml-auto justify-center items-center rounded-[30px] h-[60rpx]"
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
import { storeToRefs } from 'pinia'
import { getChatModelApi } from '@/api/chat'
import icon_member from '@/static/images/icon/icon_member.png'
import { useUserStore } from '@/stores/user'
import { useAppStore } from '@/stores/app'
import { reactive, watch } from 'vue'
import { onLoad, onShow } from '@dcloudio/uni-app'
import { computed } from 'vue'

const emit = defineEmits(['update:modelValue'])

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    }
})

const modelKey = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})

const userStore = useUserStore()
const appStore = useAppStore()
const { userInfo } = storeToRefs(userStore)
// 聊天模型数据
const chatModel = reactive({
    loading: true,
    show: false,
    index: 0,
    current: {
        balance: 1,
        key: '',
        member_free: true,
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
        chatModel.index = data.findIndex((item: any) => item.default)
        if (chatModel.index === -1) {
            chatModel.index = 0
        }
        chatModel.current = data[chatModel.index]
    } catch (error) {
        console.log('获取聊天模型数据错误=>', error)
    } finally {
        chatModel.loading = false
    }
}

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

getChatModelFunc()
</script>
