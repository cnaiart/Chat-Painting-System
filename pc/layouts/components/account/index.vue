<template>
    <ClientOnly>
        <ElDialog
            v-model="showLogin"
            width="auto"
            custom-class="login-popup"
            append-to-body
            style="border-radius: 16px; overflow: hidden"
        >
            <div class="flex">
                <div v-if="appStore.config.pc_login_image">
                    <ElImage
                        class="w-[320px] h-full"
                        fit="cover"
                        :src="appStore.config.pc_login_image"
                    />
                </div>
                <div class="flex-1 text-tx-primary flex flex-col w-[400px]">
                    <Login
                        v-if="
                            userStore.loginPopupType == LoginPopupTypeEnum.LOGIN
                        "
                    />
                    <Register
                        v-if="
                            userStore.loginPopupType ==
                            LoginPopupTypeEnum.REGISTER
                        "
                    />
                    <ForgotPwd
                        v-if="
                            userStore.loginPopupType ==
                                LoginPopupTypeEnum.FORGOT_PWD_MAILBOX ||
                            userStore.loginPopupType ==
                                LoginPopupTypeEnum.FORGOT_PWD_MOBILE
                        "
                    />
                    <BindMobile
                        v-if="
                            userStore.loginPopupType ==
                            LoginPopupTypeEnum.BIND_MOBILE
                        "
                    />
                </div>
            </div>
        </ElDialog>
    </ClientOnly>
</template>
<script lang="ts" setup>
import { ElDialog, ElImage } from 'element-plus'
import Login from './login/index.vue'
import Register from './register/index.vue'
import ForgotPwd from './forgot-pwd.vue'
import BindMobile from './bind-mobile.vue'
import { useUserStore } from '~~/stores/user'
import { LoginPopupTypeEnum } from '~/enums/appEnums'
import { useAppStore } from '~/stores/app'

const userStore = useUserStore()
const appStore = useAppStore()
const showLogin = computed({
    get() {
        return userStore.showLogin
    },
    set(value) {
        userStore.showLogin = value
    }
})
watch(
    () => userStore.showLogin,
    (value) => {
        if (!value) userStore.temToken = null
    }
)
</script>

<style lang="scss">
.login-popup {
    .el-dialog__header {
        padding: 0;
    }
    .el-dialog__body {
        padding: 0 !important;
    }
}
</style>
