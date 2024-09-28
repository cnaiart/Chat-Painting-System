<template>
    <div
        class="flex flex-col items-center p-4"
        ref="previewWrap"
        :style="[
            {
                minWidth: `${size.minWidth + 32}px`
            }
        ]"
    >
        <div :style="styles">
            <el-scrollbar>
                <div class="preview-content">
                    <Preview v-model:index="indexModel" :page-data="pageData">
                        <template #default="{ widget }">
                            <component
                                :is="widgets[widget?.name]?.content"
                                :prop="widget.prop"
                                :key="widget.id"
                            />
                        </template>
                    </Preview>
                </div>
            </el-scrollbar>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { useElementSize, useVModel } from '@vueuse/core'
import widgets from '../widgets-pc'
import Preview from './preview.vue'
import type { CSSProperties, PropType } from 'vue'

const props = defineProps({
    pageData: {
        type: Array as PropType<any[]>,
        default: () => []
    },
    index: {
        type: Number,
        default: 0
    }
})
const emit = defineEmits<{
    (event: 'update:index', value: number): void
}>()
const indexModel = useVModel(props, 'index', emit)
const previewWrap = shallowRef<HTMLDivElement>()
const { width, height } = useElementSize(previewWrap)
const size = {
    width: 1600,
    height: 900,
    minWidth: 600,
    maxWidth: 1000
}
const styles = computed<CSSProperties>(() => {
    let calcWidth = width.value
    if (width.value < size.minWidth) {
        calcWidth = size.minWidth
    }
    if (width.value > size.maxWidth) {
        calcWidth = size.maxWidth
    }
    const scale = calcWidth / size.width
    return {
        width: `${size.width}px`,
        height: `${height.value * (1 / scale)}px`,
        transform: `scale(${scale})`,
        transformOrigin: 'center top',
        backgroundSize: 'cover'
    }
})
</script>
<style lang="scss" scoped>
.preview-content {
    background: radial-gradient(farthest-side at 0 0, rgb(246, 235, 255) 30%, #fff 100%);
}
</style>
