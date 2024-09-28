<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <page-status :status="status">
        <view class="task-center">
            <view
                class="bg-primary text-btn-text px-[30rpx] pt-[30rpx] pb-[80rpx] mb-[-50rpx]"
            >
                剩余条数
                <text class="text-[48rpx] font-medium">{{
                    userInfo.balance || 0
                }}</text>
                条
            </view>
            <view class="px-[30rpx] pb-[20rpx]">
                <view class="daily-tasks" v-if="taskData.content.length">
                    <view class="tasks-title">
                        <view class="font-medium text-xl">
                            {{ taskData.title }}
                        </view>
                        <view class="ml-[14rpx] text-muted text-sm">
                            {{ taskData.subTitle }}
                        </view>
                    </view>
                    <view class="tasks-content">
                        <view
                            class="tasks-item p-[20rpx] flex"
                            v-for="(item, index) in taskData.content"
                            :key="index"
                        >
                            <u-icon
                                class="flex-none"
                                :name="getImageUrl(item.image)"
                                :size="120"
                            />
                            <view class="flex-1 min-w-0 ml-[20rpx]">
                                <view class="text-xl font-medium">
                                    {{ item?.customName || item.name }}
                                </view>
                                <view
                                    class="mt-[10rpx] text-xs text-muted text-justify"
                                >
                                    <text v-if="item.type == 1">
                                        每日签到，获得
                                        <template v-if="item.rewards">
                                            <text class="text-error">{{
                                                item.rewards
                                            }}</text
                                            >条对话
                                        </template>
                                        <template
                                            v-if="
                                                item.rewards &&
                                                item.draw_rewards
                                            "
                                        >
                                            ，
                                        </template>
                                        <template v-if="item.draw_rewards">
                                            <text class="text-error">{{
                                                item.draw_rewards
                                            }}</text
                                            >条绘画
                                        </template>
                                    </text>
                                    <text v-if="item.type == 2">
                                        邀请1人，获得
                                        <template v-if="item.rewards">
                                            <text class="text-error">{{
                                                item.rewards
                                            }}</text
                                            >条对话
                                        </template>
                                        <template
                                            v-if="
                                                item.rewards &&
                                                item.draw_rewards
                                            "
                                        >
                                            ，
                                        </template>
                                        <template v-if="item.draw_rewards">
                                            <text class="text-error">{{
                                                item.draw_rewards
                                            }}</text
                                            >条绘画
                                        </template>
                                    </text>
                                    <text v-if="item.type == 3">
                                        分享1次，获得
                                        <template v-if="item.rewards">
                                            <text class="text-error">{{
                                                item.rewards
                                            }}</text
                                            >条对话
                                        </template>
                                        <template
                                            v-if="
                                                item.rewards &&
                                                item.draw_rewards
                                            "
                                        >
                                            ，
                                        </template>
                                        <template v-if="item.draw_rewards">
                                            <text class="text-error">{{
                                                item.draw_rewards
                                            }}</text
                                            >条绘画
                                        </template>
                                    </text>
                                    <text v-if="item.type == 4">
                                        分享1次，获得
                                        <template v-if="item.rewards">
                                            <text class="text-error">{{
                                                item.rewards
                                            }}</text
                                            >条对话
                                        </template>
                                        <template
                                            v-if="
                                                item.rewards &&
                                                item.draw_rewards
                                            "
                                        >
                                            ，
                                        </template>
                                        <template v-if="item.draw_rewards">
                                            <text class="text-error">{{
                                                item.draw_rewards
                                            }}</text
                                            >条绘画
                                        </template>
                                    </text>
                                </view>
                                <view class="text-primary mt-[14rpx]">
                                    进度：{{ item.num }}/{{ item.max }}
                                    {{ item.type == 2 ? '人' : '次' }}
                                </view>
                            </view>
                            <view class="flex-none">
                                <u-button
                                    v-if="item.type === 4"
                                    type="primary"
                                    shape="circle"
                                    size="medium"
                                    :customStyle="{
                                        padding: '0 24rpx',
                                        height: '56rpx'
                                    }"
                                    @click="toSquareShare"
                                >
                                    去分享
                                </u-button>
                                <u-button
                                    v-else
                                    type="primary"
                                    shape="circle"
                                    size="medium"
                                    :customStyle="{
                                        padding: '0 24rpx',
                                        height: '56rpx'
                                    }"
                                    open-type="share"
                                    @click="toShare(item.type)"
                                >
                                    去分享
                                </u-button>
                            </view>
                        </view>
                    </view>
                </view>
            </view>
        </view>
        <template #error>
            <u-empty text="加载出错～"></u-empty>
        </template>
    </page-status>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
    <tabbar />
