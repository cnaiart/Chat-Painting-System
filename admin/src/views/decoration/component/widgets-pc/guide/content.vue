<template>
    <div
        class="py-[70px] bg-center bg-cover"
        :style="{
            backgroundImage: `url(${getImageUrl(prop.bgImage)})`
        }"
    >
        <div class="flex flex-col items-center">
            <div class="text-[24px]">
                {{ prop.content }}
            </div>

            <div class="mt-[20px]" v-if="prop.isShowBtn">
                <ElButton type="primary" class="enter-btn" size="large">
                    {{ prop.btnText }}
                </ElButton>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { Prop } from './config'
import useAppStore from '@/stores/modules/app'
const { getImageUrl } = useAppStore()
const props = defineProps<{
    prop: Prop
}>()
</script>

<style lang="scss" scoped>
.enter-btn {
    background: linear-gradient(90deg, #a33afe, #762aff);
    border: none;
    padding: 10px 30px;
    border-radius: 8px;
}
</style>
