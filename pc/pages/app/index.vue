<template>
    <div class="app">
        <NuxtLayout name="app">
            <div class="h-full flex flex-col">
                <div class="header text-center">
                    <div class="title">全部应用</div>
                </div>
                <div class="flex-1 min-h-0">
                    <ElScrollbar>
                        <div class="mian flex flex-wrap mt-[30px]">
                            <NuxtLink
                                v-for="item in showList"
                                :key="item.pcLink.path"
                                :to="getLink(item.pcLink)"
                                :target="
                                    typeof getLink(item.pcLink) == 'string'
                                        ? '_blank'
                                        : '_self'
                                "
                            >
                                <div class="menu-item text-center">
                                    <ElImage
                                        :src="getImageUrl(item.image)"
                                        fil="fill"
                                        class="w-full h-[260px] rounded-[16px]"
                                    ></ElImage>
                                    <div
                                        class="mt-[16px] font-medium text-[18px]"
                                    >
                                        {{ item.title }}
                                    </div>
                                    <div
                                        class="mt-[12px] text-sm text-[#999999] truncate"
                                    >
                                        {{ item.desc }}
                                    </div>
                                </div>
                            </NuxtLink>
                        </div>
                    </ElScrollbar>
                </div>
            </div>
        </NuxtLayout>
    </div>
</template>
<script setup lang="ts">
import { getdecorate } from '@/api/index'
import { useAppStore } from '~~/stores/app'
const { getImageUrl } = useAppStore()
definePageMeta({
    layout: false
})

const decorate = ref([])
const getData = async () => {
    const data = await getdecorate({ id: 7 })
    decorate.value = JSON.parse(data.data)
}
const showList = computed(() => {
    return (
        decorate?.value[0]?.content.data.filter(
            (item: any) => item.is_show == 1
        ) || []
    )
})
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
getData()
</script>
<style lang="scss" scoped>
.header {
    .title {
        display: inline-block;
        font-size: 30px;
        font-weight: bold;
        background: linear-gradient(
            90deg,
            var(--gradient-1) 0%,
            var(--gradient-2) 100%
        );
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        -moz-background-clip: text;
        -moz-text-fill-color: transparent;
        -o-background-clip: text;
        -o-text-fill-color: transparent;
        background-clip: text;
        text-fill-color: transparent;
    }
}
.menu-item {
    display: inline-block;
    margin-right: 30px;
    padding: 20px;
    width: 300px;
    height: 380px;
    border-radius: 16px;
    background-color: #ffffff;
    box-shadow: 0 3px 10px var(--el-color-primary-light-9);
    margin-bottom: 20px;
}
</style>
