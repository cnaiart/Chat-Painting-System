<template>
    <div class="title">
        <div class="flex flex-col items-center pt-[80px] pb-[40px]">
            <h1 class="font-medium text-[45px]">
                {{ prop.title }}
            </h1>
            <p class="max-w-[850px] text-center text-lg mt-[40px]">
                {{ prop.desc }}
            </p>
            <div v-if="prop.isShowBtn" class="mt-[40px] flex">
                <div>
                    <ElButton type="primary" class="enter-btn" size="large">
                        {{ prop.btnText }}
                    </ElButton>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { Prop } from './config'
const props = defineProps<{
    prop: Prop
}>()
</script>

<style lang="scss" scoped>
.enter-btn {
    --el-button-size: 60px;
    --el-font-size-base: 18px;
    background: linear-gradient(90deg, #a33afe, #762aff);
    border: none;
    padding: 20px 50px;
    border-radius: 8px;
}
</style>
