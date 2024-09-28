<template>
    <page-meta :page-style="$theme.pageStyle"> </page-meta>
    <view class="user pb-[20rpx]">
        <view v-for="(item, index) in state.pages" :key="index">
            <template v-if="item.name == 'user-info'">
                <w-user-info
                    :content="item.content"
                    :styles="item.styles"
                    :user="userInfo"
                    :is-login="isLogin"
                />
            </template>
            <template v-if="item.name == 'open-vip'">
                <w-user-vip
                    :content="item.content"
                    :styles="item.styles"
                    :user="userInfo"
                    :is-login="isLogin"
                ></w-user-vip>
            </template>
            <template v-if="item.name == 'my-service'">
                <w-my-service :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'user-banner'">
                <w-user-banner :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'user-bottom'">
                <w-user-bottom :content="item.content" :styles="item.styles" />
            </template>
        </view>
    </view>
    <tabbar />
</template>

<script setup lang="ts">
import { getDecorate } from '@/api/shop'
import { useUserStore } from '@/stores/user'
import { onPullDownRefresh, onShow } from '@dcloudio/uni-app'
import { storeToRefs } from 'pinia'
import { reactive } from 'vue'

const state = reactive<{
    pages: any[]
}>({
    pages: []
})
const getData = async () => {
    const data = await getDecorate({ id: 1 })
    state.pages = JSON.parse(data.data)
}
const userStore = useUserStore()
const { userInfo, isLogin } = storeToRefs(userStore)
onShow(() => {
    getData()
    userStore.getUser()
})

onPullDownRefresh(async () => {
    getData()
    userStore.getUser()
    uni.stopPullDownRefresh()
})
</script>

<style></style>
