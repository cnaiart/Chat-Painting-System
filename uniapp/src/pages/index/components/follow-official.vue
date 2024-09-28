<template>
    <view
        class="bg-primary flex px-[20rpx] items-center py-[16rpx]"
        v-if="show && isShow"
    >
        <view class="flex-1 line-clamp-1 min-w-0 text-btn-text">
            {{ title }}
        </view>
        <router-navigate to="/packages/pages/follow_official/follow_official">
            <u-button
                class="flex-none"
                :plain="true"
                type="primary"
                size="medium"
                shape="circle"
                hover-class="none"
                :custom-style="{
                    height: '60rpx'
                }"
            >
                前往关注
            </u-button>
        </router-navigate>

        <view class="text-btn-text ml-[10rpx]" @click.stop="close">
            <u-icon name="close" />
        </view>
    </view>
</template>
<script setup lang="ts">
import { IS_CLOSE_FOLLOW_OA } from '@/enums/constantEnums'
import cache from '@/utils/cache'
import { onBeforeMount } from 'vue'
import { ref } from 'vue'

const props = withDefaults(
    defineProps<{
        title?: string
        show?: boolean
    }>(),
    {
        title: '👉🏻关注公众号，下次访问不迷路',
        show: false
    }
)
const isShow = ref(false)
const close = () => {
    cache.set(IS_CLOSE_FOLLOW_OA, true, 24 * 60 * 60)
    isShow.value = false
}

onBeforeMount(() => {
    isShow.value = !cache.get(IS_CLOSE_FOLLOW_OA)
})
</script>
