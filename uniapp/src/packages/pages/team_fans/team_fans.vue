<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="team-fans">
        <view class="team-fans__header bg-white">
            <u-tabs
                :list="state.tabs"
                :is-scroll="false"
                :current="state.current"
                active-color="#FFC94D"
                @change="handleChange"
            ></u-tabs>
        </view>

        <view class="team-fans__main">
            <z-paging
                ref="paging"
                v-model="state.lists"
                @query="getList"
                :fixed="false"
                height="100%"
                empty-view-text="暂无团队粉丝～"
                :empty-view-img="EmptyTeamFansImage"
                :empty-view-style="{ 'margin-top': '100px' }"
                :empty-view-center="false"
                :auto-clean-list-when-reload="false"
                :auto-scroll-to-top-when-reload="false"
                :empty-view-img-style="{ width: '360rpx', height: '360rpx' }"
            >
                <view
                    class="bg-white rounded-lg m-[20rpx]"
                    v-for="item in state.lists"
                    :key="item.id"
                >
                    <view
                        class="flex justify-between items-center px-[20rpx] py-[18rpx]"
                        style="border-bottom: 1pxs solid #f2f2f2"
                    >
                        <view class="flex items-center">
                            <u-avatar size="64" :src="item?.avatar" />
                            <view class="font-medium text-base ml-[20rpx]">
                                {{ item?.nickname }}
                            </view>
                        </view>
                        <view class="text-muted">{{ item?.create_time }}</view>
                    </view>
                    <view class="p-[20rpx] pb-[30rpx] flex">
                        <view
                            class="flex flex-col justify-center items-center w-1/3"
                        >
                            <view class="text-[36rpx]">{{
                                item?.order_num
                            }}</view>
                            <view class="text-muted mt-[10rpx]">订单量</view>
                        </view>
                        <view
                            class="flex flex-col justify-center items-center w-1/3 middle-line"
                        >
                            <view class="text-[36rpx]">{{
                                item?.total_amount
                            }}</view>
                            <view class="text-muted mt-[10rpx]">累计消费</view>
                        </view>
                        <view
                            class="flex flex-col justify-center items-center w-1/3"
                        >
                            <view class="text-[36rpx]">{{
                                item?.invite_num
                            }}</view>
                            <view class="text-muted mt-[10rpx]">邀请人数</view>
                        </view>
                    </view>
                </view>
            </z-paging>
        </view>
    </view>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>
<script setup lang="ts">
import { reactive, shallowRef } from 'vue'
import { distributionFans } from '@/api/promotion'
import EmptyTeamFansImage from '@/packages/static/empty/team_fans.png'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const paging = shallowRef()
const state = reactive({
    current: 0,
    tabs: [
        {
            name: '全部(0)',
            type: ''
        },
        {
            name: '直接用户(0)',
            type: 1
        },
        {
            name: '间接用户(0)',
            type: 2
        }
    ],
    lists: []
})

const handleChange = (index: number) => {
    state.current = index
    paging.value.reload()
}

const getList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists, extend } = await distributionFans({
            page_no: pageNo,
            page_size: pageSize,
            level: state.tabs[state.current].type
        })
        await handleExtend(extend)
        paging.value.complete(lists)
    } catch (error) {
        paging.value.complete(false)
        console.log('请求分销粉丝列表失败', error)
    }
}

const handleExtend = (row: { all: number; first: number; second: number }) => {
    state.tabs[0].name = '全部(' + row.all + ')'
    state.tabs[1].name = '直接用户(' + row.first + ')'
    state.tabs[2].name = '间接用户(' + row.second + ')'
}
</script>

<style lang="scss" scoped>
.team-fans {
    &__main {
        height: calc(100vh - 40px - env(safe-area-inset-bottom));
        .middle-line {
            border-left: 1px solid #f2f2f2;
            border-right: 1px solid #f2f2f2;
        }
    }
}
</style>
