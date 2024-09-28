<template>
    <div class="menu mx-[10px] border-b border-solid border-br-extra-light">
        <MenuItem
            v-for="(item, index) in menu"
            :to="{
                path: item.link?.path,
                query: item.link?.query
            }"
            :selected="item.selected"
            :unselected="item.unselected"
            :key="index"
            :name="item.name"
        />
        <Server>
            <MenuItem icon="local-icon-customer_service" name="联系客服" />
        </Server>
    </div>
</template>

<script setup lang="ts">
import MenuItem from './menu-item.vue'
import { useAppStore } from '@/stores/app'
import Server from './server.vue'

const appStore = useAppStore()
const menu = computed(() => {
    return (
        appStore.getTabbarConfig.menu
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
</script>
<style lang="scss" scoped>
.menu-item {
    display: flex;
    align-items: center;
}
</style>
