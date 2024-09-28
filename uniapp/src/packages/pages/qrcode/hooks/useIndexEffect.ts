import { onUnmounted, reactive, ref, shallowRef, provide } from 'vue'
import type { QrcodeFormType, PromptParams } from '@/api/qrcode'
import { qrcodeImagine, qrcodeDelete, qrcodeDetail } from '@/api/qrcode'
import { useUserStore } from '@/stores/user'
import usePolling from '@/hooks/usePolling'
import { QrcodeModeEnum } from '../enums/qrcodeEnums'
import { onUnload } from '@dcloudio/uni-app'

export const useIndexEffect = () => {
    const userStore = useUserStore()

    // pageIndex
    const pageIndex = ref<number>(0)
    // 消耗绘画余额条数
    const consumptionCount = ref<number>(0)

    // 其它参数
    const promptParams: PromptParams = reactive({
        v: '3',
        iw: 0.45,
        seed: '',
        shape: 'random',
        ar: '1:1'
    })

    // 绘画数据
    const qrcodeForm: QrcodeFormType = reactive({
        model: 'mewx',
        type: 1,
        qr_content: '',
        qr_image: '',
        prompt: '',
        prompt_params: '',
        model_id: '',
        template_id: '',
        way: 1,
        aspect_ratio: '1:1',
        pixel_style: 'square',
        marker_shape: 'square'
    })

    // 是否生成中
    const isReceiving = ref<boolean>(false)
    // 记录ID（用于请求轮询的ID
    const recordIds: any = ref<number[]>([])

    // 任务列表即索引
    const taskIndex = ref<number>(3)
    const taskLists = [
        { name: '全部', value: -1 },
        { name: '生成完成', value: 3 },
        { name: '进行中', value: 1 },
        { name: '生成失败', value: 2 }
    ]

    // 任务记录
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const paging: any = shallowRef(null)
    const dataList = ref<any>([])

    /**
     *  删除记录
     *  ids: { number[] } : 删除的id数组
     **/
    const deleteDrawing = async (ids: number[]) => {
        try {
            const res = await uni.showModal({
                title: '温馨提示',
                content: '是否确认删除当前记录?',
                confirmColor: '#FFC94D'
            })
            if (res.confirm) {
                await qrcodeDelete({ ids })
                await getRecordLists()
            }
        } catch (error) {
            console.log('删除失败记录', error)
        }
    }

    /**
     *  获取分页列表
     *  **/
    const getRecordLists = async () => {
        paging?.value?.reload()
    }

    /**
     *  获取生成中的ids数组
     *  **/
    const getQrcodeIds = (arr: any[]) => {
        return arr
            .filter((item: any) => item.status === 1 && item.loading)
            .map((item: any) => item.id)
    }

    /**
     *  设置分页数据
     *  pager: { any[] } : 分页列表
     *  getLists: { func } : 获取分页列表函数
     **/
    const setTaskRecordFunc = async (data: any) => {
        end()
        paging.value = data.reload
        dataList.value = data.lists
        recordIds.value = getQrcodeIds(data.lists)
        setTimeout(() => {
            if (recordIds.value.length) {
                start()
            }
        }, 100)
    }

    /**
     *  使用获取详情（轮训请求
     **/
    const useDetails = () => {
        const check = async () => {
            try {
                const data = await qrcodeDetail({
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
                endCallback()
                console.log('获取详情失败=>', error)
            }
        }
        const endCallback = async () => {
            await getRecordLists()
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
    const { start, end } = useDetails()

    onUnmounted(() => {
        end()
    })

    onUnload(() => {
        end()
    })

    /**
     *  绘制请求
     *  options: { drawing } : 绘制参数
     *  options: { isClear } : 是否清空绘制参数
     **/
    const drawingHandler = async (options: {
        params: QrcodeFormType
        isClear: boolean
    }) => {
        if (!userStore.isLogin) {
            return uni.navigateTo({ url: '/pages/login/login' })
        }
        if (options.params.type == 2 && !options.params.qr_image) {
            return uni.$u.toast('请上传二维码！')
        }
        if (options.params.type == 1 && !options.params.qr_content) {
            return uni.$u.toast('请输入二维码内容！')
        }
        if (
            (options.params.way == 1 ||
                QrcodeModeEnum.ZHISHUYUN === options.params.model) &&
            !options.params.prompt
        ) {
            return uni.$u.toast('请输入生成方式！')
        }
        uni.showLoading({ title: '请求中' })

        try {
            end()
            await qrcodeImagine(options.params)
            // 改为生成中
            taskIndex.value = -1
            pageIndex.value = 1
            isReceiving.value = true
            dataList.value.unshift({ status: 1 })
            paging?.value.complete(dataList.value)

            // 重置参数
            if (options.isClear) {
                qrcodeForm.prompt = ''
                qrcodeForm.qr_image = ''
                qrcodeForm.qr_content = ''
            }
            await getRecordLists()
        } catch (error) {
            dataList.value.splice(0, 1)
            paging?.value.complete(dataList.value)
            if (error === '余额不足') {
                const res = await uni.showModal({
                    title: '绘画余额不足',
                    content: '绘画余额不足，请前往充值',
                    confirmText: '前往充值'
                })
                if (res.confirm) {
                    if (userStore.isLogin) {
                        uni.navigateTo({
                            url: '/packages/pages/recharge/recharge'
                        })
                    } else {
                        uni.navigateTo({ url: '/pages/login/login' })
                    }
                }
                return
            }
            console.log('绘制失败=>', error)
            await getRecordLists()
        } finally {
            setTimeout(() => {
                uni.hideLoading()
            }, 1000)
            isReceiving.value = false
        }
    }

    provide('pageIndex', pageIndex)
    provide('consumptionCount', consumptionCount)
    provide('promptParams', promptParams)
    provide('qrcodeForm', qrcodeForm)
    provide('isReceiving', isReceiving)
    provide('taskIndex', taskIndex)
    provide('taskLists', taskLists)
    provide('setTaskRecordFunc', setTaskRecordFunc)
    provide('deleteDrawing', deleteDrawing)
    provide('drawingHandler', drawingHandler)

    return {
        pageIndex,
        consumptionCount,
        qrcodeForm,
        deleteDrawing,
        setTaskRecordFunc,
        useDetails,
        drawingHandler
    }
}
