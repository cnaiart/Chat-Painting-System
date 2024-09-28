<template>
    <div
        v-if="appStore.getChatConfig.network_is_open"
        class="flex justify-center items-center rounded-lg h-[30px] px-[10px]"
    >
        <ElSwitch v-model="value" size="small" @change="showTips"></ElSwitch>
        <span class="mx-[6px] text-xs">联网模式</span>
        <el-popover placement="right" :width="240" trigger="hover" teleported>
            <div class="text-xs">
                <div>
                    1.开启联网模式后，AI机器人将可实时获取联网数据，由于网络搜索总结目前可能不太稳定，有时联网搜索出来的答案可能不太准确。
                </div>
                <div>
                    2.联网模式不支持连续对话，请在单次提问中描述清楚问题。
                </div>
                <div v-if="appStore.getChatConfig.network_balance">
                    3、开启联网后，每次对话将多消耗<span class="text-primary">{{
                        appStore.getChatConfig.network_balance
                    }}</span
                    >条对话（会员不消耗，大模型内置支持联网时也不消耗条数）
                </div>
            </div>

            <template #reference>
                <el-button link>
                    <Icon name="el-icon-Warning" />
                </el-button>
            </template>
        </el-popover>
    </div>
</template>

<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import { useAppStore } from '~/stores/app'
const props = defineProps<{
    modelValue: boolean
}>()

const emit = defineEmits<{
    (event: 'update:modelValue', value: boolean): void
}>()
const appStore = useAppStore()
const value = useVModel(props, 'modelValue', emit)

const showTips = (open: string) => {
    open
        ? feedback.msgSuccess('联网功能已开启')
        : feedback.msgError('联网功能已关闭')
}

watch(value, (value) => {
    const isOpenNetwork = useCookie('isOpenNetwork')
    isOpenNetwork.value = String(value)
})
onMounted(() => {
    if (appStore.getChatConfig.network_is_open) {
        const isOpenNetwork = useCookie('isOpenNetwork')
        value.value = Boolean(isOpenNetwork.value)
    }
})
</script>

<style scoped lang="scss"></style>
