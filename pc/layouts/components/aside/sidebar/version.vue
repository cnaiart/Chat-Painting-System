<template>
    <div class="flex items-center justify-center text-sm text-tx-secondary">
        版本号: v{{ appStore.config.version }}
    </div>
</template>
<script lang="ts" setup>
import { useAppStore } from '~~/stores/app'
const appStore = useAppStore()
</script>

<style lang="scss" scoped></style>
