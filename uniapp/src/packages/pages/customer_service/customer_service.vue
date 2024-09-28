<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="customer-service">
        <view v-for="(item, index) in state.pages" :key="index">
            <template v-if="item.name == 'customer-service'">
                <w-customer-service
                    :content="item.content"
                    :styles="item.styles"
                />
            </template>
        </view>
        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
        <tabbar />
    </view>
</template>

<script setup lang="ts">
import { getDecorate } from '@/api/shop'
import { onLoad, onPullDownRefresh } from '@dcloudio/uni-app'
import { reactive } from 'vue'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
const state = reactive<{
    pages: any[]
}>({
    pages: []
})
const getData = async () => {
    const data = await getDecorate({ id: 3 })
    state.pages = JSON.parse(data.data)
}
onLoad(() => {
    getData()
})

onPullDownRefresh(async () => {
    getData()
    uni.stopPullDownRefresh()
})
</script>

<style></style>
