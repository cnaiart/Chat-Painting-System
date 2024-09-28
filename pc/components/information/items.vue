<template>
    <NuxtLink :to="`/news/detail/${id}`">
        <div v-if="onlyTitle" class="mt-5 flex items-center">
            <div
                v-if="index !== undefined"
                class="index w-[24px] h-[24px] flex items-center justify-center bg-[#CBCBCB] rounded mr-[10px] text-[#F6F6F6]"
                :class="{
                    '!bg-[#FF3B3B]': index == 0,
                    '!bg-[#FF883B]': index == 1,
                    '!bg-[#FFC94D]': index == 2
                }"
            >
                {{ index + 1 }}
            </div>
            <slot name="title" :title="title">
                <span class="line-clamp-1 flex-1 text-lg">{{ title }}</span>
            </slot>
        </div>
        <div
            v-else
            :class="{
                'border-b border-br pb-4': border,
                'flex pt-5 items-center': !isHorizontal
            }"
        >
            <div class="flex relative">
                <ElImage
                    v-if="image"
                    class="flex-none"
                    :class="{
                        'mr-4': !isHorizontal
                    }"
                    :src="image"
                    fit="cover"
                    :style="getImageStyle"
                />
            </div>

            <div
                class="flex-1"
                :class="{
                    'p-2': isHorizontal
                }"
            >
                <slot name="title" :title="title">
                    <div
                        class="text-lg font-medium"
                        :class="`line-clamp-${titleLine}`"
                    >
                        {{ title }}
                    </div>
                </slot>

                <div
                    v-if="showDesc && desc"
                    class="text-tx-regular line-clamp-2 mt-4"
                >
                    {{ desc }}
                </div>
                <div
                    v-if="showAuthor || showTime || showClick"
                    class="mt-5 text-sm text-tx-secondary flex items-center flex-wrap"
                >
                    <span v-if="showAuthor && author">
                        {{ author }}&nbsp;|&nbsp;
                    </span>
                    <div v-if="showClick" class="flex items-center mr-5">
                        <ElIcon>
                            <View />
                        </ElIcon>
                        <span>&nbsp;{{ click }}人浏览</span>
                    </div>
                    <span v-if="showTime">{{ createTime }}</span>
                </div>
            </div>
        </div>
    </NuxtLink>
</template>
<script lang="ts" setup>
import { ElImage, ElIcon } from 'element-plus'
import { View } from '@element-plus/icons-vue'
const props = defineProps({
    index: {
        type: Number
    },
    id: {
        type: Number
    },
    title: {
        type: String
    },
    desc: {
        type: String
    },
    image: {
        type: String
    },
    author: {
        type: String
    },
    click: {
        type: Number
    },
    createTime: {
        type: String
    },
    onlyTitle: {
        type: Boolean,
        default: true
    },
    isHorizontal: {
        type: Boolean,
        default: false
    },
    titleLine: {
        type: Number,
        default: 1
    },
    border: {
        type: Boolean,
        default: true
    },
    source: {
        type: String,
        default: 'default'
    },
    imageSize: {
        type: String,
        default: 'default'
    },
    showAuthor: {
        type: Boolean,
        default: true
    },
    showDesc: {
        type: Boolean,
        default: true
    },
    showClick: {
        type: Boolean,
        default: true
    },
    showTime: {
        type: Boolean,
        default: true
    },
    showSort: {
        type: Boolean,
        default: true
    }
})

const getImageStyle = computed(() => {
    switch (props.imageSize) {
        case 'default':
            return {
                width: '180px',
                height: '135px'
            }
        case 'mini':
            return {
                width: '120px',
                height: '90px'
            }
        case 'large':
            return {
                width: '260px',
                height: '195px'
            }
    }
})
</script>
<style lang="scss" scoped></style>
