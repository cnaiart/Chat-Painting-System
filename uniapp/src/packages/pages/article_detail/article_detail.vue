<template>
    <view class="bg-white news-detail">
        <!-- 标题信息 -->
        <view class="news-detail-header py-[20rpx] px-[30rpx]">
            <view class="text-3xl font-medium">{{ news.title }}</view>
            <view class="flex mt-[20rpx] text-xs">
                <view class="mr-[40rpx]" v-if="news.author"
                    >作者: {{ news.author }}</view
                >
                <view class="text-muted mr-[40rpx] flex-1">{{
                    news.create_time
                }}</view>
                <view class="flex items-center flex-none text-muted">
                    <image
                        src="/static/images/icon/icon_visit.png"
                        class="w-[30rpx] h-[30rpx]"
                    ></image>
                    <view class="ml-[10rpx]">{{ news.click }}</view>
                </view>
            </view>
        </view>

        <!-- 咨询内容 -->
        <view class="news-detail-section bg-white p-[24rpx]">
            <!-- 摘要 -->
            <view class="summary p-[20rpx] text-base" v-if="news.abstract">
                <text class="font-medium">摘要: </text> {{ news.abstract }}
            </view>
            <!-- 内容 -->
            <view class="mt-[20rpx]">
                <mp-html :content="news.content" />
            </view>
        </view>
    </view>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>

<script lang="ts" setup>
import { reactive, shallowRef } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { getArticleDetail } from '@/api/news'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const news = reactive<any>({
    id: ''
})

const getData = async () => {
    try {
        const data = await getArticleDetail({ id: news.id })
        Reflect.ownKeys(data).map((item: any) => {
            news[item] = data[item]
        })
        uni.setNavigationBarTitle({
            title: news.title
        })
    } catch (error) {
        console.log('文章详情错误', error)
    }
}

onLoad(({ id }: any) => {
    news.id = id
    try {
        if (!news.id) {
            throw new Error('请传入商城咨询ID')
        }
        getData()
    } catch (error) {
        console.log('商品详情报错', error)
    }
})
</script>

<style lang="scss">
.news-detail {
    height: 100%;

    &-header {
        border-bottom: 2rpx solid #f8f8f8;
    }

    &-section {
        .summary {
            border-radius: 12rpx;
            background-color: #f7f7f7;
        }
    }
}
</style>
