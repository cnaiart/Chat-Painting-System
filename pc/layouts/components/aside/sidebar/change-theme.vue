<template>
    <div
        class="change-theme border-t border-solid border-br-extra-light mx-[10px] py-[10px] flex items-center justify-center text-primary cursor-pointer"
        @click="changeTheme"
    >
        <Icon name="local-icon-change" />
        <div class="ml-[8px] text-sm">切换风格</div>
    </div>
</template>

<script lang="ts" setup>
import { useSettingStore } from '@/stores/setting'
const settingStore = useSettingStore()
const changeTheme = () => {
    if (settingStore.sideTheme === 'light') {
        settingStore.setSetting({
            key: 'sideTheme',
            value: 'dark'
        })
    } else {
        settingStore.setSetting({
            key: 'sideTheme',
            value: 'light'
        })
    }
}
</script>
<style lang="scss" scoped>
.layout-sidebar.dark {
    .change-theme {
        @apply text-white;
    }
}
</style>
