<template>
    <Popup ref="popRef" title="限购提示" center cancelButtonText="">
        <div class="text-center">{{ tips }}</div>
    </Popup>
</template>

<script setup lang="ts">
import { shallowRef } from 'vue'

const popRef = shallowRef()
const tips = ref('')
const open = (value: string) => {
    popRef.value.open()
    tips.value = value
}

defineExpose({ open })
</script>

<style lang=" scss" scoped></style>
