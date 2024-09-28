<template>
    <div
        class="rounded-[12px] relative image-cover"
        :class="{ ld: !loading }"
        :style="{
            '--image-width': width,
            '--image-height': height,
            background: `url(${ thumbnail || image }) center center / cover no-repeat`
        }"
        @click="emit('on-click', [image])"
    >
    </div>
</template>
<script setup lang="ts">
const emit = defineEmits<{
    (event: 'refresh'): void
    (event: 'on-click', value: any): void
}>()
const props = withDefaults(
    defineProps<{
        thumbnail: string
        image: string
    }>(),
    {
        thumbnail: '',
        image: ''
    }
)

const width = ref<number>(350)
const height = ref<string>('400px')
const loading = ref<boolean>(true)

const onload = () => {
    const image = new Image()
    image.onload = () => {
        emit('refresh')
        loading.value = false
        width.value = image.width
        height.value = image.height + 'px'
    }
    image.src = props.thumbnail || props.image
}

onload()
</script>

<style scoped>
.ld::after {
    background: transparent !important;
    -webkit-backdrop-filter: blur(0) !important;
    backdrop-filter: blur(0) !important;
}
.image-cover {
    width: var(--col-width);
    height: calc(var(--image-height) * var(--col-width) / var(--image-width));
    overflow: hidden;
    box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.02);
    transition: background .2s;
    transform: translateZ(0);
}
.image-cover::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.05);
    -webkit-backdrop-filter: blur(42.5px);
    backdrop-filter: blur(42.5px);
    z-index: 1;
    transition: all .4s;
    border-radius: 12px
}
</style>