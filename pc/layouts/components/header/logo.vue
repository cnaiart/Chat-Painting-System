<template>
    <NuxtLink v-if="appStore.getWebsiteConfig.pc_logo" class="flex" to="/">
        <div class="flex items-center">
            <div class="w-[34px] h-[34px]">
                <img :src="appStore.getWebsiteConfig.pc_logo" />
            </div>
            <div class="text-black ml-[10px] font-bold text-[18px]">
                {{ appStore.getWebsiteConfig.pc_title }}
            </div>
        </div>
    </NuxtLink>
</template>
<script lang="ts" setup>
import { useAppStore } from '~~/stores/app'
const appStore = useAppStore()
</script>

<style lang="scss" scoped></style>
