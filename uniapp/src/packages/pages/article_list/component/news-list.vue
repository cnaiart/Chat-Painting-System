<template>
    <z-paging
        auto-show-back-to-top
        :auto="i == index"
        ref="paging"
        v-model="dataList"
        :data-key="i"
        @query="queryList"
        :fixed="false"
        height="100%"
        :auto-clean-list-when-reload="false"
        :auto-scroll-to-top-when-reload="false"
    >
        <block v-for="(newsItem, newsIndex) in dataList" :key="newsIndex">
            <news-card :item="newsItem" :newsId="newsItem.id"></news-card>
        </block>
    </z-paging>
</template>

<script lang="ts" setup>
import { ref, watch, nextTick, shallowRef, unref } from 'vue'
import { getArticleList } from '@/api/news'
import { onLoad, onShow, onUnload } from '@dcloudio/uni-app'
import NewsCard from '@/packages/pages/article_list/component/news-card.vue'

const props = withDefaults(
    defineProps<{
        cid?: number | string
        i: number
        index: number
    }>(),
    {
        cid: 0
    }
)

const paging = shallowRef<any>(null)
const dataList = ref([])
const isFirst = ref<boolean>(true)

watch(
    () => props.index,
    async () => {
        await nextTick()
        if (props.i == props.index && isFirst.value) {
            isFirst.value = false
            paging.value?.reload()
        }
    },
    { immediate: true }
)

const queryList = async (page_no: number, page_size: number) => {
    try {
        const { lists } = await getArticleList({
            cid: props.cid,
            page_no,
            page_size
        })
        paging.value.complete(lists)
    } catch (e) {
        console.log('报错=>', e)
        //TODO handle the exception
        paging.value.complete(false)
    }
}

onShow(() => {
    // 跳转回来刷新次组件数据
    if (props.index == props.i && !unref(isFirst)) {
        paging.value?.reload()
    }
})
</script>

<style scoped></style>
