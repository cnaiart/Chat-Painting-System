import { Ref } from 'nuxt/dist/app/compat/capi'
import usePolling from '~/composables/usePolling'
import { useUserStore } from '~/stores/user'
import { checkTicket, getWxCode } from '~/api/account'
import { LoginPopupTypeEnum } from '~/enums/appEnums'
import { useAppStore } from '~/stores/app'

//user仓库
const userStore = useUserStore()
const appStore = useAppStore()

export const useGetCode = () => {
    const state = useState<{
        pending: boolean
        data: any
    }>(() => ({
        pending: true,
        data: {}
    }))

    const getData = async () => {
        try {
            state.value.pending = true
            const data = await getWxCode()
            state.value.data = data
            state.value.pending = false
        } catch (error: any) {
            state.value.pending = false
            return Promise.reject(error)
        }
    }

    return {
        state,
        getData
    }
}

export enum WxLoginStatus {
    CODE_ERROR = -1,
    INVALID = 0,
    NORMAL = 1,
    SCANNED_CODE = 2,
    LOGIN_FAIL = 3,
    LOGIN_SUCCESS = 4
}

export const useCheckTicket = (ticket: Ref<any>) => {
    const status = useState(() => WxLoginStatus.NORMAL)
    const check = async () => {
        try {
            const data = await checkTicket({
                key: ticket.value.key
            })
            status.value = data.status
            if (status.value == WxLoginStatus.LOGIN_SUCCESS) {
                loginSuccess(data.user)

                end()
            }
            return data
        } catch (error: any) {
            status.value = WxLoginStatus.LOGIN_FAIL
        }
    }
    const endCallback = () => {
        status.value = WxLoginStatus.INVALID
    }
    const { start, end, result } = usePolling(check, {
        totalTime: 120 * 1000,
        callback: endCallback
    })
    return {
        status,
        start,
        end,
        result
    }
}

const loginSuccess = async (user: any) => {
    const { login, setUser, toggleShowLogin, setLoginPopupType } = userStore
    if (!user.mobile && appStore.getLoginConfig.coerce_mobile) {
        userStore.temToken = user.token
        setLoginPopupType(LoginPopupTypeEnum.BIND_MOBILE)
    } else {
        login(user.token)
        setUser(user)
        toggleShowLogin(false)
        location.reload()
    }
}
