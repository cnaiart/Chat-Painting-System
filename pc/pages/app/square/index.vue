<template>
    <div class="app">
        <NuxtLayout name="app">
            <div class="header flex flex-col items-center">
                <div class="title">绘画广场</div>
                <div class="search w-[600px] mt-[30px]">
                    <el-input
                        v-model="queryParams.keyword"
                        placeholder="请输入描述词搜索"
                        @keydown.enter="
                            () => {
                                resetParams()
                                getDrawSquareData()
                            }
                        "
                    >
                        <template #suffix>
                            <div
                                v-if="queryParams.keyword"
                                class="cursor-pointer"
                                @click.stop="
                                    () => {
                                        queryParams.keyword = ''
                                        getDrawSquareData()
                                    }
                                "
                            >
                                清空
                            </div>
                            <div
                                class="w-[40px] flex justify-center items-center cursor-pointer"
                                @click="
                                    () => {
                                        resetParams()
                                        getDrawSquareData()
                                    }
                                "
                            >
                                <Icon
                                    size="18"
                                    name="el-icon-Search"
                                    color="#828282"
                                ></Icon>
                            </div>
                        </template>
                    </el-input>
                </div>
            </div>
            <div class="nav flex mt-[30px]">
                <div
                    class="text-[18px] text-[#333333] font-medium leading-[40px]"
                >
                    分类：
                </div>
                <div class="category-list flex-1 ml-[10px]">
                    <div
                        v-for="item in cateLists"
                        :key="item.id"
                        class="category-item"
                        :class="{
                            'category-item--active':
                                item.id === queryParams.category_id
                        }"
                        @click="changeCategory(item.id)"
                    >
                        {{ item.name }}
                    </div>
                </div>
            </div>
            <div class="main mt-[10px]">
                <el-empty
                    v-show="!pager.lists.length && !pager.loading"
                    class="mt-[50px]"
                    :image-size="250"
                    :image="EmptyIcon"
                    description="暂时没有绘画哦，快去生成试试吧"
                />
                <div
                    v-infinite-scroll="getDrawSquareData"
                    :infinite-scroll-delay="200"
                    :infinite-scroll-distance="400"
                    :infinite-scroll-disabled="!pager.more"
                >
                    <template v-if="pager.lists.length">
                        <Waterfall
                            ref="waterFull"
                            :delay="100"
                            :list="pager.lists"
                            :width="326"
                            :gutter="20"
                            :animationDelay="0"
                            :animationDuration="0"
                            backgroundColor="none"
                            animationPrefix="none"
                            animated="none"
                            animationEffect="none"
                            :breakpoints="breakpoints"
                        >
                            <template #item="{ item }">
                                <div
                                    class="image-payload h-full w-full relative text-sm"
                                >
                                    <div class="image-bg">
                                        <image-cover
                                            :thumbnail="item.thumbnail"
                                            :image="
                                                item?.image || item?.image_url
                                            "
                                            @refresh="loadImageSuccess"
                                            @on-click="
                                                (val) => (previewLists = val)
                                            "
                                        ></image-cover>
                                    </div>
                                    <!--  点赞  -->
                                    <div
                                        class="image-praise relative"
                                        @click="onPraise(item)"
                                    >
                                        <div
                                            class="praise-animate"
                                            :class="item.is_praise ? 'praise-entry':'praise-leave'"
                                        />
                                    </div>
                                    <!--  海报  -->
                                    <el-tooltip
                                        effect="dark"
                                        content="生成海报"
                                        placement="top"
                                    >
                                        <div
                                            class="image-poster relative"
                                            @click="openPoster(item)"
                                        >
                                            <div
                                                class="text-center leading-[38px]"
                                            >
                                                <!-- <div
                                            class="absolute top-[50%] left-[50%] translate-x-[-50%] translate-y-[-50%]"
                                        > -->
                                                <Icon
                                                    size="16px"
                                                    color="#ffffff"
                                                    name="el-icon-Picture"
                                                />
                                            </div>
                                        </div>
                                    </el-tooltip>
                                    <div class="image-content">
                                        <p
                                            class="text-white line-clamp-2"
                                            @click="
                                                copy(
                                                    item.original_prompts.prompt
                                                )
                                            "
                                        >
                                            {{
                                                item?.prompts_cn ||
                                                item?.original_prompts.prompt
                                            }}
                                        </p>
                                        <div
                                            class="flex justify-between mt-[10px]"
                                        >
                                            <div class="flex items-center">
                                                <template
                                                    v-if="
                                                        appStore
                                                            .getDrawSquareConfig
                                                            .is_show_user
                                                    "
                                                >
                                                    <ElAvatar
                                                        :size="28"
                                                        :src="
                                                            item?.user_info
                                                                ?.image
                                                        "
                                                    />
                                                    <p
                                                        class="text-[#BBBBBB] ml-[6px] w-[80px] truncate"
                                                    >
                                                        {{
                                                            item.user_info.name
                                                        }}
                                                    </p>
                                                </template>
                                            </div>
                                            <div
                                                class="flex items-center"
                                                @click="copy(item.prompts)"
                                            >
                                                <ElImage
                                                    class="w-[16px] h-[16px]"
                                                    :src="CopyIcon"
                                                ></ElImage>
                                                <p class="text-white ml-[6px]">
                                                    复制
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Waterfall>
                        <div
                            v-if="pager.loading"
                            class="flex justify-center items-center mt-[50px]"
                        >
                            <el-icon size="25" class="is-loading"
                                ><Loading
                            /></el-icon>
                            <span class="mt-[4px] ml-[10px] text-[#999999]"
                                >加载中...</span
                            >
                        </div>
                        <div
                            v-if="!pager.more && !pager.loading"
                            class="flex justify-center items-center mt-[50px]"
                        >
                            <span class="mt-[4px] ml-[10px] text-[#999999]">
                                --- 没有更多了 ---
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </NuxtLayout>
        <posterPop ref="popRef"></posterPop>
        <el-image-viewer
            v-if="previewLists.length"
            :url-list="previewLists"
            :hide-on-click-modal="true"
            @close="previewLists = []"
        />
    </div>
