<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view
        v-if="!appStore.getDrawConfig.is_open"
        class="w-full h-full bg-white rounded-[6px] flex items-center justify-center"
    >
        <u-empty text="绘画功能未开启" mode="favor"></u-empty>
        <tabbar />
    </view>
    <view v-else class="drawing-container">
        <u-tabs
            :list="tabsState.lists"
            :is-scroll="true"
            :current="tabsState.current"
            :active-color="$theme.primaryColor"
            bg-color="transparent"
            :barStyle="{
                background: $theme.primaryColor
            }"
            @change="handleChange"
        ></u-tabs>
        <view class="drawing-content">
            <view class="h-full" v-show="tabsState.current == 0">
                <DrawingControl
                    class="h-full"
                    :formData="formData"
                    @drawing="drawingHandler"
                >
                    <template #model>
                        <ModelPicker v-model="drawModel" />
                    </template>
                </DrawingControl>
            </view>
            <view class="h-full" v-show="tabsState.current == 1">
                <DrawingRecord
                    class="h-full"
                    v-model:current="recordState.current"
                    :formData="drawingResult"
                    :contentList="recordState.lists"
                    @change="choiceRecord"
                    @drawing="drawingHandler"
                    @delete="deleteHandler"
                    @split="splitHandler"
                >
                </DrawingRecord>
            </view>
        </view>
        <tabbar />
    </view>
</template>

<script lang="ts" setup>
import { reactive, ref, onUnmounted } from 'vue'
import type { DrawingFormType } from '@/api/drawing'
import {
    drawing,
    drawingRecord,
    drawingDelete,
    drawingDetail
} from '@/api/drawing'
import { useUserStore } from '@/stores/user'
import { useRouter } from 'uniapp-router-next'
// import { useRouter } from 'uniapp-router-next-zm'
import { useSplit } from '@/pages/drawing/hooks/useSplit'
import usePolling from '@/hooks/usePolling'
import { onHide, onLoad } from '@dcloudio/uni-app'
import { useAppStore } from '@/stores/app'

import DrawingControl from '@/pages/drawing/component/drawing-control/index.vue'
import ModelPicker from '@/pages/drawing/component/drawing-control/model-picker.vue'
import DrawingRecord from '@/pages/drawing/component/drawing-record.vue'

const router = useRouter()
const appStore = useAppStore()
const userStore = useUserStore()

const tabsState = reactive({
    current: 0,
    lists: [
        {
            name: 'AI绘画',
            value: 0
        },
        {
            name: '生成记录',
            value: 0
        }
    ]
})
// 是否切片
const isSplit = ref<boolean>(false)
// 绘画模型
const drawModel = ref<string>('')
// 记录ID（用于请求轮询的ID
const recordIds = ref<number[]>([])
// 记录状态
const recordState = reactive<any>({
    current: 0,
    lists: []
})
// 绘制结果
const drawingResult = reactive<any>({
    actions: [],
    prompt: '',
    prompt_en: '',
    fail_reason: '',
    status: '',
    other: '',
    thumbnail: null,
    loading: 1,
    task_id: '',
    image_id: '',
    image_url: ''
})
// 绘制参数（初始化餐素
const initialData: DrawingFormType = {
    prompt: '',
    action: 'generate',
    image_base: '',
    other: '',
    image_id: '',
    scale: '1:1'
}
// 转为reactive
const formData = reactive<DrawingFormType>({ ...initialData })

const handleChange = (index: number) => {
    tabsState.current = index
}

// 使用
const useDrawingDetails = () => {
    const check = async () => {
        try {
            const data = await drawingDetail({
                records_id: recordIds.value
            })
            const res = data.filter((item: any) => {
                return item.status == 3 || item.status == 2 || !item.loading
            })
            if (res.length) {
                end()
                endCallback()
            }

            return data
        } catch (error: any) {
            end()
            console.log('获取详情失败=>', error)
        }
    }
    const endCallback = async () => {
        await getDrawingRecord()
        recordState.current = 0
        await userStore.getUser()
    }
    const { start, end, result } = usePolling(check, {
        totalTime: 480 * 1000,
        time: 5000,
        count: 96,
        callback: endCallback
    })

    return {
        start,
        end,
        result
    }
}

const { start, end } = useDrawingDetails()

onHide(() => {
    end()
})

onUnmounted(() => {
    end()
})

/**
 *  选择绘图记录
 *  **/
