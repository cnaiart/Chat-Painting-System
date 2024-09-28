<template>
    <div class="session flex flex-col bg-white">
        <div class="p-[16px]">
            <ElButton
                type="primary"
                class="w-full !h-[40px] !border-none !rounded-[8px]"
                :loading="isLock"
                style="
                    background: linear-gradient(
                        90deg,
                        var(--gradient-1) 0%,
                        var(--gradient-2) 100%
                    );
                "
                @click="sessionAddLock"
            >
                + 新建会话
            </ElButton>
        </div>
        <div class="flex-1 min-h-0">
            <ElScrollbar>
                <div class="px-[16px]">
                    <div v-for="item in sessionLists" :key="item.id">
                        <Item
                            v-model="sessionActive"
                            :item-id="item.id"
                            :name="item.name"
                            @edit="sessionEdit"
                            @delete="sessionDelete"
                        />
                    </div>
                </div>
            </ElScrollbar>
        </div>
        <div class="p-[16px]">
            <ElButton class="w-full" @click="sessionClear">
                <Icon name="el-icon-Delete" />
                <span class="ml-[10px]">清除所有会话</span>
            </ElButton>
        </div>
    </div>
</template>
<script lang="ts" setup>
import Item from './item.vue'

import { useAppStore } from '~/stores/app'
import { useSessionList } from '@/composables/useSessionList'
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
if (appStore.getChatConfig.is_reopen) {
    sessionAddLock()
    appStore.getChatConfig.is_reopen = 0
}
</script>
<style lang="scss" scoped>
.session {
    width: 240px;
    height: 100%;
}
</style>
