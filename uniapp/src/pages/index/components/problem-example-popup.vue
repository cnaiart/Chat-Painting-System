<template>
    <u-popup
        v-model="show"
        height="1000rpx"
        mode="bottom"
        safe-area-inset-bottom
        border-radius="14"
        closeable
    >
        <view class="py-[30rpx] h-full flex flex-col">
            <view class="text-2xl font-medium px-[30rpx]">问题示例</view>
            <view>
                <scroll-view :scroll-x="true" class="whitespace-nowrap">
                    <view class="inline-block py-[20rpx]">
                        <view class="flex px-[15rpx]">
                            <view
                                class="mx-[15rpx] leading-[72rpx] px-[40rpx] bg-primary-light-9 text-primary rounded-[8rpx]"
                                :class="{
                                    '!text-btn-text !bg-primary':
                                        currentTab == index
                                }"
                                v-for="(item, index) in data"
                                :key="item.id"
                                @click="currentTab = index"
                            >
                                <view>{{ item.name }}</view>
                            </view>
                        </view>
                    </view>
                </scroll-view>
            </view>

            <view
                class="border-0 border-t border-light border-solid flex-1 min-h-0"
            >
                <scroll-view class="h-full" :scroll-y="true">
                    <view class="px-[30rpx] py-[20rpx]">
                        <template v-if="currentSample.length">
                            <view
                                v-for="item in currentSample"
                                :key="item.id"
                                class="py-[10rpx]"
                            >
                                <view
                                    class="text-center px-[20rpx] bg-page rounded py-[20rpx]"
                                    @click="clickItem(item.content)"
                                >
                                    {{ item.content }}
                                </view>
                            </view>
                        </template>
                        <template v-else>
                            <view class="py-[150rpx]">
                                <u-empty text="暂无数据" mode="data"></u-empty>
                            </view>
                        </template>
                    </view>
                </scroll-view>
            </view>
        </view>
    </u-popup>
</template>
<script setup lang="ts">
import { computed, ref } from 'vue'

const props = defineProps<{
    data: any[]
    modelValue: boolean
}>()
const emit = defineEmits<{
    (event: 'update:modelValue', value: boolean): void
    (event: 'click-item', value: string): void
}>()

const show = computed({
    get: () => {
        return props.modelValue
    },
    set: (value) => {
        emit('update:modelValue', value)
    }
})
const currentTab = ref(0)
const currentSample = computed(() => props.data[currentTab.value]?.sample || [])
const clickItem = (value: string) => {
    show.value = false
    emit('click-item', value)
}
</script>
