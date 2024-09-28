<template>
    <div ref="textRef">
        <el-tooltip v-bind="props" :disabled="disabled">
            <div
                :class="[`overflow-text line-clamp-${line}`]"
                :style="{ textOverflow: overflowType }"
            >
                {{ content }}
            </div>
        </el-tooltip>
    </div>
</template>

<script lang="ts" setup>
import { useEventListener } from '@vueuse/core'
import { useTooltipContentProps, type Placement } from 'element-plus'
import type { PropType } from 'vue'
import { ElTooltip } from 'element-plus'
const props = defineProps({
    ...useTooltipContentProps,
    teleported: {
        type: Boolean,
        default: false
    },
    placement: {
        type: String as PropType<Placement>,
        default: 'top'
    },
    overflowType: {
        type: String as PropType<'ellipsis' | 'unset' | 'clip'>,
        default: 'ellipsis'
    },
    line: {
        type: Number,
        default: 1
    }
})
const textRef = shallowRef<HTMLElement>()
const disabled = ref(false)

onMounted(() => {
    useEventListener(textRef, 'mouseenter', () => {
        console.log(textRef)
        if (textRef.value?.scrollWidth! > textRef.value?.offsetWidth!) {
            disabled.value = false
        } else {
            disabled.value = true
        }
    })
})
</script>

<style></style>