const choiceRecord = (row: any) => {
    Reflect.ownKeys(row).map((item: any) => {
        drawingResult[item] = row[item]
    })
}

/**
 *  绘制记录
 *  **/
const getDrawingRecord = async () => {
    try {
        const lists = await drawingRecord({})
        if (lists.length != 0) {
            Reflect.ownKeys(lists[0]).map((item: any) => {
                drawingResult[item] = lists[0][item]
            })
        }
        recordState.lists = lists
        recordIds.value = getDrawingRecordIds()
        // 如果有一个是正在生成中状态的话我就会重新进入轮询请求结果
        if (recordIds.value.length) {
            tabsState.current = 1
            start()
        }
    } catch (error) {
        console.log('获取绘画记录失败=>', error)
    }
}

/**
 *  获取生成中的ids数组
 *  **/
const getDrawingRecordIds = () => {
    return recordState.lists
        .filter((item: any) => item.status === 1 && item.loading)
        .map((item: any) => item.id)
}

/**
 *  删除绘图
 *  **/
const deleteHandler = async (id: number) => {
    try {
        await drawingDelete({
            ids: [id]
        })
        await getDrawingRecord()
    } catch (error) {
        console.log('删除绘画记录失败=>', error)
    }
}

/**
 *  一键切图
 *  url: { string } : 图片地址
 **/
const splitHandler = async (url: string | string[]) => {
    //#ifndef APP-PLUS
    if (isSplit.value || Array.isArray(drawingResult.image_url)) {
        return
    }
    uni.showLoading({
        title: '切图中'
    })
    isSplit.value = true
    try {
        const { getImages } = useSplit(url, {
            columns: 2,
            rows: 2
        })
        const data: any[] = await getImages()
        drawingResult.image_url = data
        isSplit.value = false
        uni.hideLoading()
    } catch (error) {
        uni.hideLoading()
        isSplit.value = false
        uni.$u.toast('下载图片超时，请重试')
        console.log('一键切图失败=>', error)
    }
    //#endif
    //#ifdef APP-PLUS
    drawingResult.image_url = url
    //#endif
}

/**
 *  绘制请求
 *  options: { drawing } : 绘制参数
 *  options: { isClear } : 是否清空绘制参数
 **/
const drawingHandler = async (options: {
    drawing: DrawingFormType
    isClear: boolean
}) => {
    if (!userStore.isLogin) {
        return router.navigateTo('/pages/login/login')
    }
    if (!options.drawing.prompt) return uni.$u.toast('请输入绘画描述！')

    tabsState.current = 1
    // 添加插入一个空的绘画结果
    recordState.current = 0
    drawingResult.image_url = null
    drawingResult.status = 1
    drawingResult.loading = 1
    drawingResult.thumbnail = null
    recordState.lists.unshift({ ...drawingResult })

    try {
        end()
        const { records_id } = await drawing({
            model: drawModel.value,
            ...options.drawing
        })
        // 重置参数
        if (options.isClear) {
            let scale: string = formData.scale
            Object.assign(formData, initialData)
            formData.scale = scale
            scale = null
        }
        // 防止某种情况下突然的界面显示错误可以删除id
        recordState.lists[0].id = records_id
        recordIds.value.push(records_id)
        start()
    } catch (error) {
        tabsState.current = 0
        drawingResult.status = 0
        recordState.lists.splice(0, 1)
        recordState.current = -1
        if (error === '余额不足') {
            toRecharge()
            return
        }
        console.log('绘制失败=>', error)
        drawingResult.fail_reason = error
        await getDrawingRecord()
    }
}

// 去充值
const toRecharge = async () => {
    const res = await uni.showModal({
        title: '绘画余额不足',
        content: '绘画余额不足，请前往充值',
        confirmText: '前往充值'
    })
    if (res.confirm) {
        if (userStore.isLogin) {
            router.navigateTo('/packages/pages/recharge/recharge')
        } else {
            router.navigateTo('/pages/login/login')
        }
    }
}

onLoad(() => {
    getDrawingRecord()
})
</script>

<style lang="scss">
page {
    height: 100%;
}

.drawing-container {
    display: flex;
    flex-direction: column;
    flex: 1;
    height: 100%;
    @apply bg-white;
    .drawing-content {
        flex: 1;
        min-height: 0;
        border-top: 1px solid;
        @apply border-light;
    }
}
</style>
