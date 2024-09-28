<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="p-[20rpx]">
        <!-- <u-parse :html="agreementContent"></u-parse> -->
        <mp-html :content="agreementContent" />
    </view>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>

<script setup lang="ts">
import { ref, shallowRef } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { getPolicy } from '@/api/app'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const agreementType = ref('') // 协议类型
const agreementContent = ref('') // 协议内容

const getData = async (type) => {
    const res = await getPolicy({ type })
    agreementContent.value = res.content
    uni.setNavigationBarTitle({
        title: String(res.title || '协议政策')
    })
}

onLoad((options: any) => {
    if (options.type) {
        agreementType.value = options.type
        getData(agreementType.value)
    }
})
</script>

<style lang="scss" scoped></style>
