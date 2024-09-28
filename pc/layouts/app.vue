<template>
    <el-container
        class="layout-app bg-page h-full"
        :style="[{ height: `${windowHeight}px` }]"
    >
        <el-container class="min-h-0">
            <el-aside width="auto">
                <LayoutAside>
                    <template v-if="$slots?.panel" #panel>
                        <slot name="panel" />
                    </template>
                </LayoutAside>
            </el-aside>
            <el-container>
                <el-header height="auto" style="padding: 0">
                    <LayoutHeader />
                </el-header>
                <el-main style="padding: 0">
                    <LayoutMain>
                        <template v-if="$slots?.mainLeft" #mainLeft>
                            <slot name="mainLeft" />
                        </template>
                        <slot />
                        <template v-if="$slots?.mainRight" #mainRight>
                            <slot name="mainRight" />
                        </template>
                    </LayoutMain>
                    <!-- <ToLogin class="h-full" v-else /> -->
                </el-main>
                <el-footer height="auto" v-if="showFooter">
                    <LayoutFooter />
                </el-footer>
            </el-container>
        </el-container>

        <LayoutAccount v-if="userStore.showLogin" />
        <NoticePopup></NoticePopup>
    </el-container>
</template>
<script lang="ts" setup>
import { ElContainer, ElAside, ElMain, ElHeader, ElFooter } from 'element-plus'
import LayoutHeader from './components/header/index.vue'
import LayoutMain from './components/main/index.vue'
import LayoutFooter from './components/footer/index.vue'
import LayoutAccount from './components/account/index.vue'
import LayoutAside from './components/aside/index.vue'
import NoticePopup from './components/notice/index.vue'

import { useUserStore } from '~~/stores/user'
import ToLogin from './components/account/to-login.vue'
import { useWindowSize } from '@vueuse/core'
const userStore = useUserStore()
const route = useRoute()
const { height: windowHeight } = useWindowSize({
    includeScrollbar: false
})
const showFooter = computed(() => !route.meta.hiddenLayoutFooter)
</script>
<style lang="scss" scoped>
.layout-app {
    background: linear-gradient(
        to right,
        var(--el-color-primary-light-9),
        #ffffff,
        var(--el-color-primary-light-9)
    );
}
</style>
