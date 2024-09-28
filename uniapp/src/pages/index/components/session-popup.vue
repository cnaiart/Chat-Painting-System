<template>
    <u-popup v-model="show" mode="left" width="480rpx" safe-area-inset-bottom>
        <view class="flex flex-col h-full relative z-[9999]">
            <view class="px-[20rpx] py-[20rpx]">
                <u-button
                    type="primary"
                    :custom-style="{ width: '100%' }"
                    @click="sessionAddLock"
                    :loading="isLock"
                >
                    + 新建会话
                </u-button>
            </view>
            <view class="flex-1 min-h-0">
                <scroll-view scroll-y class="h-full">
                    <view class="px-[20rpx]">
                        <view v-for="item in sessionLists" :key="item.id">
                            <session-item
                                v-model="sessionActive"
                                :item-id="item.id"
                                :name="item.name"
                                @edit="sessionEdit"
                                @delete="sessionDelete"
                            />
                        </view>
                    </view>
                </scroll-view>
            </view>
            <view class="px-[20rpx] py-[20rpx]">
                <u-button
                    :custom-style="{ width: '100%' }"
                    @click="sessionClear"
                >
                    删除所有会话
                </u-button>
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useAppStore } from '@/stores/app'
import { useSessionList } from './useSessionList'
import { useLockFn } from '@/hooks/useLockFn'
import SessionItem from './session-item.vue'
import { watch } from 'vue'
const props = defineProps({
    modelValue: {
        type: Boolean
    }
})
const emit = defineEmits<{
    (event: 'update:modelValue', value: boolean): void
}>()
const show = computed({
    get: () => {
        return props.modelValue
    },
    set: (value) => {
        emit('update:modelValue', value)
    }
})
const {
    sessionLists,
    sessionAdd,
    sessionEdit,
    sessionDelete,
    sessionClear,
    sessionActive
} = useSessionList()

const { lockFn: sessionAddLock, isLock } = useLockFn(async () => {
    await sessionAdd()
})

const appStore = useAppStore()
watch(
    () => appStore.getChatConfig,
    () => {
        if (appStore.getChatConfig.is_reopen) {
            sessionAddLock()
            appStore.getChatConfig.is_reopen = 0
        }
    },
    {
        immediate: true
    }
)
</script>
