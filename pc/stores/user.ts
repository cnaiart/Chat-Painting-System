import { getUserCenter, shareInvite, shareClick } from '@/api/user'
import { TOKEN_KEY } from '@/enums/cacheEnums'
import { defineStore } from 'pinia'
import { LoginPopupTypeEnum } from '~/enums/appEnums'

interface UserSate {
    userInfo: Record<string, any>
    token: string | null
    temToken: string | null
    showLogin: boolean
    loginPopupType: LoginPopupTypeEnum
}
export const useUserStore = defineStore({
    id: 'userStore',
    state: (): UserSate => {
        const TOKEN = useCookie(TOKEN_KEY)
        return {
            userInfo: {},
            token: TOKEN.value,
            temToken: null,
            showLogin: false,
            loginPopupType: LoginPopupTypeEnum.LOGIN
        }
    },
    getters: {
        isLogin: (state) => !!state.token
    },
    actions: {
        async getUser() {
            const data = await getUserCenter()
            this.userInfo = data
            this.checkShare()
        },
        //弹起登录二维码
        toggleShowLogin(toggle?: boolean) {
            this.showLogin = toggle ?? !this.showLogin
        },
        setLoginPopupType(type: LoginPopupTypeEnum = LoginPopupTypeEnum.LOGIN) {
            this.loginPopupType = type
        },
        setUser(userInfo) {
            this.userInfo = userInfo
        },
        login(token: string) {
            const oneYear = 360 * 24 * 60 * 60 * 1000
            const TOKEN = useCookie(TOKEN_KEY, {
                expires: new Date(Date.now() + oneYear)
            })

            this.token = token
            TOKEN.value = token
        },
        logout() {
            const TOKEN = useCookie(TOKEN_KEY)
            this.token = null
            this.userInfo = {}
            TOKEN.value = null
        },
        //分享/邀请
        async checkShare() {
            //邀请id
            const cid = useCookie('cid')
            const user_sn = useCookie('user_sn')
            if (cid.value || user_sn.value) {
                const cid = useCookie('cid')
                shareClick({
                    share_id: cid.value
                })
                if (this.isLogin) {
                    await shareInvite({
                        share_id: cid.value,
                        user_sn: user_sn.value
                    })
                }
                cid.value = null
                user_sn.value = null
            }
        }
    }
})
