<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="safe-area-inset-bottom chat">
        <view class="flex-1 min-h-0">
            <chat-scroll-view
                ref="chatRef"
                :tips="modelInfo.tips"
                :type="pageOptions.type"
                :otherId="pageOptions.id"
                :currentModel="modelKey"
                :avatar="modelInfo.image"
            >
                <template #top>
                    <view class="border-b border-solid border-light border-0">
                        <model-picker v-model="modelKey" />
                    </view>
                </template>
                <template #empty>
                    <view
                        class="chat-record pt-[20rpx] pb-[40rpx] w-full absolute top-0 left-0"
                    >
                        <chat-record-item
                            v-if="modelInfo.tips"
                            type="left"
                            :avatar="modelInfo.image"
                            :content="`${modelInfo.tips}`"
                            :show-collect-btn="false"
                            :showCopyBtn="false"
                        ></chat-record-item>
                    </view>
                </template>
            </chat-scroll-view>
        </view>
        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
    </view>
</template>

<script setup lang="ts">
import { getCreationDetail, getSkillDetail } from '@/api/chat'
import { useAppStore } from '@/stores/app'
import { onLoad, onPullDownRefresh } from '@dcloudio/uni-app'
import { shallowRef, reactive, ref } from 'vue'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const pageOptions = reactive({
    id: 0,
    type: 1,
    token: ''
})
const modelKey = ref('')
const chatRef = shallowRef()
const appStore = useAppStore()

const modelInfo = ref<any>({})
const getChatModel = async () => {
    switch (pageOptions.type) {
        case 2:
            modelInfo.value = await getCreationDetail({
                id: pageOptions.id
            })
            break
        case 3:
            modelInfo.value = await getSkillDetail({
                id: pageOptions.id
            })
    }
    uni.setNavigationBarTitle({
        title: modelInfo.value.name
    })
}

const getData = async () => {
    getChatModel()
}

onLoad((options) => {
    pageOptions.id = Number(options?.id)
    pageOptions.type = Number(options?.type)
    getData()
})

onPullDownRefresh(async () => {
    appStore.getConfig()
    getData()
    uni.stopPullDownRefresh()
})
</script>

<style lang="scss">
page {
    background-color: $-color-white;
    height: 100%;
    overflow: hidden;
}

.chat {
    height: 100%;
    display: flex;
    flex-direction: column;
}
</style>
