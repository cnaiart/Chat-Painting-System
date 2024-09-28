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
        <view class="skills">
            <view class="px-[24rpx] py-[14rpx] bg-white">
                <u-search
                    v-model="keyword"
                    placeholder="请输入关键词搜索"
                    height="72"
                    :show-action="false"
                    @search="getData()"
                />
            </view>
            <view class="main">
                <view class="main__left bg-white">
                    <scroll-view scroll-y="true" class="h-full">
                        <view class="category py-[20rpx] text-content">
                            <view
                                class="category-item"
                                :class="{
                                    'category-item--active':
                                        currentCategory.id == item.id
                                }"
                                v-for="item in data"
                                :key="item.id"
                                @click="changeCategory(item)"
                            >
                                {{ item.name }}
                            </view>
                        </view>
                    </scroll-view>
                </view>
                <view class="main__right flex-1">
                    <scroll-view scroll-y="true" class="h-full">
                        <view class="p-[20rpx]">
                            <view
                                class="skills-list"
                                v-if="currentCategory.skill?.length"
                            >
                                <view
                                    class="skills-list-item"
                                    v-for="item in currentCategory.skill"
                                    :key="item.id"
                                >
                                    <router-navigate
                                        :to="`/packages/pages/chat/chat?type=3&id=${item.id}`"
                                        class="flex items-center"
                                    >
                                        <u-icon :name="item.image" :size="76" />
                                        <view class="flex-1 min-w-0 ml-[20rpx]">
                                            <view class="text-lg">{{
                                                item.name
                                            }}</view>
                                            <view
                                                v-if="item.describe"
                                                class="line-clamp-2 text-xs text-content mt-[10rpx]"
                                            >
                                                {{ item.describe }}
                                            </view>
                                        </view>
                                    </router-navigate>
                                </view>
                            </view>
                            <view v-else class="pt-[300rpx]">
                                <u-empty
                                    text="暂无数据～"
                                    mode="list"
                                ></u-empty>
                            </view>
                        </view>
                    </scroll-view>
                </view>
            </view>
        </view>
        <template #error>
            <u-empty text="加载出错～"></u-empty>
        </template>
    </page-status>
    <tabbar />
</template>

<script setup lang="ts">
import { getSkillLists } from '@/api/chat'
import { PageStatusEnum } from '@/enums/appEnums'
import { useAppStore } from '@/stores/app'
import { onLoad, onPullDownRefresh, onShow } from '@dcloudio/uni-app'
import { ref } from 'vue'
const status = ref(PageStatusEnum.LOADING)
const data = ref<any[]>([])
const currentCategory = ref<any>({})
const keyword = ref('')
const changeCategory = (item: any) => {
    currentCategory.value = item
}

const appStore = useAppStore()

const getData = async () => {
    try {
        data.value = await getSkillLists({
            keyword: keyword.value
        })
        const categoryAll = {
            id: 0,
            name: '全部',
            skill: data.value.reduce((prev, current) => {
                return prev.concat(current.skill)
            }, [])
        }
        currentCategory.value = categoryAll
        data.value.unshift(categoryAll)
        status.value = PageStatusEnum.NORMAL
    } catch (error) {
        status.value = PageStatusEnum.ERROR
    }
}
onLoad(() => {
    getData()
})

onShow(() => {
    appStore.getConfig()
})

onPullDownRefresh(async () => {
    await getData()
    uni.stopPullDownRefresh()
})
</script>

<style lang="scss">
page {
    height: 100%;
    display: flex;
    flex-direction: column;
}
.skills {
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    .main {
        flex: 1;
        min-height: 0;
        display: flex;
        &__left {
            width: 200rpx;
            .category {
                &-item {
                    padding: 25rpx 30rpx;
                    &--active {
                        background-color: $u-bg-color;
                        font-weight: 500;
                        color: $u-main-color;
                    }
                }
            }
        }
        &__right {
            .skills-list {
                &-item {
                    background-color: #fff;
                    padding: 20rpx;
                    border-radius: 14rpx;

                    &:not(:last-child) {
                        margin-bottom: 20rpx;
                    }
                }
            }
        }
    }
}
</style>
