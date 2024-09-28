<template>
    <div class="py-[40px]">
        <div class="w-[1160px] mx-auto">
            <div class="mb-[20px]" v-for="(item, index) in getShowData" :key="index">
                <div
                    class="flex items-center gap-7"
                    :class="{
                        'flex-row-reverse': index % 2 == 1
                    }"
                >
                    <div class="flex-1">
                        <div class="text-[22px] font-bold">{{ item.title }}</div>
                        <div class="mt-[10px] text-[16px]">{{ item.subtitle }}</div>
                        <div class="mt-[30px]">
                            <div
                                class="mb-[15px] flex items-center"
                                v-for="(fItem, fIndex) in item.functionPoint"
                                :key="fIndex"
                            >
                                <Icon name="el-icon-SuccessFilled" class="text-primary" />
                                <span class="ml-[8px]">
                                    {{ fItem.text }}
                                </span>
                            </div>
                        </div>
                        <ElButton link type="primary">去使用 ></ElButton>
                    </div>
                    <div class="flex-1">
                        <DecorationImg width="560px" height="356px" fit="cover" :src="item.image" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { Prop } from './config'
import DecorationImg from '../../decoration-img.vue'
const props = defineProps<{
    prop: Prop
}>()
const getShowData = computed(() => props.prop.data.filter((item) => item.isShow))
</script>

<style lang="scss" scoped></style>
