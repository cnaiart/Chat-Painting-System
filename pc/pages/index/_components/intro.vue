<template>
    <div class="py-[40px]">
        <div class="w-[1200px] mx-auto">
            <div
                class="mb-[20px]"
                v-for="(item, index) in getShowData"
                :key="index"
            >
                <div
                    class="flex items-center"
                    :class="{
                        'flex-row-reverse': index % 2 == 0
                    }"
                >
                    <div class="flex-1 px-[20px]">
                        <div class="flex justify-center">
                            <ElImage
                                class="w-[560px] h-[356px]"
                                fit="cover"
                                :src="appStore.getImageUrl(item.image)"
                            />
                        </div>
                    </div>
                    <div class="flex-1 my-[30px] px-[20px]">
                        <div class="flex justify-start">
                            <div>
                                <div class="text-[22px] font-bold">
                                    {{ item.title }}
                                </div>
                                <div class="mt-[10px] text-[16px]">
                                    {{ item.subtitle }}
                                </div>
                                <div class="mt-[30px]">
                                    <div
                                        class="mb-[15px] flex items-center"
                                        v-for="(
                                            fItem, fIndex
                                        ) in item.functionPoint"
                                        :key="fIndex"
                                    >
                                        <span class="text-primary flex">
                                            <Icon
                                                name="el-icon-SuccessFilled"
                                            />
                                        </span>

                                        <span class="ml-[8px]">
                                            {{ fItem.text }}
                                        </span>
                                    </div>
                                </div>

                                <NuxtLink
                                    :to="getLink(item.link)"
                                    :target="
                                        typeof getLink(item.link) == 'string'
                                            ? '_blank'
                                            : '_self'
                                    "
                                >
                                    <ElButton link type="primary">
                                        去使用 >
                                    </ElButton>
                                </NuxtLink>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'

const props = defineProps<{
    prop: Record<string, any>
}>()
const appStore = useAppStore()
const getShowData = computed(() =>
    props.prop.data.filter((item: any) => item.isShow)
)
const getLink = (item: any) => {
    if (item?.type == 'custom') {
        return item?.query?.url
    } else {
        return {
            path: item?.path,
            query: item?.query
        }
    }
}
</script>

<style lang="scss" scoped>
.enter-btn {
    --el-button-size: 60px;
    --el-font-size-base: 18px;
    background: linear-gradient(90deg, #54c6ee 0%, #3c5efd 100%);
    border: none;
    padding: 20px 50px;
    border-radius: 8px;
}
</style>
