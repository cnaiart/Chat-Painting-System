<template>
    <div @click="open" class="line-clamp-2 cursor-pointer">{{ content }}</div>
    <Popup ref="popRef" :title="title" width="700px">
        <div>{{ content }}</div>
    </Popup>
</template>

<script setup lang="ts">
const props = defineProps({
    content: {
        type: String,
        default: ''
    },
    title: {
        type: String,
        default: '用户提问'
    }
})

const popRef = shallowRef()

const open = () => {
    popRef.value.open()
}

defineExpose({ open })
</script>

<style lang="scss" scoped></style>
