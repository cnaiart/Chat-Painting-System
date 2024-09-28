<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="square-container">
        <view class="square-header bg-white">
            <!--  搜索栏  -->
            <view class="py-[14rpx] px-[24rpx]">
                <u-search
                    placeholder="请输入要搜索的关键词"
                    v-model="keyword"
                    :show-action="false"
                ></u-search>
            </view>
            <!--  切换标签  -->
            <u-tabs
                :list="cateState.lists"
                :is-scroll="true"
                :current="cateState.current"
                :active-color="$theme.primaryColor"
                inactive-color="#333333"
                bg-color="transparent"
                @change="handleChange"
            ></u-tabs>
        </view>
        <view class="square-content h-full">
            <swiper
                class="swiper"
                :duration="cateState.isTouch ? 200 : 0"
                :current="cateState.current"
                @animationfinish="swiperAnimationfinish"
            >
                <swiper-item
                    class="swiper-item px-[20rpx] box-border"
                    v-for="(item, index) in cateState.lists"
                    :key="index"
                >
                    <SquareList
                        :index="index"
                        :i="cateState.current"
                        :cid="item.id"
                        :keyword="keyword"
                        @click="handlePic"
                        @show-poster="showPoster"
                    />
                </swiper-item>
            </swiper>
        </view>
        <Preview ref="viewRef" />
        <poster v-if="posterShow" ref="posterRef" @close="posterShow = false" />

        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
        <tabbar />
    </view>
</template>

<script lang="ts" setup>
import { reactive, nextTick, ref, shallowRef } from 'vue'
import { getDrawSquareCateLists } from '@/api/square'
import SquareList from './component/lists.vue'
import Preview from './component/preview.vue'
import poster from './component/poster.vue'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'
import { useUserStore } from '@/stores/user'
import router from '@/router'

const userStore = useUserStore()
const viewRef = shallowRef<any>(null)
//海报弹框ref
const posterRef = shallowRef()
const posterShow = ref(false)

const keyword = ref<string>('')
const cateState = reactive<any>({
    isTouch: true,
    current: 0,
    lists: [{ name: '全部', id: '' }]
})

const handleChange = async (index: number) => {
    cateState.isTouch = false
    await nextTick()
    cateState.current = index
}

const swiperAnimationfinish = (e: any) => {
    cateState.isTouch = true
    cateState.current = e.detail.current
}

const getCategory = async () => {
    try {
        const data = await getDrawSquareCateLists()
        cateState.lists = [{ name: '全部', id: '' }, ...data]
    } catch (error) {
        console.log('获取分类错误=>', error)
    }
}

const handlePic = (row: any) => {
    viewRef.value.open(row)
}

//弹出海报
const showPoster = async (row: any) => {
    if (!userStore.isLogin) {
        router.navigateTo('/pages/login/login')
        return
    }
    posterShow.value = true
    //#ifndef MP
    await nextTick()
    posterRef?.value?.open(row)
    //#endif
    //#ifdef MP
    setTimeout(() => {
        posterRef.value.open(row)
    }, 300)
    //#endif
}

getCategory()
</script>

<style lang="scss">
page {
    height: 100%;
}

.square-container {
    display: flex;
    flex-direction: column;
    flex: 1;
    height: 100%;
    background: linear-gradient(to right, #e8fdf8, #ffffff, #e5f7ff);
    .swiper {
        width: 100%;
        height: 100%;
        &-item {
            width: 100%;
            height: 100%;
        }
    }
}
</style>
