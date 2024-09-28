<template>
    <NuxtLayout name="default" v-if="!appStore.getDrawConfig.is_open">
        <div
            class="w-full h-full bg-white rounded-[6px] flex items-center justify-center"
        >
            <el-result>
                <template #icon>
                    <el-image class="w-[200px] h-[200px]" :src="DrawingClose" />
                </template>
                <template #title>
                    <div class="text-base text-[#666]">绘画功能未开启</div>
                </template>
            </el-result>
        </div>
    </NuxtLayout>
    <NuxtLayout name="default" v-else>
        <!--    Left 属性创作栏    -->
        <template #panel>
            <div class="drawing-left bg-white w-[350px] h-full py-[20px]">
                <div class="h-full">
                    <DrawingControl> </DrawingControl>
                </div>
            </div>
        </template>
        <div class="h-full pb-[15px]">
            <!--    Content 主体生成内容    -->
            <div class="drawing-main h-full">
                <div class="drawing-main__title">绘画任务</div>

                <div class="p-[10px]" style="height: calc(100% - 180px)">
                    <div class="task-lists flex-1">
                        <div
                            v-for="(item, index) in taskLists"
                            :key="index"
                            class="task-item"
                            :class="{
                                'task-item--active': item.value === taskIndex
                            }"
                            @click="taskIndex = item.value"
                        >
                            {{ item.name }}
                        </div>
                    </div>

                    <div
                        class="h-full"
                        v-loading="isReceiving"
                        element-loading-text="请求生成中"
                    >
                        <DrawingTabs
                            v-for="tabs in taskLists"
                            :key="tabs.value"
                            :status="taskIndex"
                            :lazy="taskIndex == tabs.value"
                            @pagination="setTaskRecordFunc"
                        >
                            <template #item="{ item }">
                                <DrawingResult
                                    :value="item"
                                    :status="item?.status"
                                    :censorStatus="item?.censor_status"
                                />
                            </template>
                        </DrawingTabs>
                    </div>
                </div>
            </div>
        </div>
    </NuxtLayout>
</template>

<script setup lang="ts">
import { useAppStore } from '~/stores/app'
import DrawingControl from './component/drawing-control/index.vue'
import DrawingTabs from './component/drawing-tabs.vue'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import DrawingResult from './component/drawing-result/index.vue'

import { useIndexEffect } from './hooks/useIndexEffect'
import DrawingClose from '~/assets/images/drawing/close.png'

definePageMeta({
    layout: false,
    hiddenLayoutFooter: true
})

const appStore = useAppStore()
appStore.getConfig()
const { taskLists, taskIndex, isReceiving, setTaskRecordFunc } =
    useIndexEffect()
</script>

<style lang="scss" scoped>
.drawing-main {
    border-radius: 12px;
    background: #ffffff;
    &__title {
        font-size: 18px;
        font-weight: 500;
        color: #101010;
        padding: 20px 0;
        margin: 0 20px;
        border-bottom: 1px solid #e5e5ea;
    }
    .task-lists {
        .task-item {
            display: inline-block !important;
            line-height: 30px;
            border-radius: 30px;
            box-shadow: 0 2px 6px #ebeefd;
            text-align: center;
            height: 30px;
            padding: 0px 24px;
            font-size: 14px;
            margin: 10px 0;
            margin-left: 10px;
            cursor: pointer;
            @apply line-clamp-1 text-tx-primary;
            &--active {
                @apply text-btn-text;
                box-shadow: 0 3px 6px #ebeefd;
                background: linear-gradient(
                    87.73deg,
                    var(--gradient-1) 0%,
                    var(--gradient-2) 100%
                );
            }
        }
    }
}
</style>