</template>
<script setup lang="ts">
import Waterfall from '~/components/waterfall/index.vue'
import CopyIcon from '@/assets/images/icon_copy_white.png'
import EmptyIcon from '@/assets/images/empty_news.png'
import ErrorIcon from '@/assets/images/square_err.png'
import { getDrawSquareLists, getDrawSquareCateLists, drawPraise } from '~/api/square'
import { Loading } from '@element-plus/icons-vue'
import { useCopy } from '~/composables/useCopy'
import { useAppStore } from '~/stores/app'
import posterPop from './_components/posterPop.vue'
import ImageCover from './_components/image.vue'
import { useUserStore } from '~/stores/user'

const { copy } = useCopy()
const appStore = useAppStore()
const userStore = useUserStore()
definePageMeta({
    auth: false,
    layout: false
})

const value = ref(false)

interface QueryParamsType {
    keyword: number | string
    page_no: number
    page_size: number
    category_id: number | string
}

//弹框ref
const popRef = shallowRef()

const waterFull = shallowRef<any>(null)
const queryParams = reactive<QueryParamsType>({
    keyword: '',
    page_no: 0,
    page_size: 20,
    category_id: ''
})
const pager = reactive({
    more: true,
    loading: false,
    lists: []
})
const cateLists = ref<any[]>([{ name: '全部', id: '' }])
const previewLists = ref<any>([])

const breakpoints = {
    4000: { rowPerView: 8 },
    2000: { rowPerView: 6 },
    1800: { rowPerView: 6 },
    1600: { rowPerView: 5 },
    1440: { rowPerView: 5 },
    1360: { rowPerView: 5 },
    1280: { rowPerView: 4 },
    1024: { rowPerView: 3 }
}

const loadImageSuccess = () => {
    waterFull?.value?.renderer()
    // waterFullItem.height = '100%'
}

const resetParams = () => {
    pager.more = true
    queryParams.page_no = 0
}

const changeCategory = (id: number | string) => {
    if (queryParams.category_id === id) return
    queryParams.category_id = id
    resetParams()
    getDrawSquareData()
}

