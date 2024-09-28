import { reactive, ref, onUnmounted, provide } from 'vue'
import type { DrawingFormType } from '~/api/drawing'
import { drawing, drawingDelete, drawingDetail } from '~/api/drawing'
import { useUserStore } from '~/stores/user'
import usePolling from '~/composables/usePolling'
import { ElNotification } from 'element-plus'

export const useIndexEffect = () => {
    const userStore = useUserStore()

    // 绘画数据
    const drawForm: DrawingFormType = reactive({
        prompt: '',
        action: 'generate',
        image_base: '',
        other: '',
        image_id: '',
        model: '',
        scale: '1:1',
        no_content: '',
        version: '',
        style: 'default',
        engine: '',
        quality: 'standard'
    })
    // 是否生成中
    const isReceiving = ref<boolean>(false)
    // 记录ID（用于请求轮询的ID
    const recordIds: any = ref<number[]>([])

    // 任务列表即索引
    const taskIndex = ref<number>(3)
    const taskLists = [
        { name: '全部', value: -1 },
        { name: '绘画完成', value: 3 },
        { name: '进行中', value: 1 },
        { name: '绘画失败', value: 2 }
    ]

    // 任务记录
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    let getDrawRecordFunc: any = null

    /**
     *  删除记录
     *  ids: { number[] } : 删除的id数组
     **/
    const deleteDrawing = async (ids: number[]) => {
        if (!ids.length) {
            feedback.msgError('请选择需要删除的图片')
            return
        }
        try {
            await feedback.confirm('是否确认删除当前绘画？')
            await drawingDelete({ ids })
            feedback.msgSuccess('删除成功')
            end()
            await getRecordLists()
        } catch (error) {
            console.log('删除失败绘画记录', error)
        }
    }

    /**
     *  获取分页列表
     *  **/
    const getRecordLists = async () => {
        const data = await getDrawRecordFunc()
        recordIds.value = getDrawingIds(data.lists)
        if (recordIds.value.length) {
            start()
        }
    }

    /**
     *  获取生成中的ids数组
     *  **/
    const getDrawingIds = (arr: any[]) => {
        return arr
            .filter(
                (item: any) =>
                    (item.status === 1 || item.status == 0) && item.loading
            )
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
        getDrawRecordFunc = func
        await getRecordLists()
    }

    /**
     *  使用获取详情（轮训请求
     **/
    const useDetails = () => {
        const check = async () => {
            const route = useRoute()
            try {
                const data = await drawingDetail({
                    records_id: recordIds.value
                })
                const res = data.filter((item: any) => {
                    if (item.status == 3 && !route.fullPath.includes('/app/drawing')) {
                        ElNotification({
                            title: '绘画成功',
                            message: '请前往绘画记录查看',
                            type: 'success',
                            duration: 10000
                        })
                    } else if (item.status == 2 && !route.fullPath.includes('/app/drawing')) {
                        ElNotification({
                            title: '绘画失败',
                            message: item.fail_reason,
                            type: 'error',
                            duration: 10000
                        })
                    }
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
            totalTime: 10 * 60 * 1000,
            time: 2000,
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
        // end()
    })

    /**
     *  绘制请求
     *  options: { drawing } : 绘制参数
     *  options: { isClear } : 是否清空绘制参数
     **/
    const drawingHandler = async (options: {
        drawing: DrawingFormType
        isClear: boolean
    }) => {
        if (!userStore.isLogin) return userStore.toggleShowLogin(true)
        if (!options.drawing.prompt)
            return feedback.msgError('请输入绘画描述！')

        // 改为生成中
        taskIndex.value = -1
        isReceiving.value = true

        try {
            end()
            await drawing(options.drawing)
            // 重置参数
            if (options.isClear) {
                drawForm.prompt = ''
                drawForm.image_base = ''
                drawForm.no_content = ''
                drawForm.other = ''
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

    provide('drawForm', drawForm)
    provide('isReceiving', isReceiving)
    provide('taskIndex', taskIndex)
    provide('taskLists', taskLists)
    provide('deleteDrawing', deleteDrawing)
    provide('setTaskRecordFunc', setTaskRecordFunc)
    provide('drawingHandler', drawingHandler)

    return {
        isReceiving,
        taskIndex,
        taskLists,
        deleteDrawing,
        setTaskRecordFunc,
        useDetails,
        drawingHandler
    }
}
