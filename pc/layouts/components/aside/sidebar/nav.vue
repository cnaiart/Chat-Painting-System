<template>
    <div class="nav">
        <div class="px-[10px]">
            <template v-for="(item, index) in nav" :key="index">
                <NuxtLink
                    v-if="!item?.hidden"
                    :to="getLink(item)"
                    class="nav-item"
                    :class="{
                        active: currentIndex === index
                    }"
                    :target="
                        typeof getLink(item) == 'string' ? '_blank' : '_self'
                    "
                    @click="currentIndex = index"
                >
                    <img
                        class="w-[18px] h-[18px] object-cover"
                        :src="
                            currentIndex === index
                                ? item.selected
                                : item.unselected
                        "
                    />
                    <div class="ml-[10px] font-bold line-clamp-1">
                        {{ item.name }}
                    </div>
                </NuxtLink>
            </template>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useAppStore } from '~/stores/app'
const route = useRoute()
const currentIndex = ref(-1)
const appStore = useAppStore()
const nav = computed(() => {
    return (
        appStore.getTabbarConfig.nav
            ?.filter((item: any) => item.is_show == '1')
            .map((item: any) => {
                return {
                    ...item,
                    selected: appStore.getImageUrl(item.selected),
                    unselected: appStore.getImageUrl(item.unselected)
                }
            }) || []
    )
})
const routeIndex = nav.value.findIndex((nav) => {
    const routePath =
        route.path === '/' ? route.path : route.path.replace(/\/$/, '')
    return (
        nav.link?.path === route.meta.activePath || nav.link?.path === routePath
    )
})
if (routeIndex !== -1) {
    currentIndex.value = routeIndex
}

const getLink = (item: any) => {
    if (item.link?.type == 'custom') {
        return item.link?.query?.url
    } else {
        return {
            path: item.link?.path,
            query: item.link?.query
        }
    }
}
</script>

<style lang="scss" scoped>
.layout-sidebar.dark {
    .nav {
        .nav-item {
            &.active {
                color: var(--aside-color);
                background: #2b2d31;
            }
        }
    }
}
.nav {
    .nav-item {
        border-radius: 8px;
        display: flex;
        align-items: center;
        height: 40px;
        margin: 10px 0;
        padding: 0 12px;
        &.active {
            @apply text-btn-text;
            background: linear-gradient(
                90deg,
                var(--gradient-1) 0%,
                var(--gradient-2) 100%
            );
        }
    }
}
</style>
