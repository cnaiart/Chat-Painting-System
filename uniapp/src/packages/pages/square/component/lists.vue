<template>
    <z-paging
        auto-show-back-to-top
        :auto="i == index"
        ref="paging"
        v-model="dataList"
        :data-key="i"
        :fixed="false"
        height="100%"
        :auto-clean-list-when-reload="true"
        :auto-scroll-to-top-when-reload="false"
        @query="queryList"
    >
        <u-waterfall ref="waterfallRef" v-model="dataList" add-time="50">
            <template v-slot:left="{ leftList }">
                <view
                    v-for="(item, index) in leftList"
                    :key="index"
                    class="mt-[20rpx] mr-[15rpx] relative"
                    @click="onActive(item)"
                >
                    <u-lazy-load
                        threshold="0"
                        border-radius="10"
                        :image="item?.thumbnail || item?.image"
                        :index="index"
                        :errorImg="ErrorIcon"
                        :loadingImg="LoadingIcon"
                    ></u-lazy-load>
                    <view class="enter" v-if="item.id == activeId">
                        <view
                            class="praise top-[16rpx] left-[16rpx]"
                            style="background-color: rgba(0, 0, 0, 0.5)"
                            @click.stop="onPraise(item)"
                        >
                            <view
                                class="praise-animate"
                                :class="item.is_praise ? 'praise-entry':'praise-leave'"
                            >
                            </view>
                        </view>
                        <view
                            class="poster top-[16rpx] right-[16rpx]"
                            style="background-color: rgba(0, 0, 0, 0.5)"
                            @click.stop="$emit('showPoster', item)"
                        >
                            <view>
                                <u-icon
                                    name="photo"
                                    color="#FFFFFF"
                                    size="34rpx"
                                ></u-icon>
                            </view>
                        </view>
                    </view>
                </view>
            </template>
            <template v-slot:right="{ rightList }">
                <view
                    v-for="(item, index) in rightList"
                    :key="index"
                    class="mt-[20rpx] ml-[15rpx] relative"
                    @click="onActive(item)"
                >
                    <u-lazy-load
                        threshold="0"
                        border-radius="10"
                        :image="item?.thumbnail || item?.image"
                        :index="index"
                        :errorImg="ErrorIcon"
                        :loadingImg="LoadingIcon"
                    ></u-lazy-load>
                    <view class="enter" v-if="item.id == activeId">
                        <view
                            class="praise top-[16rpx] left-[16rpx]"
                            style="background-color: rgba(0, 0, 0, 0.5)"
                            @click.stop="onPraise(item)"
                        >
                            <view
                                class="praise-animate"
                                :class="item.is_praise ? 'praise-entry':'praise-leave'"
                            >
                            </view>
                        </view>
                        <view
                            class="poster top-[16rpx] right-[16rpx]"
                            style="background-color: rgba(0, 0, 0, 0.5)"
                            @click.stop="$emit('showPoster', item)"
                        >
                            <view>
                                <u-icon
                                    name="photo"
                                    color="#FFFFFF"
                                    size="34"
                                ></u-icon>
                            </view>
                        </view>
                    </view>
                </view>
            </template>
        </u-waterfall>
    </z-paging>
</template>

<script lang="ts" setup>
import { ref, watch, nextTick, shallowRef } from 'vue'
import { getDrawSquareLists, drawPraise } from '@/api/square'
import { useUserStore } from '@/stores/user'
import ErrorIcon from '@/packages/static/images/square/error.png'
import LoadingIcon from '@/packages/static/images/square/loading.png'
import router from '@/router'

const emit = defineEmits(['click', 'showPoster'])
const props = withDefaults(
    defineProps<{
        cid?: number | string
        i?: number
        index?: number
        keyword: string
    }>(),
    {
        cid: 0,
        keyword: ''
    }
)

const userStore = useUserStore()
const waterfallRef = shallowRef<any>(null)
const paging = shallowRef<any>(null)
const isFirst = ref<boolean>(true)
const dataList = ref<any[]>([])
const activeId = ref<number>(-1)

watch(
    () => props.keyword,
    async () => {
        await nextTick()
        if (props.i != props.index) return
        paging.value?.reload()
    },
    { immediate: false }
)

watch(
    () => props.i,
    async () => {
        if (props.i == props.index && isFirst.value) {
            isFirst.value = false
            await nextTick()
            paging.value?.reload()
        }
    },
    { immediate: true }
)

const queryList = async (page_no: number, page_size: number) => {
    try {
        const { lists } = await getDrawSquareLists({
            category_id: props.cid,
            keyword: props.keyword,
            page_no,
            page_size
        })
        if (props.keyword || page_no == 1) {
            waterfallRef.value?.clear()
        }
        paging.value.complete(lists)
    } catch (e) {
        console.log('报错=>', e)
        //TODO handle the exception
        paging.value.complete(false)
    }
}

const onActive = (item: any) => {
    if (activeId.value == -1 || activeId.value != item.id) {
        activeId.value = item.id
    } else {
        activeId.value = -1
        emit('click', item)
    }
}

const onPraise = async (value: any) => {
    if (!userStore.isLogin) {
        router.navigateTo('/pages/login/login')
        return
    }
    try {
        await drawPraise({
            id: value.id,
            praise: value.is_praise ? 0 : 1
        })
        // 分类ID是0说明是当前在喜欢页
        if (props.cid === 0) {
            paging.value.refresh()
            waterfallRef.value?.remove(value.id)
        } else {
            waterfallRef.value?.modify(value.id, 'is_praise', value.is_praise ? 0 : 1)
        }
    } catch (e) {
        console.error(e)
        uni.$u.toast(JSON.stringify(e))
    }
}
</script>

<style lang="scss" scoped>
.enter {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    animation: animate__fadeIn 0.3s;
}

@keyframes animate__fadeIn {
    0% {
        opacity: 0;
        margin-top: -20rpx;
    }

    100% {
        opacity: 1;
        margin-top: 0rpx;
    }
}

.praise,
.poster {
    position: absolute;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 64rpx;
    height: 64rpx;
    border-radius: 30px;
}

.praise-animate {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 54px;
    height: 54px;
    background: url('@/packages/static/images/praise.png') no-repeat;
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
</style>
