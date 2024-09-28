import { useClipboard } from '@vueuse/core'
import { ElMessage } from 'element-plus'
export function useCopy() {
    const copy = async (text: string) => {
        const { copy } = useClipboard({ source: text })
        try {
            if (navigator.clipboard) {
                setTimeout(async () => {
                    await copy(text)
                }, 0)
            } else {
                const textarea = document.createElement('textarea')
                textarea.value = text
                document.body.appendChild(textarea)
                textarea.select()
                document.execCommand('copy')
                document.body.removeChild(textarea)
            }
            ElMessage.success({ message: '复制成功' })
        } catch (error) {
            console.log(error)
            ElMessage.error({ message: '复制失败' })
        }
    }
    return {
        copy
    }
}
