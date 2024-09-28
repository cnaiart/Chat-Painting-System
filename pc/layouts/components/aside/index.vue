<template>
    <div class="layout-aside h-full flex justify-stretch">
        <Sidebar />
        <div class="panel" v-if="$slots.panel">
            <slot name="panel" />
        </div>
    </div>
</template>

<script lang="ts" setup>
import Sidebar from './sidebar/index.vue'
</script>
<style lang="scss" scoped>
.layout-aside {
    padding-bottom: env(safe-area-inset-bottom);
}
.layout-aside {
    :deep(.el-drawer__body) {
        padding: 0;
    }
    .panel {
        border-left: 1px solid var(--el-border-color-light);
    }
}
</style>
