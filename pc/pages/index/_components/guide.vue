<template>
    <div
        class="py-[70px] bg-center bg-cover"
        :style="{
            backgroundImage: `url(${appStore.getImageUrl(prop.bgImage)})`
        }"
    >
        <div class="flex flex-col items-center">
            <div class="text-[24px]">
                {{ prop.content }}
            </div>

            <div class="mt-[20px]" v-if="prop.isShowBtn">
                <NuxtLink
                    :to="getLink(prop.link)"
                    :target="
                        typeof getLink(prop.link) == 'string'
                            ? '_blank'
                            : '_self'
                    "
                >
                    <ElButton type="primary" class="enter-btn" size="large">
                        {{ prop.btnText }}
                    </ElButton>
                </NuxtLink>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { useAppStore } from '@/stores/app'

const props = defineProps<{
    prop: any
}>()
const appStore = useAppStore()
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
    background: linear-gradient(
        90deg,
        var(--gradient-1) 0%,
        var(--gradient-2) 100%
    );
    border: none;
    padding: 10px 30px;
    border-radius: 8px;
}
</style>
