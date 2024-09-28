<template>
    <div>
        <NuxtLayout name="default">
            <template #panel>
                <ControlPanel
                    ref="controlPanelRef"
                    @update="descUpdate"
                    @history="handleShowHistory"
                />
            </template>
            <div class="h-full pb-[15px]" v-if="!showHistory">
                <div class="h-full bg-white rounded-[20px] relative p-[15px]">
                    <MindMapPreview
                        ref="mindMapPreviewRef"
                        v-if="descInput.length"
                    />
                    <EmptyView v-else />
                </div>
            </div>

            <MindMapHistory
                v-else
                @view="handlePreview"
                @history="handleShowHistory"
            />
        </NuxtLayout>
    </div>
</template>

<script setup lang="ts">
import ControlPanel from './component/control-panel.vue'
import MindMapPreview from './component/mind-map-preview.vue'
import EmptyView from './component/empty-view.vue'
import MindMapHistory from './component/mind-map-history.vue'
import { useUserStore } from '~/stores/user'

const descInput = ref('')
const showHistory = ref(false)
const mindMapPreviewRef = shallowRef<InstanceType<typeof MindMapPreview>>()
const controlPanelRef = shallowRef<InstanceType<typeof ControlPanel>>()
const userStore = useUserStore()
const handleShowHistory = async () => {
    if (!userStore.isLogin) return userStore.toggleShowLogin()
    showHistory.value = !showHistory.value
    if (!showHistory.value) {
        await nextTick()
        mindMapPreviewRef.value?.renderMarkMap(descInput.value)
    }
}
const descUpdate = async (value) => {
    descInput.value = value
    showHistory.value = false
    await nextTick()
    mindMapPreviewRef.value?.renderMarkMap(value)
}
const handlePreview = async (value) => {
    showHistory.value = false
    controlPanelRef.value?.changDescInput(value)
    await nextTick()
    mindMapPreviewRef.value?.renderMarkMap(value)
}
definePageMeta({
    layout: false,
    hiddenLayoutFooter: true
})
</script>
