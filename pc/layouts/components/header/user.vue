<template>
    <div class="flex-none ml-[10px]">
        <template v-if="userStore.isLogin">
            <ClientOnly>
                <div class="flex items-center">
                    <div
                        v-if="
                            userStore.userInfo.is_member &&
                            memberPackageTimeView
                        "
                        class="flex items-center mr-[10px] text-xs text-[#F2B55B]"
                    >
                        <span>会员： </span>
                        <span>
                            {{ userStore.userInfo.member_package_name }}</span
                        >
                        <span>
                            ({{
                                userStore.userInfo.member_expired
                                    ? '已过期'
                                    : userStore.userInfo.member_end_time
                            }})
                        </span>
                    </div>

                    <Balance />
                    <ElDropdown @command="handleCommand">
                        <template #default>
                            <div class="flex items-center">
                                <ElAvatar
                                    :size="25"
                                    :src="userStore.userInfo.avatar"
                                />
                                <div class="ml-1 flex items-center">
                                    <span class="mr-2">
                                        {{ userStore.userInfo.nickname }}
                                    </span>
                                    <Icon name="el-icon-ArrowDown"></Icon>
                                </div>
                            </div>
                        </template>

                        <template #dropdown>
                            <ElDropdownMenu>
                                <ElDropdownItem command="user">
                                    个人信息
                                </ElDropdownItem>
                                <ElDropdownItem command="logout">
                                    退出登录
                                </ElDropdownItem>
                            </ElDropdownMenu>
                        </template>
                    </ElDropdown>
                </div>
            </ClientOnly>
        </template>
        <div v-else class="flex items-center">
            <el-image
                class="w-[25px] h-[25px]"
                :src="default_avatar"
            ></el-image>
            <ElButton link @click="handleToLogin"> 点击登录 </ElButton>
        </div>
    </div>
    <UserPop ref="userPopRef"> </UserPop>
</template>
<script lang="ts" setup>
import default_avatar from '@/assets/images/default_avatar.png'
import UserPop from './user-info.vue'
import Balance from './balance.vue'
import { useUserStore } from '@/stores/user'
import feedback from '@/utils/feedback'
import { logout } from '@/api/account'
import { LoginPopupTypeEnum } from '@/enums/appEnums'
import {
    ElAvatar,
    ElButton,
    ElDropdown,
    ElDropdownMenu,
    ElDropdownItem
} from 'element-plus'
import { useAppStore } from '~/stores/app'
const userStore = useUserStore()
const userPopRef = shallowRef()

const appStore = useAppStore()
const memberPackageTimeView = computed(() =>
    appStore.config?.member_package_time_view?.includes('1')
)

const handleToLogin = () => {
    userStore.setLoginPopupType(LoginPopupTypeEnum.LOGIN)
    userStore.toggleShowLogin(true)
}

const handleCommand = async (command: string) => {
    switch (command) {
        case 'logout':
            await feedback.confirm('确定退出登录吗？')
            await logout()
            userStore.logout()
            window.location.reload()
            break
        case 'user':
            userPopRef.value.open()
            break
    }
}
</script>

<style lang="scss" scoped></style>
