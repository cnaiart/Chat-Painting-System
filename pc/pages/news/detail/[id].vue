<template>
    <div class="h-full flex flex-col">
        <div class="flex items-center">
            当前位置：
            <el-breadcrumb separator="/">
                <el-breadcrumb-item :to="{ path: '/news' }">
                    资讯中心
                </el-breadcrumb-item>

                <el-breadcrumb-item>文章详情</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="flex-1 min-h-0 mt-4">
            <ElScrollbar>
                <div class="flex gap-4">
                    <div class="flex-1 bg-body rounded-[12px] min-w-0 p-5">
                        <div class="border-b border-br pb-4">
                            <span class="font-medium text-[22px]">
                                {{ newsDetail.title }}
                            </span>
                            <div
                                class="mt-3 text-tx-secondary flex items-center flex-wrap"
                            >
                                <span v-if="newsDetail.author">
                                    {{ newsDetail.author }}&nbsp;|&nbsp;
                                </span>
                                <span class="mr-5">{{
                                    newsDetail.create_time
                                }}</span>
                                <div class="flex items-center">
                                    <Icon name="el-icon-View" />
                                    <span
                                        >&nbsp;{{
                                            newsDetail.click
                                        }}人浏览</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="newsDetail.abstract"
                            class="bg-page mt-4 p-3 rounded-[12px]"
                        >
                            摘要：{{ newsDetail.abstract }}
                        </div>
                        <div class="py-4" v-html="newsDetail.content"></div>
                        <div class="border-t border-br mt-[30px]">
                            <div class="mt-5 flex">
                                <span class="text-tx-regular flex-none"
                                    >上一篇：</span
                                >
                                <template v-if="newsDetail.last?.id">
                                    <NuxtLink
                                        class="hover:underline"
                                        :to="`/news/detail/${newsDetail.last.id}`"
                                    >
                                        {{ newsDetail.last.title }}
                                    </NuxtLink>
                                </template>

                                <span v-else> 暂无相关文章 </span>
                            </div>
                            <div class="mt-5 flex">
                                <span class="text-tx-regular flex-none"
                                    >下一篇：</span
                                >
                                <template v-if="newsDetail.next?.id">
                                    <NuxtLink
                                        class="hover:underline"
                                        :to="`/news/detail/${newsDetail.next.id}`"
                                    >
                                        {{ newsDetail.next.title }}
                                    </NuxtLink>
                                </template>

                                <span v-else> 暂无相关文章 </span>
                            </div>
                        </div>
                    </div>
                    <div class="w-[400px]">
                        <InformationCard
                            class="flex-1"
                            header="相关资讯"
                            :data="newsDetail.new"
                            :only-title="false"
                            image-size="mini"
                            :show-author="false"
                            :show-desc="false"
                            :show-click="false"
                            :border="false"
                            :title-line="2"
                            source="new"
                        />
                    </div>
                </div>
            </ElScrollbar>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { getArticleDetail } from '~~/api/news'
const route = useRoute()
const { data: newsDetail, refresh } = await useAsyncData(
    () =>
        getArticleDetail({
            id: route.params.id,
            source: route.params.source
        }),
    {
        default() {
            return {}
        }
    }
)

definePageMeta({
    layout: 'default',
    auth: true
})
</script>
<style lang="scss" scoped></style>
