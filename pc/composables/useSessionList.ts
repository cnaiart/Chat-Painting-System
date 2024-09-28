import {
    chatCategoryAdd,
    chatCategoryClear,
    chatCategoryDelete,
    chatCategoryEdit,
    getChatCategoryLists
} from '~/api/chat'
import { useUserStore } from '~/stores/user'

export function useSessionList() {
    const sessionActive = useState(() => 0)
    const sessionLists = useState<any[]>(() => [])
    const getSessionLists = async () => {
        const data = await getChatCategoryLists({ page_type: 0 })
        sessionLists.value = data?.lists || []
        if (sessionActive.value === 0) {
            initSessionActive()
        }
        return sessionLists.value
    }

    const userStore = useUserStore()
    const sessionAdd = async () => {
        if (!userStore.isLogin) return userStore.toggleShowLogin(true)
        await chatCategoryAdd({})
        await getSessionLists()
        initSessionActive()
    }

    const sessionDelete = async (id: number) => {
        await feedback.confirm('确定删除该会话？')
        await chatCategoryDelete({ id })
        await getSessionLists()
        initSessionActive()
    }

    const sessionClear = async () => {
        if (!userStore.isLogin) return userStore.toggleShowLogin(true)
        await feedback.confirm('确定清除所有会话？')
        await chatCategoryClear()
        await getSessionLists()
        initSessionActive()
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
