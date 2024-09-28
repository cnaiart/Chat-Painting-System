<template>
    <div class="overflow-hidden">
        <Menu
            class="navbar"
            :menu="menu"
            :default-active="activeMenu"
            mode="vertical"
        />
    </div>
</template>
<script setup lang="ts">
import Menu from '../menu/index.vue'
const route = useRoute()
const activeMenu = computed<string>(() => route.meta.activeMenu ?? route.path)
const { menu } = useMenu()
</script>
<style lang="scss" scoped>
.navbar {
    --el-menu-item-font-size: 14px;
    --el-menu-bg-color: var(--el-bg-color);
    --el-menu-active-color: var(--el-color-primary);
    --el-menu-text-color: var(--el-text-color-primary);
    // --el-menu-item-hover-fill: var(--el-color-primary);
    // --el-menu-hover-text-color: var(--color-white);
    --el-menu-hover-bg-color: var(--el-bg-color);
}
</style>
