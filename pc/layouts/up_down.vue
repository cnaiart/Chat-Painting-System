<template>
    <el-container
        class="layout-default bg-page h-full"
        :style="[{ height: `${windowHeight}px` }]"
    >
        <el-container>
            <el-header height="auto" style="padding: 0">
                <LayoutHeader />
            </el-header>
            <el-main style="padding: 0">
                <slot />
            </el-main>
        </el-container>

        <LayoutAccount v-if="userStore.showLogin" />
        <NoticePopup></NoticePopup>
    </el-container>
</template>
<script lang="ts" setup>
import { ElContainer, ElMain, ElHeader } from 'element-plus'
import LayoutHeader from './components/header/header1.vue'
import LayoutAccount from './components/account/index.vue'
import NoticePopup from './components/notice/index.vue'

import { useUserStore } from '~~/stores/user'
import { useWindowSize } from '@vueuse/core'
const userStore = useUserStore()
const route = useRoute()
const { height: windowHeight } = useWindowSize({
    includeScrollbar: false
})
</script>
<style lang="scss" scoped></style>