const getCategory = async () => {
    try {
        const data = await getDrawSquareCateLists()
        cateLists.value = [{ name: '全部', id: '' }, ...data]
    } catch (error) {
        console.log('获取分类错误=>', error)
    }
}

const getDrawSquareData = async () => {
    if (pager.loading) return
    if (pager.more) {
        queryParams.page_no += 1
    } else {
        return
    }
    pager.loading = true
    try {
        const data = await getDrawSquareLists(queryParams)
        const { lists, page_no, page_size, count } = data
        if (page_no * page_size > count) {
            pager.more = false
        }
        if (page_no == 1) {
            pager.lists = lists
        } else {
            pager.lists = [...pager.lists, ...lists]
        }
        setTimeout(() => (pager.loading = false), 500)
    } catch (error) {
        pager.loading = false
        console.log('获取绘画广场列表错误=>', error)
    }
}

const onPraise = async (val: any) => {
    if (!userStore.isLogin) {
        userStore.toggleShowLogin(true)
        return
    }
    try {
        await drawPraise({
            id: val.id,
            praise: val.is_praise ? 0 : 1
        })
        // 分类ID是0说明是当前在喜欢页
        if (queryParams.category_id === 0) {
            resetParams()
            getDrawSquareData()
        } else {
            val.is_praise = val.is_praise ? 0 : 1
        }
    } catch (e) {
        console.error(e)
    }
}

//打开海报
const openPoster = (value: any) => {
    if (!userStore.isLogin) {
        userStore.toggleShowLogin(true)
        return
    }
    popRef.value.open(value)
}

getCategory()
getDrawSquareData()
</script>
<style lang="scss" scoped>
.header {
    .title {
        display: inline-block;
        font-size: 30px;
        font-weight: bold;
        background: linear-gradient(
            90deg,
            var(--gradient-1) 0%,
            var(--gradient-2) 100%
        );
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        -moz-background-clip: text;
        -moz-text-fill-color: transparent;
        -o-background-clip: text;
        -o-text-fill-color: transparent;
        background-clip: text;
        text-fill-color: transparent;
    }
    .search {
        :deep(.el-input) {
            .el-input__wrapper {
                padding-left: 20px;
                height: 50px;
                border-radius: 50px;
            }
        }
    }
}
.nav {
    .category-list {
        .category-item {
            display: inline-block !important;
            line-height: 40px;
            border-radius: 30px;
            box-shadow: 0 2px 6px #ebeefd;
            text-align: center;
            height: 40px;
            padding: 0px 24px;
            font-size: 18px;
            font-weight: 500;
            margin-right: 10px;
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
}

.main {
    .image-payload:hover {
        .image-bg {
            //transform: scale(1.1);
        }
        .image-content {
            opacity: 1;
            bottom: 0;
        }
        .image-praise {
            opacity: 1;
        }
        .image-poster {
            opacity: 1;
        }
    }
    .image-payload {
        cursor: pointer;
        overflow: hidden;
        border-radius: 12px;
        .image-bg {
            user-select: none;
            //pointer-events: none;
            //transition: all 1s;
        }
        .image-content {
            transition: all 0.5s;
            opacity: 0;
            position: absolute;
            left: 0;
            right: 0;
            bottom: -10px;
            margin: 10px;
            padding: 10px;
            border-radius: 6px;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .image-poster {
            transition: opacity 0.5s;
            background-color: rgba($color: #000000, $alpha: 0.5);
            position: absolute;
            top: 10px;
            right: 10px;
            width: 32px;
            height: 32px;
            border-radius: 32px;
            opacity: 0;
        }
        .image-praise {
            transition: opacity 0.5s;
            background-color: rgba($color: #000000, $alpha: 0.5);
            position: absolute;
            top: 10px;
            left: 10px;
            width: 32px;
            height: 32px;
            border-radius: 32px;
            opacity: 0;
            .praise-animate {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                width: 54px;
                height: 54px;
                background: url('~/assets/images/praise.png') no-repeat;
                background-position: left;
                background-size: cover;
            }
            // 没点赞
            .praise-leave {
                background-position: left;
            }

            // 点赞
            .praise-entry {
                background-position: right;
                transition: background 1s steps(28);
            }
        }
    }
}
</style>
