<template>
    <div>
        <NuxtLayout name="default">
            <ElScrollbar class="!rounded-[12px] bg-white">
                <div v-if="taskData?.content?.length" class="p-4">
                    <span class="font-bold text-2xl">
                        <span class="mr-[8px]">
                            {{ taskData.title }}
                        </span>
                        <span class="text-[#999] font-normal text-sm">
                            {{ taskData.subTitle }}
                        </span>
                    </span>
                    <div class="grid grid-cols-2 gap-4 mt-[23px]">
                        <div
                            class="bg-[#F8F8F8] p-[20px] flex rounded-md"
                            v-for="(item, index) in taskData.content"
                            :key="index"
                        >
                            <img
                                v-if="item.image"
                                class="w-[50px] h-[50px] mr-[10px]"
                                :src="appStore.getImageUrl(item.image)"
                                alt=""
                            />
                            <div class="flex-1">
                                <div class="font-medium text-xl">
                                    {{ item?.customName || item.name }}
                                </div>
                                <div
                                    class="text-[14px] mt-[8px] flex flex-wrap text-tx-secondary"
                                >
                                    <template v-if="item.type == 1">
                                        每日签到，获得
                                        <span v-if="item.rewards">
                                            <span class="text-error">{{
                                                item.rewards
                                            }}</span
                                            >条对话
                                        </span>
                                        <span v-if="item.draw_rewards">
                                            <span class="text-error">{{
                                                item.draw_rewards
                                            }}</span
                                            >条绘画
                                        </span>
                                    </template>
                                    <template v-if="item.type == 2">
                                        邀请1人，获得
                                        <span v-if="item.rewards">
                                            <span class="text-error">{{
                                                item.rewards
                                            }}</span
                                            >条对话
                                        </span>
                                        <span
                                            v-if="
                                                item.rewards &&
                                                item.draw_rewards
                                            "
                                        >
                                            ，
                                        </span>
                                        <span v-if="item.draw_rewards">
                                            <span class="text-error">{{
                                                item.draw_rewards
                                            }}</span
                                            >条绘画
                                        </span>
                                    </template>
                                    <template v-if="item.type == 3">
                                        分享1次，获得
                                        <span v-if="item.rewards">
                                            <span class="text-error">{{
                                                item.rewards
                                            }}</span
                                            >条对话
                                        </span>
                                        <span
                                            v-if="
                                                item.rewards &&
                                                item.draw_rewards
                                            "
                                        >
                                            ，
                                        </span>
                                        <span v-if="item.draw_rewards">
                                            <span class="text-error">{{
                                                item.draw_rewards
                                            }}</span
                                            >条绘画
                                        </span>
                                    </template>
                                    <template v-if="item.type == 4">
                                        分享1次，获得
                                        <span v-if="item.rewards">
                                            <span class="text-error">{{
                                                item.rewards
                                            }}</span
                                            >条对话
                                        </span>
                                        <span
                                            v-if="
                                                item.rewards &&
                                                item.draw_rewards
                                            "
                                        >
                                            ，
                                        </span>
                                        <span v-if="item.draw_rewards">
                                            <span class="text-error">{{
                                                item.draw_rewards
                                            }}</span
                                            >条绘画
                                        </span>
                                    </template>
                                </div>
                                <div class="text-primary text-[14px] mt-[8px]">
                                    进度：{{ item?.num }}/{{ item?.max }}
                                    {{ item.type == 2 ? '人' : '次' }}
                                </div>
                            </div>
                            <NuxtLink v-if="item.type == 4" to="/app/drawing">
                                <el-button type="primary" class="self-center">
                                    去分享
                                </el-button>
                            </NuxtLink>
                            <el-button
                                v-else
                                type="primary"
                                class="self-center"
                                @click="getShareLink"
                            >
                                点击复制
                            </el-button>
                        </div>
                        <div
                            class="bg-[#F8F8F8] p-[20px] flex rounded-md min-h-[120px] justify-center items-center text-tx-regular"
                        >
                            敬请期待
                        </div>
                    </div>
                </div>
            </ElScrollbar>
        </NuxtLayout>
    </div>
</template>
<script setup lang="ts">
import { ElScrollbar, ElButton } from 'element-plus'
import { getTaskMsg, toShare } from '@/api/recharge'
import { getdecorate } from '~/api'
import { useAppStore } from '@/stores/app'
import { useCopy } from '~/composables/useCopy'

const appStore = useAppStore()
//任务数据
const taskData = ref<any>({
    title: '',
    subTitle: '',
    content: [],
    originalContent: []
})

//获取任务数据
const getTaskData = async () => {
    taskData.value.originalContent = await getTaskMsg()
}

//获取任务装修数据
const getDecorateData = async () => {
    // 将任务数据合并给装修数据中
    const res = await getdecorate({ id: 10 })
    const parseData: any = JSON.parse(res.data)[0].content
    const objMap: any = {}
    const assembly: any = []
    for (const obj of taskData.value.originalContent) {
        delete obj.image
        objMap[obj.type] = obj
    }
    for (const obj of parseData?.data.filter((item: any) => item.show)) {
        if (obj.type in objMap) {
            assembly.push({ ...obj, ...objMap[obj.type] })
        }
    }
    console.log(assembly)
    taskData.value.originalContent = []
    taskData.value.content = assembly
    taskData.value.title = parseData.title
    taskData.value.subTitle = parseData.subTitle
}

const { copy } = useCopy()
//获取分享链接
const getShareLink = async () => {
    const { share_id } = await toShare({ channel: 4 })
    copy(`${window.origin}/?cid=${share_id}&share_id=${share_id}`)
    await getTaskData()
    await getDecorateData()
}

await useAsyncData(() => getTaskData())
await useAsyncData(() => getDecorateData())

definePageMeta({
    auth: true,
    layout: false
})
</script>
<style lang="scss" scoped></style>
