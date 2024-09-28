<template>
    <div class="task-center" :style="styles">
        <div
            class="bg-[#f7cb64] text-btn-text px-[15px] pt-[15px] pb-[40px] mb-[-25px]"
        >
            剩余条数
            <span class="text-[24px] font-medium">999</span>
            条
        </div>
        <div class="px-[15px] pb-[10px]">
            <div class="daily-tasks">
                <div class="tasks-title">
                    <div class="font-medium text-xl">每日任务</div>
                    <div class="ml-[7px] text-muted text-sm">
                        免费获得条数
                    </div>
                </div>
                <div class="tasks-content">
                    <div
                        class="tasks-item p-[10px] flex"
                        v-for="(item, index) in centerData"
                        :key="index"
                    >
                        <DecorationImg
                            width="60"
                            height="60"
                            :src="item.image"
                        />
                        <div class="flex-1 min-w-0 ml-[10px]">
                            <div class="text-lg font-medium">
                                {{ item?.customName || item.name }}
                            </div>
                            <div
                                class="mt-[3px] text-xs text-muted text-justify"
                            >
                                <span v-if="item.type == 1">
                                    每日签到，获得对话<span class="text-error">1</span>条，绘画<span class="text-error">1</span>条
                                </span>
                                <span v-if="item.type == 2">
                                    邀请1人，获得对话<span class="text-error">1</span>条，绘画<span class="text-error">1</span>条
                                </span>
                                <span v-if="item.type == 3">
                                    分享1次，获得对话<span class="text-error">1</span>条，绘画<span class="text-error">1</span>条
                                </span>
                                <span v-if="item.type == 4">
                                    分享1次，获得对话<span class="text-error">1</span>条，绘画<span class="text-error">1</span>条
                                </span>
                            </div>
                            <div class="text-[#f6c459] text-xs mt-[5px]">
                                <span v-if="item.type == 1">进度：0 / 10天</span>
                                <span v-if="item.type == 2">进度：0 / 10人</span>
                                <span v-if="item.type == 3">进度：0 / 10次</span>
                                <span v-if="item.type == 4">进度：0 / 10次</span>
                            </div>
                        </div>
                        <div class="flex-none">
                            <el-button
                                v-if="item.type == 1"
                                style="--el-color-primary: #f6c459"
                                type="primary"
                                round
                                size="small"
                                class="w-[60px]"
                            >
                                签到
                            </el-button>
                            <el-button
                                v-else
                                style="--el-color-primary: #f6c459"
                                type="primary"
                                round
                                size="small"
                            >
                                去分享
                            </el-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import type options from './options'
import DecorationImg from '../../decoration-img.vue'
type OptionsType = ReturnType<typeof options>
const props = defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({})
    }
})

const centerData = computed(() => props.content.data.filter(item => item.show))
</script>

<style lang="scss" scoped>
.task-center {
    .daily-tasks {
        background: linear-gradient(
                180deg,
                #fcf0d1 0%,
                #ffffff 100px
        ), #fff no-repeat;
        //background-size: 100% 42px;
        border-radius: 7px;
    }
    .tasks-title {
        padding: 0 10px;
        height: 44px;
        display: flex;
        align-items: center;
    }
    .tasks-content {
        padding-bottom: 10px;
        .tasks-item {
            &:not(:last-of-type) {
                border-bottom: 1px solid #e5e5e5;
                margin-bottom: 5px;
            }
        }
    }
}
</style>
