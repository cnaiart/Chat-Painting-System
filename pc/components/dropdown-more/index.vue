<template>
    <div
        class="dropdown-more"
        :style="{
            '--dropdown-more-default-height': `${defaultHeight}px`,
            '--dropdown-more-z-index': zIndex
        }"
        @click.stop
    >
        <div class="dropdown-placeholder" />
        <div ref="contentRef" class="dropdown-content" :style="contentStyle">
            <div ref="slotRef" class="dropdown-slot">
                <slot />
            </div>
            <div
                v-if="showMoreIcon"
                :style="{
                    transform: `rotateZ(${showMore ? '180deg' : '0'})`
                }"
                class="dropdown-icon cursor-pointer"
                @click="showMore = !showMore"
            >
                <Icon :color="iconColor" name="el-icon-ArrowDown" />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import {
    useElementBounding,
    useElementSize,
    useEventListener
} from '@vueuse/core'
const props = withDefaults(
    defineProps<{
        defaultHeight?: number
        bg?: string
        iconColor?: string
        zIndex?: number
    }>(),
    {
        defaultHeight: 32,
        bg: 'white',
        iconColor: 'inherit',
        zIndex: 999
    }
)
const showMore = ref(false)
const showMoreIcon = ref(false)
const slotRef = shallowRef<HTMLDivElement>()
const contentHeight = ref<string | number>('auto')
const contentRef = shallowRef<HTMLDivElement>()
const contentStyle = computed(() => {
    const height = showMore.value ? contentHeight.value : props.defaultHeight
    return {
        height: typeof height === 'number' ? `${height}px` : height,
        background: props.bg
    }
})
watch(showMore, async (value) => {
    if (value) {
        await nextTick()
        contentHeight.value = slotHeight.value ? slotHeight.value : 'auto'
    } else {
        contentHeight.value = props.defaultHeight
    }
})
const { height: slotHeight } = useElementSize(slotRef)
const { x, y, height, width } = useElementBounding(contentRef)
watchEffect(() => {
    if (slotHeight.value > props.defaultHeight) {
        showMoreIcon.value = true
    } else {
        showMoreIcon.value = false
    }
})
useEventListener(
    window,
    'click',
    (e) => {
        if (
            e.clientX > x.value &&
            e.clientX < x.value + width.value &&
            e.clientY > y.value &&
            e.clientY < y.value + height.value
        ) {
            return
        }
        showMore.value = false
    },
    {
        capture: true
    }
)

defineExpose({
    hidden() {
        showMore.value = false
    },
    show() {
        showMore.value = true
    }
})
</script>
<style lang="scss" scoped>
.dropdown-more {
    position: relative;
    .dropdown-placeholder {
        height: var(--dropdown-more-default-height);
    }
    .dropdown-content {
        position: absolute;
        top: 0;
        width: 100%;
        overflow: hidden;
        display: flex;
        transition: all 0.5s;
        z-index: var(--dropdown-more-z-index);
        .dropdown-slot {
            flex: 1;
            min-width: 0;
            min-height: 0;
            position: absolute;
            padding-right: 20px;
        }
        .dropdown-icon {
            position: absolute;
            right: 0;
            height: var(--dropdown-more-default-height);
            display: flex;
            align-items: center;
            transition: all 0.5s;
            padding: 0 5px;
        }
    }
}
</style>
