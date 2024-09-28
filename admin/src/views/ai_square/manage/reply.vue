<template>
    <popup
        width="700px"
        title="查看原因"
        ref="popRef"
        @confirm="emit('success')"
        @close="emit('close')"
    >
        <div>
            <div class="mt-[20px]">
                <span class="mr-[20px]">审核状态</span>
                <span class="text-error">不通过</span>
            </div>
            <div class="mt-[20px]">
                <span class="mr-[20px]">审核原因</span>
                <span>{{ verify_result }}</span>
            </div>
        </div>
    </popup>
</template>

<script setup lang="ts">
import Popup from '@/components/popup/index.vue'

const emit = defineEmits(['success', 'close'])
const popRef = shallowRef()
const verify_result = ref('')

const open = (reply: any) => {
    popRef.value?.open()
    verify_result.value = reply
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
