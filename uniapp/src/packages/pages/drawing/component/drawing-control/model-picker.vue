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
            class="model-item"
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
                <span class="text-base">{{ item.name }}</span>
                <span class="text-xs">{{ item.desc }}</span>
            </view>
        </view>
    </view>

    <!--    版本选择    -->
    <view class="mt-[20rpx] flex items-center">
        <view class="mr-[10px] text-base font-medium">版本选择</view>
        <drop-down mode="up">
            <template #default>
                <view class="drop-down-input">
                    <text>{{ version_desc || '请选择版本' }}</text>
                    <u-icon name="arrow-down"></u-icon>
                </view>
            </template>
            <template #menu>
                <view class="w-full">
                    <view
                        v-for="(item, key) in appStore?.getDrawConfig
                            ?.mj_version[currentModel]"
                        :key="item"
                        class="py-[14rpx] px-[30rpx]"
                        @click="
                            ;(version_desc = item), emit('update:version', key)
                        "
                    >
                        {{ item }}
                    </view>
                </view>
            </template>
        </drop-down>
    </view>

    <!--    风格选择    -->
    <view v-if="currentModel === 'niji'" class="mt-[20rpx] flex items-center">
        <view class="mr-[10px] text-base">风格选择</view>
        <drop-down mode="up">
            <template #default>
                <view class="drop-down-input">
                    <text>{{ style_desc || '请选择风格' }}</text>
                    <u-icon name="arrow-down"></u-icon>
                </view>
            </template>
            <template #menu>
                <view class="w-full">
                    <view
                        v-for="(item, key) in appStore?.getDrawConfig?.mj_style"
                        :key="item"
                        class="py-[14rpx] px-[30rpx]"
                        @click=";(style_desc = item), emit('update:style', key)"
                    >
                        {{ item }}
                    </view>
                </view>
            </template>
        </drop-down>
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAppStore } from '@/stores/app'
import DropDown from '@/components/drop-down/drop-down.vue'
import config from '@/config'

const appStore = useAppStore()
const emit = defineEmits<{
    (event: 'update:version', value: string): void
    (event: 'update:style', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        version?: any
        style?: any
    }>(),
    {
        version: '',
        style: 'default'
    }
)
const style_desc = ref<string>('动漫')
const version_desc = ref<string>('5.2')

const domain = config.baseUrl
const currentModel = ref<string>('mj')
const modelList: any = [
    {
        name: 'Midjourney',
        value: 'mj',
        desc: '真实感强',
        background: domain + 'resource/image/adminapi/default/draw/mj.png'
    },
    {
        name: 'Niji',
        value: 'niji',
        desc: '卡通动漫',
        background: domain + 'resource/image/adminapi/default/draw/nj.png'
    }
]

const changeVersion = (val: string) => {
    currentModel.value = val
    const version = appStore?.getDrawConfig?.mj_version[val]
    version_desc.value = version[Object.keys(version)[0]]
    emit('update:version', Object.keys(version)[0])
}

changeVersion('mj')
emit('update:style', 'default')
</script>

<style scoped>
.model-item {
    width: 340rpx;
    height: 134rpx;
    padding: 2rpx 4rpx;
    border: 2px solid transparent;
    color: #ffffff;
    border-radius: 12rpx;
}
.model-item:first-child {
    margin-right: 8rpx;
}
.model-item_active {
    border: 2px solid var(--color-primary);
}
.model-item-bg {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-size: 100% 100%;
    width: 100%;
    height: 100%;
}
.model-item-bg span:first-child {
    font-weight: 500;
}

.drop-down-input {
    display: flex;
    justify-content: space-between;
    color: #888888;
    width: 554rpx;
    padding: 10rpx 14rpx;
    border: 1px solid #e5e5e5;
    border-radius: 8rpx;
}
</style>
