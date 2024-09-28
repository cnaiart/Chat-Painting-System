<template>
    <div
        class="preview-picture"
        v-loading="loading"
    >
        <image-contain
            :src="lazyImg"
            :preview-src-list="[picture]"
            :preview-teleported="true"
            :hide-on-click-modal="true"
            height="300px"
            fit="contain"
            id="preview-picture"
        >
            <template #error>
                <div class="flex justify-center items-center h-full">
                    <img :src="ImageError" alt="加载错误" />
                </div>
            </template>
        </image-contain>
    </div>
</template>

<script setup lang="ts">
import ImageError from '~/assets/images/drawing/error.png'

const props = withDefaults(
    defineProps<{
        loading: boolean
        lazyImg: string | any // 缩略图
        picture?: string | any // 图片
    }>(),
    {
        loading: false,
        lazyImg: '',
        picture: ''
    }
)
</script>

<style lang="scss" scoped>
.preview-picture {
    //flex: 1 1 0;
    max-width: 620px;
    min-width: 150px;
    overflow: hidden;
    margin: 0 auto;
    height: 300px;
}
</style>
