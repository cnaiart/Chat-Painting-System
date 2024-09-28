<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="distribute-order">
        <z-paging
            ref="paging"
            v-model="dataList"
            @query="getList"
            :fixed="true"
            height="100%"
            empty-view-text="暂无分销订单记录～"
            :empty-view-img="EmptyDistributionImage"
            :empty-view-style="{ 'margin-top': '100px' }"
            :empty-view-center="false"
            :auto-clean-list-when-reload="false"
            :auto-scroll-to-top-when-reload="false"
            :empty-view-img-style="{ width: '360rpx', height: '360rpx' }"
        >
            <view
                class="m-[30rpx] bg-white rounded-[14rpx]"
                v-for="item in dataList"
                :key="item"
            >
                <!-- 订单头部 -->
                <view
                    class="flex justify-between items-center order-header px-[20rpx] py-[18rpx]"
                >
                    <view class="flex items-center">
                        <u-avatar size="64" :src="item?.user?.avatar" />
                        <view class="ml-[20rpx] text-base text-content">
                            {{ item?.user?.nickname }}
                        </view>
                    </view>
                    <view class="text-sm text-[#FF2C3C]">
                        {{ item.status_desc }}
                    </view>
                </view>
                <!-- 订单主体 -->
                <view class="order-main p-[20rpx]">
                    <view class="flex justify-between">
                        <view class="font-medium text-lg">
                            {{ item?.order_type_desc }}
                        </view>
                        <view class="text-xs text-muted">
                            {{ item?.create_time }}
                        </view>
                    </view>
                    <view class="flex justify-between mt-[16rpx] text-base">
                        <view>
                            <text class="text-content">获得收益</text>
                            <text class="font-medium text-[#FF2C3C]">
                                ￥{{ item?.income }}
                            </text>
                        </view>
                        <view>
                            <text class="text-content">付款金额</text>
                            <text class="font-medium text-black">
                                ¥{{ item?.order_amount }}
                            </text>
                        </view>
                    </view>
                    <view class="flex justify-between mt-[10rpx] pb-[10rpx]">
                        <text class="text-content text-sm">
                            订单号: {{ item?.order_sn }}
                        </text>
                    </view>
                </view>
            </view>
        </z-paging>
        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
    </view>
</template>
<script setup lang="ts">
import { ref, shallowRef } from 'vue'
import { distributionOrder } from '@/api/promotion'
import EmptyDistributionImage from '@/packages/static/empty/distribute_order.png'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const paging = shallowRef()
const dataList = ref<Record<string, any>>([])

const getList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists } = await distributionOrder({
            page_no: pageNo,
            page_size: pageSize
        })
        paging.value.complete(lists)
    } catch (error) {
        paging.value.complete(false)
        console.log('请求分销订单列表失败', error)
    }
}
</script>

<style lang="scss" scoped>
.distribute-order {
    .order-header {
        border-bottom: 1px solid #f2f2f2;
    }
}
</style>
