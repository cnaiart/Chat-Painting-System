<template>
    <div class="preview-picture">
        <!--    单张图片    -->
        <u-image
            :src="lazyImg"
            width="640rpx"
            height="640rpx"
            mode="aspectFit"
            border-radius="0"
            :lazy-load="true"
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
    </div>
</template>

<script setup lang="ts">
const emit = defineEmits<{
    (event: 'preview'): void
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
    justify-content: center;
}
</style>
