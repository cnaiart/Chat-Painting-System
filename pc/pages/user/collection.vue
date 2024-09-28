<template>
    <div>
        <NuxtLayout name="default">
            <ElScrollbar class="bg-white rounded-[12px]">
                <div class="w-full p-[20px]">
                    <div class="text-[18px] font-medium">我的收藏</div>
                    <div class="mt-2" v-if="data.lists.length">
                        <CollectionItem
                            v-for="item in data.lists"
                            :key="item.id"
                            :item="item"
                            @collect="handelCollect(item.id)"
                        />

                        <div class="py-4 flex justify-end">
                            <el-pagination
                                v-model:current-page="params.page_no"
                                :total="data.count"
                                :page-size="params.page_size"
                                hide-on-single-page
                                layout="total, prev, pager, next, jumper"
                                @current-change="refresh()"
                            />
                        </div>
                    </div>
                    <div class="flex flex-1 justify-center items-center" v-else>
                        <el-empty description="暂无收藏" :image-size="250" />
                    </div>
                </div>
            </ElScrollbar>
        </NuxtLayout>
    </div>
</template>
<script setup lang="ts">
import { getcollectLists, cancelCollect } from '@/api/my'
import { ElPagination, ElEmpty } from 'element-plus'
import CollectionItem from './component/collection-item.vue'
const params = reactive({
    page_no: 1,
    page_size: 15
})
const { copy } = useCopy()
const { data, refresh } = await useAsyncData(() => getcollectLists(params))
const handelCollect = async (id: any) => {
    await cancelCollect({ collect_id: id })
    refresh()
}
definePageMeta({
    auth: true,
    layout: false
})
</script>

<style lang="scss" scoped>
.cancelCollection {
    line-height: normal !important;
}
</style>
