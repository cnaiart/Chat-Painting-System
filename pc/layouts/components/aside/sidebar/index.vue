<template>
    <ElScrollbar
        :class="['layout-sidebar', settingStore.sideTheme]"
        :style="styleVars"
    >
        <div class="h-full flex flex-col">
            <TitleLogo />
            <Nav class="mb-auto" />
            <Menu />
            <Version class="my-[10px]" />
            <ChangeTheme />
        </div>
    </ElScrollbar>
</template>

<script lang="ts" setup>
import TitleLogo from './title-logo.vue'
import Nav from './nav.vue'
import Menu from './menu.vue'
import Version from './version.vue'
import ChangeTheme from './change-theme.vue'
import { useSettingStore } from '@/stores/setting'
const settingStore = useSettingStore()
const styleVars = computed(() => {
    if (settingStore.sideTheme == 'light') {
        return {
            '--aside-bg-color': '#fff',
            '--aside-color': '#333'
        }
    } else {
        return {
            '--aside-bg-color': '#0D0F10',
            '--aside-color': '#fff',
            '--el-border-color-extra-light': '#2B2D31'
        }
    }
})
</script>
<style lang="scss" scoped>
.layout-sidebar {
    background-color: var(--aside-bg-color);
    width: var(--sidebar-width);
    color: var(--aside-color);
    height: 100%;
    :deep(.el-scrollbar__view) {
        height: 100%;
    }
}
</style>
