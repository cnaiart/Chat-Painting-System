<template>
    <u-popup v-model="showNotice" mode="center" border-radius="24">
        <view class="w-[90vw] px-[24rpx]">
            <view class="p-[30rpx] text-lg text-center font-medium">
                {{ bulletinTitle }}
            </view>
            <scroll-view class="max-h-[768rpx]" scroll-y>
                <!-- <u-parse :html="richTextContent"></u-parse> -->
                <mp-html :content="richTextContent" />
            </scroll-view>
            <view class="py-[30rpx] bg-white">
                <u-button
                    type="primary"
                    :style="{
                        height: '82rpx',
                        lineHeight: '82rpx',
                        fontSize: '30rpx',
                        border: 'none',
                        borderRadius: '60px'
                    }"
                    @click="showNotice = false"
                >
                    我知道了
                </u-button>
            </view>
        </view>
    </u-popup>
</template>

<script lang="ts" setup>
import { ref, computed, watch } from 'vue'
import { useAppStore } from '@/stores/app'
import cache from '@/utils/cache'
import { NOTICE } from '@/enums/constantEnums'
import { useUserStore } from '@/stores/user'

const appStore = useAppStore()
const userStore = useUserStore()
const showNotice = ref<boolean>(false)
const richTextContent = computed(
    () => appStore.getBulletinConfig.bulletin_content
)

const bulletinTitle = computed(() => appStore.getBulletinConfig.bulletin_title)
const isBulletin = computed(() => appStore.getBulletinConfig.is_bulletin)

watch(
    () => [userStore.isLogin, isBulletin.value],
    () => {
        if (
            isBulletin.value &&
            shouldShowNotice(isBulletin.value) &&
            userStore.isLogin
        ) {
            showNotice.value = true
        }
    },
    {
        deep: true,
        immediate: false
    }
)

const shouldShowNotice = (value: number) => {
    const lastVisitTime = cache.get(NOTICE)
    const currentTime = new Date().toDateString()
    const isNewDay = !lastVisitTime || lastVisitTime !== currentTime
    if (isNewDay && value && userStore.isLogin) {
        cache.set(NOTICE, currentTime)
    }
    return isNewDay
}
</script>
