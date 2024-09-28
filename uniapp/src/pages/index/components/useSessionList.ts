import {
    chatCategoryAdd,
    chatCategoryClear,
    chatCategoryDelete,
    chatCategoryEdit,
    getChatCategoryLists
} from '@/api/chat'
import { computed, ref } from 'vue'
const sessionActive = ref(0)
const sessionLists = ref<any[]>([])
export function useSessionList() {
    const getSessionLists = async () => {
        try {
            const data = await getChatCategoryLists({ page_type: 0 })
            sessionLists.value = data?.lists || []
            if (sessionActive.value === 0) {
                initSessionActive()
            }
            return sessionLists.value
        } catch (error) {
            sessionLists.value = []
            initSessionActive()
        }
    }

    const sessionAdd = async () => {
        await chatCategoryAdd({})
        await getSessionLists()
        initSessionActive()
    }

    const sessionDelete = async (id: number) => {
        const res = await uni.showModal({
            title: '确定删除该会话？'
        })
        if (res.confirm) {
            await chatCategoryDelete({ id })
            await getSessionLists()
            initSessionActive()
        }
    }

    const sessionClear = async () => {
        const res = await uni.showModal({
            title: '确定清除所有会话？'
        })
        if (res.confirm) {
            await chatCategoryClear()
            await getSessionLists()
            initSessionActive()
        }
    }

    const sessionEdit = async (id: number, name: string) => {
        await chatCategoryEdit({ id, name })
        getSessionLists()
    }

    const initSessionActive = () => {
        sessionActive.value = sessionLists.value[0]?.id || 0
    }

    const currentSession = computed(() => {
        return (
            sessionLists.value.find(
                (session) => session.id === sessionActive.value
            )?.name || ''
        )
    })

    return {
        getSessionLists,
        sessionActive,
        sessionLists,
        sessionAdd,
        sessionEdit,
        sessionDelete,
        sessionClear,
        currentSession
    }
}
