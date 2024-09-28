<template>
    <NuxtLayout name="default">
        <div
            class="h-full flex flex-col bg-white rounded-[4px] py-[16px] creation-container"
        >
            <div class="flex px-[16px]">
                <div class="flex-1 min-w-0 flex items-center justify-center">
                    <!-- <div class="swiper-button-prev" @click="slidePrev">
                        <Icon name="el-icon-ArrowLeft" :size="20" />
                    </div> -->
                    <swiper
                        :slidesPerView="'auto'"
                        :spaceBetween="16"
                        class="my-swiper creation-lists"
                        @swiper="onSwiper"
                    >
                        <swiper-slide
                            v-for="(item, index) in categoryList"
                            :key="item.id"
                            style="width: auto"
                        >
                            <div
                                v-if="Object.keys(item).includes('name')"
                                class="creation-item bg-white"
                                :class="{
                                    'creation-item--active':
                                        item.id === queryParams.category_id
                                }"
                                @click="selectCategory(index)"
                            >
                                {{ item.name }}
                            </div>
                        </swiper-slide>
                    </swiper>
                    <!-- <div class="swiper-button-next" @click="slideNext">
                        <Icon name="el-icon-ArrowRight" :size="20" />
                    </div> -->
                </div>

                <div class="w-60 mt-[16px] ml-[16px]">
                    <el-input
                        v-model="queryParams.keyword"
                        placeholder="请输入关键词搜索"
                    >
                    </el-input>
                </div>
            </div>
            <div class="flex-1 min-h-0">
                <ElScrollbar v-if="pager.lists.length">
                    <div>
                        <div
                            class="model-lists flex flex-wrap mt-[10px] px-[6px]"
                        >
                            <div
                                v-for="(item, index) in pager.lists"
                                :key="index"
                                class="model-item p-[5px] sm:p-[10px] 2xl:w-1/6 xl:w-1/4 lg:w-1/3 w-1/3"
                            >
                                <NuxtLink
                                    :to="`/create/produce?id=${item?.id}`"
                                    class="h-full"
                                >
                                    <el-card
                                        class="!border-none h-full rounded-[12px] cardItem"
                                        shadow="never"
                                        style="box-shadow: 0 3px 10px #ebeefd"
                                        :body-style="{
                                            height: '100%',
                                            padding: '20px'
                                        }"
                                    >
                                        <div class="h-full flex flex-col">
                                            <div class="flex">
                                                <div class="flex items-center">
                                                    <img
                                                        class="w-[34px] h-[34px] mr-[10px]"
                                                        :src="item?.image"
                                                        alt=""
                                                    />
                                                    <div
                                                        class="text-lg font-medium line-clamp-1"
                                                    >
                                                        {{ item?.name }}
                                                    </div>
                                                </div>
                                                <div
                                                    class="ml-auto collect"
                                                    @click.prevent="
                                                        Collection(item?.id)
                                                    "
                                                >
                                                    <div class="scale-[0.85]">
                                                        <Icon
                                                            v-if="
                                                                item?.is_collect ==
                                                                false
                                                            "
                                                            :size="24"
                                                            color="#999"
                                                            name="el-icon-Star"
                                                        />
                                                    </div>
                                                    <Icon
                                                        v-if="
                                                            item?.is_collect ==
                                                            true
                                                        "
                                                        :size="24"
                                                        name="el-icon-StarFilled"
                                                        color="#FFB529"
                                                    />
                                                </div>
                                            </div>
                                            <div
                                                v-if="item?.tips"
                                                class="text-xs text-tx-secondary mt-[10px] line-clamp-2 flex-1"
                                            >
                                                {{ item?.tips }}
                                            </div>
                                            <div
                                                class="flex mt-[15px] items-center"
                                            >
                                                <span
                                                    class="text-tx-secondary text-sm mr-auto"
                                                    >🔥
                                                    {{ item.use_num }}人使用过
                                                </span>
                                                <span
                                                    class="arrow-right text-primary flex"
                                                >
                                                    <Icon
                                                        name="el-icon-Right"
                                                        size="20"
                                                    />
                                                </span>
                                            </div>
                                        </div>
                                    </el-card>
                                </NuxtLink>
                            </div>
                        </div>
                    </div>
                </ElScrollbar>
                <div
                    v-else
                    class="flex h-full flex-col justify-center items-center"
                >
                    <emptyCom></emptyCom>
                </div>
            </div>
            <div class="flex justify-end mt-4 px-[16px]">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </div>
    </NuxtLayout>
</template>

<script setup lang="ts">
import { watchDebounced } from '@vueuse/core'
import {
    getCreationCategory,
    creationCollection,
    getCreationListsByCategory
} from '@/api/create'
import emptyCom from '@/components/empty-com/index.vue'
import { Swiper as SwiperInstance } from 'swiper'
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/css'
const queryParams = reactive({
    category_id: 0,
    keyword: ''
})

const { pager, getLists, resetPage } = usePaging({
    size: 30,
    fetchFun: getCreationListsByCategory,
    params: queryParams
})

const { data: categoryList, refresh } = await useAsyncData(
    () => getCreationCategory(),
    {
        default() {
            return []
        }
    }
)
const swiperInstance = shallowRef<SwiperInstance>()
const onSwiper = (swiper: SwiperInstance) => {
    swiperInstance.value = swiper
    console.log(swiper)
}

const slidePrev = () => {
    swiperInstance.value?.slidePrev()
}

const slideNext = () => {
    swiperInstance.value?.slideNext()
}

await getLists()
const selectCategory = async (index: any) => {
    queryParams.category_id = categoryList.value[index]?.id
    await getLists()
}

const Collection = async (id: number) => {
    await creationCollection({ id })
    getLists()
}

definePageMeta({
    layout: false
})
watchDebounced(
    () => queryParams.keyword,
    (value) => {
        resetPage()
    },
    {
        debounce: 500
    }
)
</script>

<style lang="scss" scoped>
.creation-container {
    background: radial-gradient(
        farthest-side at 100% 0,
        var(--el-color-primary-light-9) 0%,
        #fff 40%
    );
}
.swiper-button-prev,
.swiper-button-next {
    position: relative;
    z-index: 99;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 100px;
    cursor: pointer;
    // &.swiper-button-disabled {
    //     opacity: 0;
    // }
}
.creation-lists {
    flex: 1;
    min-width: 0;
    padding: 10px 0;
    .creation-item {
        line-height: 40px;
        border-radius: 8px;
        box-shadow: 0 2px 6px #ebeefd;
        text-align: center;
        height: 40px;
        padding: 0px 24px;
        font-size: 14px;
        cursor: pointer;
        @apply line-clamp-1 text-tx-primary;
        &--active {
            @apply text-btn-text;
            box-shadow: 0 3px 6px #ebeefd;
            background: linear-gradient(
                87.73deg,
                var(--gradient-1) 0%,
                var(--gradient-2) 100%
            );
        }
    }
}
.cardItem {
    .arrow-right {
        opacity: 0;
        transition: all linear 0.2s;
    }
    :hover {
        .arrow-right {
            opacity: 1;
        }
    }
}
</style>
