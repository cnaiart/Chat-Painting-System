<template>
    <NuxtLink
        :to="getLink"
        :target="typeof getLink == 'string' ? '_blank' : '_self'"
    >
        <div
            class="menu-item"
            :class="{
                'menu-item--active': isActive
            }"
        >
            <Icon v-if="icon" :name="icon" :size="16" />
            <img
                v-else
                class="w-[16px] h-[16px] object-cover"
                :src="isActive ? selected : unselected"
            />
            <span class="ml-[6px] line-clamp-1">{{ name }}</span>
        </div>
    </NuxtLink>
</template>
<script setup lang="ts">
import { RouteLocationPathRaw } from 'vue-router'
const props = defineProps<{
    to?: RouteLocationPathRaw
    icon?: string
    selected?: string
    unselected?: string
    name: string
}>()
const route = useRoute()
const isActive = computed(() => {
    const routePath =
        route.path === '/' ? route.path : route.path.replace(/\/$/, '')
    return (
        props.to &&
        (props.to.path === route.meta.activePath || props.to.path === routePath)
    )
})
const getLink = computed(() => {
    const to: any = props.to as any
    if (to?.path == '/pages/webview/webview' && to?.path) {
        return to.query.url
    } else {
        return to
    }
})
</script>
<style lang="scss" scoped>
.layout-sidebar.dark {
    .menu-item {
        &--active {
            color: var(--aside-color);
            background: #2b2d31;
        }
    }
}
.menu-item {
    display: flex;
    align-items: center;
    // background-color: var(--el-bg-color-page);
    height: 38px;
    line-height: 38px;
    padding: 0 12px;
    border-radius: 8px;
    cursor: pointer;
    margin: 3px 0;
    @apply text-sm;
    &--active {
        @apply text-primary bg-primary-light-9;
    }
}
</style>
