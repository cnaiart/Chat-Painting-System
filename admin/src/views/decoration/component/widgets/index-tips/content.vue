<template>
    <div class="container">
        <div class="bg-[#fff8e8] flex flex-col items-center m-[10px] py-[15px]">
            <div class="flex items-center">
                <img src="../../../image/icon_limit.png" class="h-[20px] w-[20px]" />
                <div class="text-lg ml-[10px]">{{ content?.title }}</div>
            </div>
            <div class="text-content text-sm mt-[20px]">{{ content?.sub_title }}</div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import type options from './options'
type OptionsType = ReturnType<typeof options>

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

<style lang="scss" scoped></style>
