import { ref } from 'vue'

interface Options {
    time?: number
    totalTime?: number
    count?: number
    callback?(): void
}

// @ts-ignore
export default function usePolling(fun: () => Promise<any>, options?: Options) {
    const result = ref(null)
    const error = ref(null)
    const {
        time = 2000,
        totalTime,
        count,
        callback = () => false
    } = options || ({} as Options)
    let timer: any = null
    let endTime: number | null = null
    let totalCount = 0
    const run = () => {
        console.log('count2:', count)
        if (endTime && endTime <= Date.now()) {
            console.log('结束？？')
            end()
            callback()
            return
        }
        if (count && totalCount >= count) {
            console.log('结束？？')
            end()
            callback()
            return
        }
        totalCount++
        timer = setTimeout(() => {
            fun()
                .then((res) => {
                    result.value = res
                    run()
                })
                .catch((err) => {
                    error.value = err
                })
        }, time)
    }

    const start = () => {
        endTime = totalTime ? Date.now() + totalTime : null
        console.log('不是运行了吗？？')
        run()
    }
    const end = () => {
        setTimeout(() => {
            clearTimeout(timer)
            timer = null
            endTime = null
            totalCount = 0
        }, 0)
    }
    return {
        start,
        end,
        error,
        result
    }
}
