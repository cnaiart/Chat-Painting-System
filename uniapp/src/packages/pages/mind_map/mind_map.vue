<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <!-- #ifndef H5 -->
    <web-view :src="url" @message="getMessage" />
    <!-- #endif -->
    <!-- #ifdef H5 -->
    <webview />
    <tabbar />
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>

<script setup lang="ts">
import { computed } from 'vue'
// #ifdef H5
import webview from './webview.vue'
// #endif
import { useUserStore } from '@/stores/user'
import { getClient } from '@/utils/client'
import { ClientEnum } from '@/enums/appEnums'
import { onReady, onLoad } from '@dcloudio/uni-app'
import { getCurrentInstance } from 'vue'
import { base64ToPath } from '@/utils/imgPath.js'
import config from '@/config'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const userStore = useUserStore()
const client = computed(() => {
    if (getClient() == ClientEnum.ANDROID || getClient() == ClientEnum.IOS) {
        return 'isApp'
    } else {
        return 'noApp'
    }
})

const getMessage = async (event: any) => {
    const data = event.detail.data
    const path = await base64ToPath(data[0].base64)
    uni.saveImageToPhotosAlbum({
        filePath: path,
        success: (result) => {
            uni.$u.toast('保存成功！')
        },
        fail: (err) => {
            uni.$u.toast('保存失败！')
        }
    })
}

const url = computed(() => {
    const domain = config.baseUrl
    return `${domain}mobile/packages/pages/mind_map/webview?is_web_view=1&token=${userStore.token}&client=${client.value}`
})
</script>

<style lang="scss">
page {
    height: 100%;
    overflow: hidden;
}
</style>
