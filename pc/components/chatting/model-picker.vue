<template>
    <div class="flex items-center py-[10px]">
        <template v-if="chatModel.modelList.length">
            <el-select
                class="flex-1 min-w-[210px]"
                v-model="value"
                filterable
                @change="handleChangeModel"
            >
                <el-option
                    v-for="item in chatModel.modelList"
                    :value="item.key"
                    :label="item.alias"
                    :key="item.key"
                />
            </el-select>
            <div
                class="ml-[10px] text-xs text-[#999999]"
                v-if="!chatModel?.current?.member_free"
            >
                <span v-if="chatModel?.current?.balance">
                    消耗
                    <span class="text-primary">{{
                        chatModel?.current?.balance
                    }}</span>
                    条对话次数
                </span>

                <div class="text-xs text-[#999999]" v-else>免费</div>
            </div>

            <div class="ml-[10px] text-xs text-[#999999]" v-else>会员免费</div>
        </template>
        <template v-else-if="userStore.isLogin">
            <div
                class="ml-[10px] text-[#999999]"
                v-if="
                    userStore.userInfo.is_member &&
                    userStore.userInfo.member_expired !== 1
                "
            >
                会员免费
            </div>
            <div class="ml-[10px] text-[#999999]" v-else>
                消耗
                <span class="text-primary">1</span>
                条对话次数
            </div>
        </template>
    </div>
</template>
<script setup lang="ts">
import { ArrowDown } from '@element-plus/icons-vue'
import { getChatModelApi } from '@/api/chat'
import { useUserStore } from '~/stores/user'
import { useVModel } from '@vueuse/core'
const userStore = useUserStore()
const emit = defineEmits(['update:modelValue'])

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    }
})
const value = useVModel(props, 'modelValue', emit)

// 聊天模型数据
const chatModel = reactive({
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

        value.value = chatModel.current?.key || ''
    } catch (error) {
        console.log('获取聊天模型数据错误=>', error)
    }
}

const handleChangeModel = (key: any) => {
    chatModel.current = chatModel.modelList.find((item: any) => item.key == key)
}

getChatModelFunc()
</script>
