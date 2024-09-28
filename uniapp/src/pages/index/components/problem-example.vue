<template>
    <view class="problem-example px-[10rpx]">
        <view class="flex">
            <view
                v-for="item in sliceData"
                :key="item.id"
                class="flex-1 mx-[10rpx]"
            >
                <view
                    class="flex flex-col justify-center items-center mb-[20rpx]"
                >
                    <image
                        v-if="item.image"
                        class="w-[88rpx] h-[88rpx]"
                        :src="item.image"
                        alt=""
                    />
                    <view
                        class="text-[30rpx] font-medium mt-[32rpx] line-clamp-1"
                    >
                        {{ item.name }}
                    </view>
                </view>
                <view>
                    <view
                        v-for="sample in slice3InData(item.sample)"
                        :key="sample.id"
                        class="bg-page mb-[20rpx] px-[20rpx] py-[15rpx] flex justify-center rounded-[12rpx] cursor-pointer"
                        @click="clickItem(sample.content)"
                    >
                        <view class="line-clamp-2 text-sm text-center">
                            {{ sample.content }}
                        </view>
                    </view>
                </view>
            </view>
        </view>
        <view class="py-[10rpx]" @click="emit('show-more')">
            <view class="text-sm text-muted flex items-center justify-center">
                查看更多
                <u-icon name="arrow-right" size="26" />
            </view>
        </view>
    </view>
</template>
<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    data: any[]
}>()
const emit = defineEmits<{
    (event: 'click-item', value: string): void
    (event: 'show-more'): void
}>()

const slice3InData = (data: any[]) => {
    return data.slice(0, 3)
}
const sliceData = computed(() => {
    return slice3InData(props.data)
})
const clickItem = (value: string) => {
    emit('click-item', value)
}
</script>