</template>

<script setup lang="ts">
import { getTask } from '@/api/task'
import { getDecorate } from '@/api/shop'
import { PageStatusEnum } from '@/enums/appEnums'
import { useCopy } from '@/hooks/useCopy'
import { generateSharePath, createShareOptions } from '@/mixins/share'
import { useUserStore } from '@/stores/user'
import { useAppStore } from '@/stores/app'
import { handleClientEvent } from '@/utils/client'
import { onPullDownRefresh, onShow } from '@dcloudio/uni-app'
import { storeToRefs } from 'pinia'
import { ref } from 'vue'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const { getImageUrl } = useAppStore()

const status = ref(PageStatusEnum.LOADING)
const { copy } = useCopy()

const toSquareShare = () => {
    uni.navigateTo({
        url: '/packages/pages/drawing/drawing?type=1'
    })
}

const toShare = (type: number) => {
    handleClientEvent({
        OA_WEIXIN: () => {
            uni.showModal({
                title: '温馨提示',
                content: '点击右上角“...”，分享给好友',
                showCancel: false
            })
        },
        H5: async () => {
            const path = await generateSharePath(true)
            copy(path)
            uni.showModal({
                title: '温馨提示',
                content: '已经复制到剪贴板，请转发给好友',
                showCancel: false
            })
        },
        ANDROID: async () => {
            const appStore = useAppStore()
            const options = await createShareOptions({
                ...appStore.getShareConfig,
                ...appStore.getWebsiteConfig
            })
            const path = await generateSharePath(true)
            uni.share({
                provider: 'weixin',
                scene: 'WXSceneSession',
                type: 0,
                href: path,
                title: options.title,
                imageUrl: options.img_url,
                success: (res) => {
                    console.log('分享成功')
                },
                fail: (err) => {
                    uni.$u.toast(err.errMsg)
                }
            })
        },
        IOS: async () => {
            const appStore = useAppStore()
            const options = await createShareOptions({
                ...appStore.getShareConfig,
                ...appStore.getWebsiteConfig
            })
            const path = await generateSharePath(true)
            uni.share({
                provider: 'weixin',
                scene: 'WXSceneSession',
                type: 0,
                href: path,
                title: options.title,
                imageUrl: options.img_url,
                success: (res) => {
                    console.log('分享成功')
                },
                fail: (err) => {
                    uni.$u.toast(err.errMsg)
                }
            })
        }
    })
}

const taskData = ref<any>({
    title: '',
    subTitle: '',
    content: [],
    originalContent: []
})
const getTaskData = async () => {
    taskData.value.originalContent = await getTask()
}
const getDecorateData = async () => {
    // 将任务数据合并给装修数据中
    const res = await getDecorate({ id: 10 })
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
    taskData.value.originalContent = []
    taskData.value.content = assembly
    taskData.value.title = parseData.title
    taskData.value.subTitle = parseData.subTitle
}

const userStore = useUserStore()
const { userInfo } = storeToRefs(userStore)
const getData = async () => {
    try {
        await getTaskData()
        await getDecorateData()
        status.value = PageStatusEnum.NORMAL
    } catch (error) {
        console.error(error)
        status.value = PageStatusEnum.ERROR
    }
}

onShow(() => {
    getData()
    userStore.getUser()
})

onPullDownRefresh(async () => {
    try {
        await getData()
        await userStore.getUser()
    } catch (error) {}
    uni.stopPullDownRefresh()
})
</script>

<style lang="scss">
.task-center {
    .daily-tasks {
        background: linear-gradient(
                180deg,
                var(--color-primary-light-7) 0%,
                $-color-white 100%
            ),
            #fff;
        background-size: 100% 175rpx;
        background-repeat: no-repeat;
        border-radius: 14rpx;
    }
    .tasks-title {
        padding: 0 20rpx;
        height: 88rpx;
        display: flex;
        align-items: center;
    }
    .tasks-content {
        padding-bottom: 20rpx;
        .tasks-item {
            &:not(:last-of-type) {
                border-bottom: 1px solid $u-border-color;
                margin-bottom: 10rpx;
            }
        }
    }
}
</style>
