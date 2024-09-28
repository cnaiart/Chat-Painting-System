<template>
    <div class="user-bottom flex items-center p-[16px] mt-[20px] text-center">
        <div class="text-[12px] w-full">
            <span class="">{{ content.title }}</span>
            <span class="mr-[4px]">{{ content.content }}</span>
            <el-icon class="align-middle" v-if="content?.canCopy"><CopyDocument /></el-icon>
        </div>
    </div>
</template>

<script lang="ts" setup>
import type { PropType } from 'vue'
import type options from './options'
type OptionsType = ReturnType<typeof options>

import { CopyDocument } from '@element-plus/icons-vue'

const props = defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({})
    }
})
</script>

<style lang="scss" scoped>
.user-info {

}
</style>
