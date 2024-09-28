<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="earnings-detail">
        <view class="earnings-detail__header bg-white">
            <u-tabs
                :list="state.tabs"
                :is-scroll="false"
                :current="state.current"
                active-color="#FFC94D"
                @change="handleChange"
            ></u-tabs>
        </view>

        <view class="earnings-detail__main">
            <z-paging
                ref="paging"
                v-model="state.lists"
                @query="getList"
                :fixed="false"
                height="100%"
                empty-view-text="暂无明细记录～"
                :empty-view-img="EmptyWithdrawImage"
                :empty-view-style="{ 'margin-top': '100px' }"
                :empty-view-center="false"
                :auto-clean-list-when-reload="false"
                :auto-scroll-to-top-when-reload="false"
                :empty-view-img-style="{ width: '360rpx', height: '360rpx' }"
            >
                <view class="list mx-[20rpx] bg-white rounded-[14rpx]">
                    <view
                        class="list__item p-[20rpx] flex justify-between"
                        v-for="item in state.lists"
                        :key="item.id"
                    >
                        <view>
                            <view class="text-lg text-black">
                                {{ item.change_type_desc }}
                            </view>
                            <view class="text-muted text-xs mt-[10rpx]">
                                {{ item.create_time }}
                            </view>
                        </view>
                        <view
                            v-if="item.action == 1"
                            class="text-2xl text-[#FF2C3C]"
                        >
                            +{{ item.change_amount }}
                        </view>
                        <view v-if="item.action == 2" class="text-2xl">
                            -{{ item.change_amount }}
                        </view>
                    </view>
                </view>
            </z-paging>
        </view>
        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
    </view>
</template>
<script lang="ts" setup>
import { reactive, shallowRef } from 'vue'
import { accountLog } from '@/api/user'
import EmptyWithdrawImage from '@/packages/static/empty/withdraw.png'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const paging = shallowRef()
const state = reactive({
    current: 0,
    tabs: [
        {
            name: '全部',
            type: ''
        },
        {
            name: '收入明细',
            type: 1
        },
        {
            name: '支出明细',
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
        const { lists } = await accountLog({
            type: 'money',
            page_no: pageNo,
            page_size: pageSize,
            action: state.tabs[state.current].type
        })
        paging.value.complete(lists)
    } catch (error) {
        paging.value.complete(false)
        console.log('请求佣金列表失败', error)
    }
}
</script>

<style lang="scss" scoped>
.earnings-detail {
    &__header {
        overflow: hidden;
        margin: 20rpx;
        border-radius: 14rpx;
    }

    &__main {
        height: calc(100vh - 40px - env(safe-area-inset-bottom));

        .list {
            &__item {
                border-bottom: 1px solid #e5e5e5;
            }
            &__item:last-child {
                border-bottom: none;
            }
        }
    }
}
</style>
