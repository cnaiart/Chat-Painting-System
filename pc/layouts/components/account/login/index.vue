<template>
    <div class="flex-1 pt-[30px] px-[30px] min-h-0">
        <el-tabs :model-value="loginWay" @tabChange="tabChange">
            <el-tab-pane
                v-for="(item, index) in loginWayListsFilter"
                :key="index"
                :label="item.name"
                :name="item.type"
            >
                <WeixinLogin
                    v-if="
                        item.type === LoginWayEnum.WEIXIN &&
                        tabCurrent == LoginWayEnum.WEIXIN
                    "
                />
                <MobileLogin v-if="item.type === LoginWayEnum.MOBILE" />
                <MailboxLogin v-if="item.type === LoginWayEnum.MAILBOX" />
            </el-tab-pane>
        </el-tabs>
    </div>
    <div class="bg-[#f4f4f4] px-[20px] py-[15px] text-sm flex">
        <div class="flex-1 text-tx-secondary" v-if="isOpenAgreement">
            您登录即同意
            <NuxtLink
                :to="`/policy/${PolicyAgreementEnum.SERVICE}`"
                v-slot="{ href }"
                custom
            >
                <a class="text-tx-primary" :href="href" target="_blank">
                    用户协议
                </a>
            </NuxtLink>
            和
            <NuxtLink
                class="text-tx-primary"
                :to="`/policy/${PolicyAgreementEnum.PRIVACY}`"
                v-slot="{ href }"
                custom
            >
                <a class="text-tx-primary" :href="href" target="_blank">
                    隐私政策
                </a>
            </NuxtLink>
        </div>
        <ElButton
            v-if="tabCurrent != LoginWayEnum.WEIXIN"
            type="primary"
            link
            @click="userStore.setLoginPopupType(LoginPopupTypeEnum.REGISTER)"
        >
            注册新账号
        </ElButton>
    </div>
</template>
<script setup lang="ts">
import { ElButton } from 'element-plus'
import WeixinLogin from './weixin-login.vue'
import MobileLogin from './mobile-login.vue'
import MailboxLogin from './mailbox-login.vue'
import { useAppStore } from '~/stores/app'
import { LoginPopupTypeEnum, PolicyAgreementEnum } from '~/enums/appEnums'
import { useUserStore } from '~/stores/user'

const appStore = useAppStore()
const userStore = useUserStore()
const isOpenAgreement = computed(
    () => appStore.getLoginConfig.login_agreement == 1
)
enum LoginWayEnum {
    WEIXIN = '1',
    MOBILE = '2',
    MAILBOX = '3'
}

const loginWayLists = [
    {
        name: '微信登录',
        type: LoginWayEnum.WEIXIN
    },
    {
        name: '手机号登录',
        type: LoginWayEnum.MOBILE
    },
    {
        name: '邮箱登录',
        type: LoginWayEnum.MAILBOX
    }
]

const getLoginWay = computed<string[]>(
    () => appStore.getLoginConfig?.login_way || []
)
const loginWayListsFilter = computed(() => {
    return loginWayLists.filter((item) => getLoginWay.value.includes(item.type))
})

const loginWay = computed(() => {
    tabCurrent.value = appStore.getLoginConfig.default_login_way.toString()
    return appStore.getLoginConfig.default_login_way.toString()
    // if (getLoginWay.value.includes(LoginWayEnum.WEIXIN)) {
    //     tabCurrent.value = LoginWayEnum.WEIXIN
    //     return LoginWayEnum.WEIXIN
    // }
    // const [firstLoginWay] = getLoginWay.value
    // tabCurrent.value = firstLoginWay
    // return firstLoginWay || ''
})

const tabCurrent = ref<string | number>(2)
const tabChange = (name: string | number) => {
    tabCurrent.value = name
}
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
