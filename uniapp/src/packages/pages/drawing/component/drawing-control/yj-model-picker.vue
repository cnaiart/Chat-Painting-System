<template>
    <view class="flex justify-between mt-[20rpx]">
        <view class="text-base">
            <text class="font-medium"> 模型选择 </text>
        </view>
    </view>

    <!--    模型    -->
    <view class="flex mt-[10rpx]">
        <view
            v-for="item in modelList"
            :key="item.name"
            class="model-item model-item_model"
            :class="{
                'model-item_active': currentModel === item.value
            }"
            hover-class="none"
            @click="changeVersion(item.value)"
        >
            <view
                class="model-item-bg"
                :style="{
                    'background-image': `url(${item.background})`
                }"
            >
                <view class="h-[70rpx] text-center leading-[80rpx]">
                    <!--                    <el-icon-->
                    <!--                        v-if="currentModel === item.value"-->
                    <!--                        color="#ffffff"-->
                    <!--                        size="22"-->
                    <!--                    >-->
                    <!--                        <CircleCheck />-->
                    <!--                    </el-icon>-->
                </view>
                <view
                    class="text-base w-full text-center h-[90rpx] leading-[120rpx]"
                    style="
                        background: linear-gradient(180deg, transparent, #000);
                    "
                >
                    {{ item.name }}
                </view>
            </view>
        </view>
    </view>

    <view class="flex justify-between mt-[20rpx]">
        <view class="text-base">
            <text class="font-medium"> 风格选择 </text>
        </view>
    </view>
    <!--    风格选择    -->
    <view class="mt-[5px]">
        <view
            v-for="(item, index) in data[currentModel]"
            :key="index"
            class="model-item model-item_style !w-[345rpx] !h-[260rpx] inline-block"
            :class="{
                'model-item_active':
                    value.engine === item.engine && value.style === item.value
            }"
            @click="handleStyleSelection(item.engine, item.value)"
        >
            <view
                class="model-item-bg"
                :style="{
                    'background-image': `url(${item.poster})`
                }"
            >
                <view class="h-full flex justify-center items-center">
                    <!--                    <el-icon-->
                    <!--                        v-if="value.engine === item.engine"-->
                    <!--                        color="#ffffff"-->
                    <!--                        size="26"-->
                    <!--                    >-->
                    <!--                        <CircleCheck />-->
                    <!--                    </el-icon>-->
                </view>
                <view
                    class="text-sm w-full h-[140rpx] flex items-center justify-center"
                    style="
                        background: linear-gradient(180deg, transparent, #000);
                    "
                >
                    {{ item.text }}
                </view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import config from '@/config'
import { computed, ref } from 'vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: any): void
}>()
const props = withDefaults(
    defineProps<{
        data: any
        modelValue?: any
    }>(),
    {
        data: {
            cartoon: [],
            common: [],
            reality: []
        },
        modelValue: {
            engine: '',
            style: 'default'
        }
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

const domain = config.baseUrl
const currentModel = ref<string>('common')
const modelList: any = [
    {
        name: '通用模型',
        value: 'common',
        background: domain + 'resource/image/adminapi/default/draw/yj-ty.png'
    },
    {
        name: '动漫模型',
        value: 'cartoon',
        background: domain + 'resource/image/adminapi/default/draw/yj-dm.png'
    },
    {
        name: '现实模型',
        value: 'reality',
        background: domain + 'resource/image/adminapi/default/draw/yj-xs.png'
    }
]

const changeVersion = (val: string) => {
    currentModel.value = val
    const data = props.data[val][0]
    handleStyleSelection(data.engine, data.value)
}

const handleStyleSelection = (engine: string, style: string) => {
    value.value.engine = engine
    value.value.style = style
}

handleStyleSelection('default_dreamer_diffusion', '')
</script>

<style scoped>
.model-item {
    width: 340rpx;
    height: 134rpx;
    padding: 2rpx 4rpx;
    border: 2px solid transparent;
    color: #ffffff;
    overflow: hidden;
    border-radius: 12rpx;
}
.model-item_model:last-child {
    margin-right: 0px;
}
.model-item_style:nth-child(2n) {
    margin-right: 0px;
}
.model-item_active {
    border: 2px solid var(--color-primary);
}
.model-item-bg {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-size: cover;
    width: 100%;
    height: 100%;
    overflow: hidden;
    border-radius: 12rpx;
}
.model-item-bg span:first-child {
    font-weight: 500;
}
</style>
