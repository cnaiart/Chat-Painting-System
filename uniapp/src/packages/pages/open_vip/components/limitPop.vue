<template>
    <u-popup v-model="popShow" mode="center" border-radius="14">
        <view class="w-[80vw] p-[40rpx]">
            <view class="pb-[20rpx] text-center text-lg font-medium"
                >限购提示</view
            >
            <view class="text-center mt-2">
                {{ tips || '' }}
            </view>
            <view class="mt-4">
                <u-button
                    type="primary"
                    shape="circle"
                    size="default"
                    :customStyle="{
                        padding: '0 30rpx',
                        height: '82rpx',
                        width: '300rpx'
                    }"
                    @click="popShow = false"
                >
                    确定
                </u-button>
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const popShow = ref(false)
const tips = ref('')

const open = (value: string) => {
    popShow.value = true
    tips.value = value
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
