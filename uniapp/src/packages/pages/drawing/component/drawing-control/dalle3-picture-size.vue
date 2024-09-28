<template>
    <view class="mt-[40rpx]">
        <view class="text-base font-medium">图片尺寸</view>
        <view class="flex justify-between flex-wrap">
            <view
                v-for="(item, index) in pictureSize.lists"
                :key="index"
                class="picture-size mt-[20rpx] rounded-[8rpx] text-center"
                :class="{
                    'picture-size-active': value == item.scaleValue,
                    'picture-size-disable': !item?.scaleValue
                }"
                @click="value = item.scaleValue"
            >
                <view
                    class="flex justify-center items-center mt-[30rpx] h-[40rpx]"
                >
                    <view class="rect" :style="item.style" />
                </view>
                <view class="text-xl mt-[8rpx] size-scale">
                    {{ item.scaleLabel }}
                </view>
                <view class="text-base mt-[4px] size-name">
                    {{ item.name }}
                </view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { reactive, computed } from 'vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: '1024x1024'
    }
)

const value = computed({
    get: () => {
        return props.modelValue
    },
    set: (val) => {
        emit('update:modelValue', val)
    }
})

const pictureSize = reactive({
    lists: [
        {
            name: '头像图',
            scaleLabel: '1:1',
            scaleValue: '1024x1024',
            style: 'width: 40rpx; height: 40rpx'
        },
        {
            name: '媒体配图',
            scaleLabel: '3:4',
            scaleValue: '1024x1792',
            style: 'width: 30rpx; height: 40rpx'
        },
        {
            name: '文章配图',
            scaleLabel: '4:3',
            scaleValue: '1792x1024',
            style: 'width: 40rpx; height: 30rpx'
        },
        {
            name: '宣传海报',
            scaleLabel: '9:16',
            style: 'width: 26rpx; height: 40rpx'
        },
        {
            name: '电脑壁纸',
            scaleLabel: '16:9',
            style: 'width: 40rpx; height: 24rpx'
        },
        {
            name: '手机壁纸',
            scale: '1:2',
            style: 'width: 22rpx; height: 40rpx'
        },
        {
            name: '横版名片',
            scale: '3:2',
            style: 'width: 40rpx; height: 28rpx'
        },
        {
            name: '小红书图',
            scale: '2:3',
            style: 'width: 26rpx; height: 40rpx'
        }
    ]
})

emit('update:modelValue', '1024x1024')
</script>

<style lang="scss" scoped>
.picture-size {
    transition: all 0.5s;
    user-select: none;
    width: 156rpx;
    height: 196rpx;
    border: 1px solid transparent;
    @apply bg-page;
    .rect {
        border-radius: 4px;
        border: 4rpx solid #101010;
    }
}
.picture-size-active {
    border: 1px solid;
    @apply border-primary bg-primary-light-9;
    .rect {
        border: 4rpx solid;
        @apply border-primary;
    }
    .size-scale,
    .size-name {
        @apply text-primary;
    }
}
.picture-size-disable {
    filter: opacity(0.5);
    user-select: none;
    pointer-events: none;
    cursor: not-allowed;
}
</style>
