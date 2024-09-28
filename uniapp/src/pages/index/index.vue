<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="h-full flex flex-col">
        <view class="flex-1 min-h-0 bg-white">
            <chat-scroll-view
                v-if="showChatView"
                v-model="chatList"
                ref="chatRef"
                :type="1"
                :otherId="sessionActive"
                :currentModel="modelKey"
                :showAdd="true"
                :safeAreaInsetBottom="false"
                :avatar="appStore.getChatConfig.chat_logo"
                bottom="100rpx"
            >
                <template #top>
                    <follow-official
                        :show="!!appStore.config.is_follow_official"
                        :title="appStore.getLoginConfig.involved_text"
                    />
                    <session v-model="showPopup" />
                    <model-picker v-model="modelKey" />
                </template>
                <template #empty>
                    <view class="w-full">
                        <div
                            class="my-[60rpx] text-center text-[50rpx] font-medium"
                        >
                            {{ appStore.getChatConfig.chat_title }}
                        </div>
                        <problem-example
                            v-if="problem.length"
                            :data="problem"
                            @click-item="(value:any) => chatRef.sendLock(value)"
                            @show-more="showExamplePopup = true"
                        />

                        <view
                            v-if="indexData?.tips?.show"
                            class="bg-page flex flex-col items-center p-[30rpx] mt-[20rpx] m-[40rpx]"
                        >
                            <view class="flex items-center">
                                <u-icon name="warning" :size="40" />
                                <view class="text-lg ml-[10rpx] break-all">{{
                                    indexData?.tips?.title
                                }}</view>
                            </view>
                            <view
                                class="text-content text-sm mt-[20rpx] break-all"
                            >
                                {{ indexData?.tips?.sub_title }}
                            </view>
                        </view>
                    </view>
                </template>
            </chat-scroll-view>
            <!--  会话弹窗  -->
            <session-popup v-model="showPopup" />
            <!--  会话弹窗  -->
            <problem-example-popup
                v-model="showExamplePopup"
                :data="problem"
                @click-item="(value:any) => chatRef.sendLock(value)"
            />
            <!--  公告弹窗  -->
            <notice-popup></notice-popup>
            <!--  #ifdef MP  -->
            <!--  微信小程序隐私弹窗  -->
            <MpPrivacyPopup></MpPrivacyPopup>
            <!--  #endif  -->
            <!-- #ifdef APP-PLUS -->
            <!--  苹果App隐私弹窗  -->
            <IosPrivacyPopup
                v-if="getClient() == ClientEnum.IOS"
                @refresh="refreshHandler"
            ></IosPrivacyPopup>
            <!-- #endif -->
        </view>
        <!-- <canvas canvas-id="canvasId" id="canvasId"></canvas> -->
        <tabbar />
    </view>
</template>

<script setup lang="ts">
import { onHide, onPullDownRefresh, onShow } from '@dcloudio/uni-app'
import { reactive, ref, watch } from 'vue'
import { useAppStore } from '@/stores/app'
import { getDecorate } from '@/api/shop'
import { getSamplesLists } from '@/api/chat'

import SessionPopup from './components/session-popup.vue'
import FollowOfficial from './components/follow-official.vue'
import NoticePopup from '@/components/notice-popup/notice-popup.vue'
import ProblemExample from './components/problem-example.vue'
import Session from './components/session.vue'
import ProblemExamplePopup from './components/problem-example-popup.vue'
// #ifdef MP
import MpPrivacyPopup from './components/mp-privacy-popup.vue'
// #endif
// #ifdef APP-PLUS
import { getClient } from '@/utils/client'
import { ClientEnum } from '@/enums/appEnums'
import IosPrivacyPopup from './components/ios-privacy-popup.vue'
// #endif

import { shallowRef } from 'vue'
import { useSessionList } from './components/useSessionList'

const { getSessionLists, sessionActive } = useSessionList()
const showPopup = ref(false)
const showExamplePopup = ref(false)
const problem = ref([])
// 聊天记录列表
const chatList = ref<any[]>([])
const chatRef = shallowRef()
// 首页装修数据
const indexData = reactive({
    tips: {
        show: 0,
        title: '',
        sub_title: ''
    }
})
// 海报实力
const posterRef = shallowRef()
const modelKey = ref('')
const getProblemExample = async () => {
    problem.value = await getSamplesLists()
}

const getDecorateFunc = async () => {
    try {
        const { data } = await getDecorate({ id: 8 })
        indexData.tips = JSON.parse(data)[1]?.content
    } catch (error) {
        console.log('获取装修数据失败=>', error)
    }
}

const appStore = useAppStore()
const showChatView = ref(true)

const refreshHandler = async () => {
    await appStore.getConfig()
    await getDecorateFunc()
    await getSessionLists()
    await getProblemExample()
}

onHide(() => {
    showChatView.value = false
})

onShow(async () => {
    getDecorateFunc()
    getSessionLists()
    getProblemExample()
    showChatView.value = true
})
onPullDownRefresh(async () => {
    refreshHandler()
    uni.stopPullDownRefresh()
})
</script>

<style>
page {
    height: 100%;
    overflow: hidden;
}
</style>
