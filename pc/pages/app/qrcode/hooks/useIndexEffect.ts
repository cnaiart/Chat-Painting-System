import { reactive, ref, onUnmounted } from 'vue'
import type { QrcodeFormType } from '~/api/qrcode'
import { qrcodeImagine, qrcodeDelete, qrcodeDetail } from '~/api/qrcode'
import { useUserStore } from '~/stores/user'
import usePolling from '~/composables/usePolling'
import { QrcodeModeEnum } from '../enums/qrcodeEnums'

interface PromptParams {
    v: string // 版本取值枚举 2 1.1 1) 示例：--v 2 --v 1.1
    iw: number // (明显程序取值范围 0 - 1, 保留两位小数) 示例： --iw 0.45
    seed: string //  (取值范围1 - 999999999 ) 示例: --seed 123
    shape: string // (码眼选择范围) ["square", "circle", "plus", "box", "octagon", "random", "tiny-plus"], 示例 --shape random ，默认为 random
    ar: string // (尺寸选择) 范围 ["1:1", "9:16", "16:9", "3:4","4:3"] 示例 --ar 1:1 ，默认为 1:1
}

export const useIndexEffect = () => {
    const userStore = useUserStore()

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
        { name: '生成成功', value: 3 },
        { name: '进行中', value: 1 },
        { name: '生成失败', value: 2 }
    ]

    // 任务记录
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    let getQrcodeRecordFunc: any = null

    /**
     *  删除记录
     *  ids: { number[] } : 删除的id数组
     **/
    const deleteQrcode = async (ids: number[]) => {
        if (!ids.length) {
            feedback.msgError('请选择需要删除的记录')
            return
        }
        try {
            await feedback.confirm('是否确认删除当前记录？')
            await qrcodeDelete({ ids })
            feedback.msgSuccess('删除成功')
            await getRecordLists()
        } catch (error) {
            console.log('删除失败', error)
        }
    }

    /**
     *  获取分页列表
     *  **/
    const getRecordLists = async () => {
        const data = await getQrcodeRecordFunc()
        recordIds.value = getQrcodeIds(data.lists)
        if (recordIds.value.length) {
            start()
        }
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
    const setTaskRecordFunc = async (func: any) => {
        console.log('分页列表')
        end()
        await nextTick()
        getQrcodeRecordFunc = func
        await getRecordLists()
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
                const res = data.filter((item) => {
                    return item.status == 3 || item.status == 2 || !item.loading
                })
                if (res.length || !data.length) {
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

    /**
     *  绘制请求
     *  options: { params } : 绘制参数
     *  options: { isClear } : 是否清空绘制参数
     **/
    const drawingHandler = async (options: {
        params: QrcodeFormType
        isClear: boolean
    }) => {
        if (!userStore.isLogin) return userStore.toggleShowLogin(true)
        if (options.params.type == 2 && !options.params.qr_image) {
            return feedback.msgError('请上传二维码！')
        }
        if (options.params.type == 1 && !options.params.qr_content) {
            return feedback.msgError('请输入二维码内容！')
        }
        if (
            QrcodeModeEnum.ZHISHUYUN === options.params.model &&
            !options.params.prompt
        ) {
            return feedback.msgError('请输入生成方式！')
        }

        // 改为生成中
        taskIndex.value = -1
        isReceiving.value = true

        try {
            end()
            await qrcodeImagine(options.params)
            // 重置参数
            if (options.isClear) {
                qrcodeForm.prompt = ''
                qrcodeForm.qr_image = ''
                qrcodeForm.qr_content = ''
            }
            await getRecordLists()
        } catch (error) {
            const router = useRouter()
            if (error === '余额不足') {
                await ElMessageBox.confirm(
                    '绘画余额不足，请前往充值',
                    '绘画余额不足',
                    {
                        confirmButtonText: '前往充值',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }
                )
                router.push('/openvip?type=recharge')
                return
            }
            console.log('绘制失败=>', error)
            await getRecordLists()
        } finally {
            isReceiving.value = false
        }
    }

    provide('consumptionCount', consumptionCount)
    provide('promptParams', promptParams)
    provide('qrcodeForm', qrcodeForm)
    provide('isReceiving', isReceiving)
    provide('taskIndex', taskIndex)
    provide('taskLists', taskLists)
    provide('deleteQrcode', deleteQrcode)
    provide('drawingHandler', drawingHandler)

    return {
        consumptionCount,
        qrcodeForm,
        isReceiving,
        taskIndex,
        taskLists,
        setTaskRecordFunc,
        drawingHandler
    }
}
