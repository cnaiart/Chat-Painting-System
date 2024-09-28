<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="news">
        <!-- 内容 -->
        <tabs
            :current="current"
            @change="handleChange"
            height="80"
            bar-width="60"
            :activeColor="$theme.primaryColor"
            :barStyle="{ bottom: '0' }"
        >
            <tab v-for="(item, i) in tabList" :key="i" :name="item.name">
                <view class="news-list pt-[20rpx]">
                    <news-list
                        :cid="item.id"
                        :i="i"
                        :index="current"
                    ></news-list>
                </view>
            </tab>
        </tabs>
    </view>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>

<script lang="ts" setup>
import { ref, reactive, computed } from 'vue'
import { onLoad, onShow, onReady } from '@dcloudio/uni-app'
import NewsList from './component/news-list.vue'
import { getArticleCate } from '@/api/news'
import Tabs from '@/packages/components/tabs/tabs.vue'
import Tab from '@/packages/components/tab/tab.vue'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const tabList = ref<any>([])
const current = ref<number>(0)

const handleChange = (index: number) => {
    console.log(index)
    current.value = Number(index)
}

const getData = async () => {
    const data = await getArticleCate()
    tabList.value = [{ name: '全部', id: '' }].concat(data)
}

onLoad((options: any) => {
    getData()
    console.log(options)
    uni.setNavigationBarTitle({
        title: options.pageTitle
    })
})
</script>

<style lang="scss">
.news {
    &-search {
        margin-bottom: 2rpx;
    }

    &-list {
        height: calc(100vh - env(safe-area-inset-bottom) - 86rpx);
    }
}
</style>
