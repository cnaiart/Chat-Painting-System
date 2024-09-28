<template>
    <div class="mt-[15px]">
        <h1 class="text-base font-bold">图片尺寸</h1>
        <div class="flex justify-between flex-wrap">
            <div
                v-for="(item, index) in pictureSize.lists"
                :key="index"
                class="picture-size cursor-pointer text-center hover:text-primary"
                :class="{
                    'picture-size-active': value == item.scaleValue,
                    'picture-size-disable': !item?.scaleValue
                }"
                @click="value = item.scaleValue"
            >
                <div
                    class="flex justify-center items-center mt-[10px] h-[20px]"
                >
                    <div class="rect" :class="item.class" />
                </div>
                <div class="text-base text-[#101010] mt-[4px] size-scale">
                    {{ item.scaleLabel }}
                </div>
                <div class="text-xs text-[#666666] mt-[4px] size-name">
                    {{ item.name }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: '0'
    }
)
const { modelValue: value } = useVModels(props, emit)

const pictureSize = reactive({
    lists: [
        {
            name: '头像图',
            scaleLabel: '1:1',
            scaleValue: '2',
            class: 'w-[20px] h-[20px]'
        },
        {
            name: '媒体配图',
            scaleLabel: '3:4',
            scaleValue: '1',
            class: 'w-[15px] h-[20px]'
        },
        {
            name: '文章配图',
            scaleLabel: '4:3',
            scaleValue: '0',
            class: 'w-[20px] h-[15px]'
        },
        {
            name: '宣传海报',
            scaleLabel: '9:16',
            scaleValue: '4',
            class: 'w-[13px] h-[20px]'
        },
        {
            name: '电脑壁纸',
            scaleLabel: '16:9',
            scaleValue: '3',
            class: 'w-[20px] h-[12px]'
        },
        {
            name: '手机壁纸',
            scaleLabel: '1:2',
            class: 'w-[12px] h-[20px]'
        },
        {
            name: '横版名片',
            scaleLabel: '3:2',
            class: 'w-[20px] h-[14px]'
        },
        {
            name: '小红书图',
            scaleLabel: '2:3',
            class: 'w-[13px] h-[20px]'
        }
    ]
})
</script>

<style lang="scss" scoped>
// 图片尺寸
.picture-size {
    transition: all 0.5s;
    border: 1px solid #dcdfe6;
    user-select: none;
    width: 70px;
    height: 84px;
    margin-top: 10px;
    border-radius: 4px;
    .rect {
        border-radius: 4px;
        border: 2px solid #101010;
    }
}
.picture-size:hover {
    border: 1px solid var(--el-color-primary);
    background-color: var(--el-color-primary-light-9);
    .rect {
        border: 2px solid var(--el-color-primary);
    }
    .size-scale,
    .size-name {
        color: var(--el-color-primary);
    }
}
.picture-size-active {
    border: 1px solid var(--el-color-primary);
    background-color: var(--el-color-primary-light-9);
    .rect {
        border: 2px solid var(--el-color-primary);
    }
    .size-scale,
    .size-name {
        color: var(--el-color-primary);
    }
}
.picture-size-disable {
    filter: opacity(0.5);
    user-select: none;
    pointer-events: none;
    cursor: not-allowed;
}
</style>
