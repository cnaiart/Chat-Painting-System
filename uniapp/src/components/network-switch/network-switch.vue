<template>
    <view
        class="flex items-center text-sm"
        v-if="appStore.getChatConfig.network_is_open"
    >
        <u-switch v-model="switchModel" size="30" @change="showTips"></u-switch>
        <span class="ml-[10rpx]">联网模式</span>
        <view class="flex px-[10rpx]" @click="showNetworkTips">
            <u-icon name="info-circle" size="30" />
        </view>
    </view>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import { computed, onMounted, watch } from 'vue'
import cache from '@/utils/cache'
const props = defineProps<{
    modelValue: boolean
}>()

const emit = defineEmits<{
    (event: 'update:modelValue', value: boolean): void
}>()
const appStore = useAppStore()
const switchModel = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})

const showNetworkTips = () => {
    uni.showModal({
        title: '温馨提示',
        showCancel: false,
        content: `1、开启联网模式后，AI机器人将可实时获取联网数据，由于网络搜索总结目前可能不太稳定，有时联网搜索出来的答案可能不太准确。
2、联网模式不支持连续对话，请在单次提问中描述清楚问题。
${
    appStore.getChatConfig.network_balance
        ? `3、开启联网后，每次对话将多消耗${appStore.getChatConfig.network_balance}条对话（会员不消耗，大模型内置支持联网时也不消耗条数）`
        : ''
}`
    })
}
const showTips = (open: boolean) => {
    uni.$u.toast(open ? '联网功能已开启' : '联网功能已关闭')
}
watch(switchModel, (value) => {
    cache.set('isOpenNetwork', value)
})

watch(
    () => appStore.getChatConfig.network_is_open,
    (value) => {
        if (value) {
            const isOpenNetwork = cache.get('isOpenNetwork')
            switchModel.value = Boolean(isOpenNetwork)
        }
    },
    {
        immediate: true
    }
)
</script>

<style scoped lang="scss"></style>
