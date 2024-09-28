<template>
    <div class="preview-picture">
        <!--    单张图片    -->
        <u-image
            v-if="!Array.isArray(picture)"
            :src="lazyImg"
            width="640rpx"
            height="640rpx"
            mode="aspectFit"
            border-radius="0"
            @click="onPreview([picture], 0)"
        >
            <template #loading>
                <view
                    class="flex flex-col justify-center items-center w-[640rpx] h-[640rpx] bg-[#F7F9FD]"
                >
                    <u-loading
                        mode="circle"
                        :color="$theme.primaryColor"
                        size="40"
                    ></u-loading>
                    <view class="text-primary text-sm mt-[20rpx]">
                        加载中
                    </view>
                </view>
            </template>
        </u-image>
        <!--    多张图片    -->
        <template v-else>
            <view
                class="image__item relative inline-block"
                v-for="(item, index) in picture"
                :key="index"
            >
                <u-image
                    class='inline-block'
                    width="280"
                    mode="widthFix"
                    :src="item"
                    @click="onPreview(picture, index)"
                />
                <view class="image__item__icon" @click="emit('share', item)">
                    <u-icon name="share" color="#ffffff" size="34"></u-icon>
                </view>
            </view>
        </template>
    </div>
</template>

<script setup lang="ts">
const emit = defineEmits<{
    (event: 'preview'): void
    (event: 'share', value: string): void
}>()

const props = withDefaults(
    defineProps<{
        picture: string | string[] // 图片
        lazyImg: string // 缩略图
    }>(),
    {
        picture: '',
        lazyImg: ''
    }
)

const onPreview = (picture: string[], index: number) => {
    uni.previewImage({
        current: index,
        urls: picture
    })
}
</script>

<style lang="scss" scoped>
.preview-picture {
    overflow: hidden;
    margin: 0 auto;
    width: 100%;
    height: 640rpx;
    display: flex;
    flex-wrap: wrap;
    align-content: center;
    // #ifndef MP
    justify-content: center;
    // #endif
    .image__item {
        // #ifdef MP
        margin: 10rpx 16rpx;
        // #endif
        // #ifndef MP
        margin: 10rpx;
        // #endif
        &__icon {
            position: absolute;
            top: 10rpx;
            right: 10rpx;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 64rpx;
            height: 64rpx;
            border-radius: 8rpx;
            background-color: rgba(0, 0, 0, 0.6);
        }
    }
}
</style>
