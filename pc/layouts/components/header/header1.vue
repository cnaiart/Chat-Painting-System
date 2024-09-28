<template>
    <header class="layout-header px-[16px]">
        <div class="header-contain">
            <Logo class="flex-none mr-4" />
            <div class="flex-1"></div>
            <!-- <Search class="mr-[40px] flex-none" />
           -->
            <User class="flex-none ml-[10px]" />
        </div>
    </header>
</template>
<script lang="ts" setup>
import User from './user1.vue'
import Logo from './logo.vue'

import { useUserStore } from '~~/stores/user'
const userStore = useUserStore()
</script>

<style lang="scss" scoped>
.layout-header {
    height: var(--header-height);
    border-bottom: 1px solid var(--el-border-color-extra-light);
    position: sticky;
    top: 0;
    width: 100%;
    z-index: 1999;
    @apply bg-white;
    .header-contain {
        height: 100%;
        display: flex;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
    }
}
</style>
