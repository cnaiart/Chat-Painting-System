<template>
    <div
        class="preview-picture"
        v-loading="loading"
    >
        <!--    单张图片    -->
        <image-contain
            v-if="!Array.isArray(picture)"
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
        <!--    多张图片    -->
        <template v-else>
            <div
                class="image__item h-[140px] relative"
                v-for="(item, index) in picture"
                :key="index"
            >
                <image-contain
                    :src="item"
                    height="140"
                    :initial-index="index"
                    fit="cover"
                    :preview-src-list="picture"
                    :preview-teleported="true"
                    :hide-on-click-modal="true"
                >
                </image-contain>
                <div class="image__item__icon" @click="emit('share', item)">
                    <el-icon color="#ffffff" size="16"><Share /></el-icon>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup lang="ts">
import ImageError from '~/assets/images/drawing/error.png'
import { Share } from '@element-plus/icons-vue'

const emit = defineEmits(['share'])

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
    display: flex;
    flex-wrap: wrap;
    align-content: center;
    justify-content: center;
    .image__item {
        flex-basis: calc(50% - 10px);
        margin: 5px;
        &__icon {
            opacity: 0;
            position: absolute;
            top: 4px;
            right: 4px;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 24px;
            height: 24px;
            border-radius: 4px;
            background-color: rgba(0, 0, 0, 0.6);
        }
    }
    .image__item:hover .image__item__icon {
        opacity: 1;
    }
}
</style>
