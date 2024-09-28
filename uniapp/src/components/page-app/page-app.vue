<template>
    <view class="page-app" :style="getVars">
        <slot />
    </view>
</template>
<script lang="ts" setup>
import { useThemeStore } from '@/stores/theme'
import { computed } from 'vue'
const themeStore = useThemeStore()
const getVars = computed<string>(() => themeStore.vars)
</script>

<script lang="ts">
export default {
    options: {
        virtualHost: true
    }
}
</script>

<style lang="scss">
.page-app {
    height: 100%;
}
</style>
