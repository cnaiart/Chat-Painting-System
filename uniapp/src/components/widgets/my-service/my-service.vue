<template>
    <div class="my-service bg-white mx-[20rpx] mb-[20rpx] rounded-lg">
        <div
            v-if="content.style == 1"
            class="flex flex-wrap pt-[40rpx] pb-[20rpx]"
        >
            <div
                v-for="(item, index) in showList"
                :key="index"
                class="flex flex-col items-center w-1/4 mb-[15px]"
                @click="handleClick(item.link, item.name)"
            >
                <u-image
                    width="52"
                    height="52"
                    :src="getImageUrl(item.image)"
                    alt=""
                />
                <div class="mt-[7px]">{{ item.name }}</div>
            </div>
        </div>
        <div v-if="content.style == 2">
            <div
                v-for="(item, index) in showList"
                :key="index"
                class="flex items-center nav-item h-[100rpx] px-[24rpx]"
                @click="handleClick(item.link, item.name)"
            >
                <u-image
                    width="48"
                    height="48"
                    :src="getImageUrl(item.image)"
                    alt=""
                />
                <div class="ml-[20rpx] flex-1">{{ item.name }}</div>
                <div class="text-muted">
                    <u-icon name="arrow-right" />
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import { navigateTo } from '@/utils/util'
import { computed } from 'vue'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})
const { getImageUrl } = useAppStore()

const showList = computed(() => {
    return (
        props.content.data?.filter((item: any) =>
            item.is_show ? item.is_show == '1' : true
        ) || []
    )
})
const handleClick = (link: any, name: string) => {
    link.name = name
    navigateTo(link, true)
}
</script>

<style lang="scss" scoped>
.nav-item {
    &:not(:last-of-type) {
        @apply border-light border-solid border-0 border-b;
    }
}
</style>
