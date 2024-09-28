<template>
    <NuxtLayout name="default">
        <div class="h-full flex">
            <div
                class="flex-1 min-w-0 bg-white rounded-[12px] p-[16px] flex flex-col"
            >
                <div class="flex items-center mb-[10px]">
                    <div class="text-[18px] font-bold mr-auto">资讯中心</div>
                    <div class="w-60">
                        <el-input
                            v-model="params.keyword"
                            placeholder="请输入关键词搜索"
                        >
                        </el-input>
                    </div>
                </div>
                <div>
                    <el-tabs v-model="params.cid" @tab-change="tabChange">
                        <el-tab-pane label="全部" name=""></el-tab-pane>
                        <el-tab-pane
                            v-for="item in cate"
                            :key="item.id"
                            :label="item.name"
                            :name="item.id"
                        ></el-tab-pane>
                    </el-tabs>
                </div>
                <div class="flex-1 min-h-0">
                    <ElScrollbar>
                        <div class="flex">
                            <div class="flex flex-col flex-1 min-w-0">
                                <div v-loading="pending">
                                    <div
                                        class="bg-white px-5 rounded overflow-hidden"
                                        v-if="data.lists.length"
                                    >
                                        <InformationItems
                                            v-for="item in data.lists"
                                            :key="item.id"
                                            :id="item.id"
                                            :title="item.title"
                                            :desc="item.desc"
                                            :click="item.click"
                                            :author="item.author"
                                            :create-time="item.create_time"
                                            :image="item.image"
                                            :only-title="false"
                                            :border="false"
                                        />
                                        <div class="py-4 flex justify-center">
                                            <el-pagination
                                                v-model:current-page="
                                                    params.page_no
                                                "
                                                :total="data.count"
                                                hide-on-single-page
                                                :page-size="params.page_size"
                                                @current-change="refresh()"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        v-else
                                        class="flex-1 flex justify-center items-center py-[50px]"
                                    >
                                        <el-empty
                                            :image="empty_news"
                                            description="暂无资讯"
                                            :image-size="250"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </ElScrollbar>
                </div>
            </div>
            <div class="w-[400px] ml-4 bg-white rounded-[12px] p-[16px]">
                <div class="h-full flex flex-col">
                    <div class="text-2xl font-medium">热门文章</div>
                    <div class="flex-1 min-h-0">
                        <ElScrollbar>
                            <InformationItems
                                v-for="(item, index) in hotList"
                                :index="index"
                                :key="item.id"
                                :id="item.id"
                                :title="item.title"
                                :desc="item.desc"
                                :click="item.click"
                                :author="item.author"
                                :create-time="item.create_time"
                                :image="item.image"
                                :only-title="true"
                            />
                        </ElScrollbar>
                    </div>
                </div>
            </div>
        </div>
    </NuxtLayout>
</template>
<script lang="ts" setup>
import { watchDebounced } from '@vueuse/core'
import { ElPagination, ElEmpty } from 'element-plus'
import empty_news from '@/assets/images/empty_news.png'
import { getArticleCate, getArticleList, getHotLists } from '~~/api/news'
const route = useRoute()

const params = reactive({
    page_no: 1,
    page_size: 10,
    keyword: '',
    cid: ''
})

const { data: cate } = await useAsyncData(() => getArticleCate())
const { data, refresh, pending } = await useAsyncData(() =>
    getArticleList(params)
)
const { data: hotList } = await useAsyncData(() => getHotLists(), {
    lazy: false
})

const tabChange = () => {
    params.page_no = 1
    refresh()
}

watchDebounced(
    () => params.keyword,
    (value) => {
        tabChange()
    },
    {
        debounce: 500
    }
)
definePageMeta({
    layout: false,
    auth: false
})
</script>
<style lang="scss" scoped></style>
