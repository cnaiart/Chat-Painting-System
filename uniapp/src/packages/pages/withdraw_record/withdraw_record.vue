<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <z-paging
        ref="paging"
        v-model="dataList"
        @query="getList"
        :fixed="true"
        height="100%"
        empty-view-text="暂无提现记录～"
        :empty-view-img="EmptyWithdrawImage"
        :empty-view-style="{ 'margin-top': '100px' }"
        :empty-view-center="false"
        :auto-clean-list-when-reload="false"
        :auto-scroll-to-top-when-reload="false"
        :empty-view-img-style="{ width: '360rpx', height: '360rpx' }"
    >
        <view class="list m-[20rpx] bg-white rounded-[14rpx]">
            <view
                class="list__item p-[20rpx] flex justify-between"
                v-for="item in dataList"
                :key="item.id"
            >
                <view>
                    <!-- <view class="text-lg text-black"
                        >支付宝（{{ item.account }}）</view
                    > -->
                    <view class="text-lg text-black"
                        >提现至{{ item.type_desc }}</view
                    >
                    <view class="text-muted text-xs mt-[10rpx]">{{
                        item.create_time
                    }}</view>
                </view>
                <view>
                    <view class="text-2xl">-{{ item.money }}</view>
                    <view
                        class="mt-[5rpx] text-xs"
                        :class="{
                            'text-[#F99A12]': item.status_desc == '待审核',
                            'text-[#ffba3c]': item.status_desc == '待转账',
                            'text-[#15B112]': item.status_desc == '提现成功',
                            'text-[#FF2C3C]': item.status_desc == '提现失败'
                        }"
                    >
                        {{ item.status_desc }}
                    </view>
                </view>
            </view>
        </view>
    </z-paging>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>
<script setup lang="ts">
import { ref, shallowRef } from 'vue'
import { withdrawList } from '@/api/promotion'
import EmptyWithdrawImage from '@/packages/static/empty/withdraw.png'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const paging = shallowRef()
const dataList = ref<Record<string, any>>([])

const getList = async (pageNo: number, pageSize: number) => {
    try {
        const { lists } = await withdrawList({
            page_no: pageNo,
            page_size: pageSize
        })
        paging.value.complete(lists)
    } catch (error) {
        paging.value.complete(false)
        console.log('请求提现列表失败', error)
    }
}
</script>
<style lang="scss" scoped>
.list {
    &__item {
        border-bottom: 1px solid #e5e5e5;
    }
    &__item:last-child {
        border-bottom: none;
    }
}
</style>
