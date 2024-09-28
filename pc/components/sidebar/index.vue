<template>
    <div
        class="sidebar mr-4 bg-white rounded-[8px] overflow-hidden w-[250px] p-[15px] flex flex-col"
    >
        <div class="user rounded-lg overflow-hidden flex-none">
            <!-- <div
                class="px-[10px] py-[12px] bg-primary flex items-center"
                @click="openLogin"
            >
                <ElAvatar
                    :src="userInfo.avatar || default_avatar"
                    :size="32"
                ></ElAvatar>
                <div class="ml-[10px] text-white text-[14px]">
                    {{ userInfo.nickname }}
                </div>
                <div
                    class="flex-1 text-white flex justify-between items-center cursor-pointer"
                    v-if="isEmptyObject(userInfo)"
                >
                    <div>未登录</div>
                    <div class="text-[12px]">点击登录></div>
                </div>
            </div> -->
            <div class="bg-[#EFF9F5] p-[10px]" v-if="!isEmptyObject(userInfo)">
                <div>会员：{{ userInfo.member_end_time || '未开通' }}</div>
                <div class="flex items-end justify-between mt-[15px]">
                    <div>
                        <div class="text-primary">
                            账户余额：{{ userInfo.balance || 0 }}条
                        </div>
                    </div>
                    <div v-if="appStore.getIsShowRecharge">
                        <NuxtLink to="/openvip?type=recharge">
                            <ElButton type="warning" size="small">
                                充值
                            </ElButton>
                        </NuxtLink>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-hidden mt-4 relative flex-1 min-h-0">
            <el-scrollbar height="100%">
                <slot name="main"></slot>
            </el-scrollbar>
        </div>
    </div>
</template>
<script setup lang="ts">
import { ElAvatar, ElButton, ElScrollbar } from 'element-plus'
import { useUserStore } from '~~/stores/user'
import { isEmptyObject } from '~~/utils/validate'
import default_avatar from '@/assets/images/default_avatar.png'
import { useAppStore } from '~~/stores/app'
const userStore = useUserStore()
const appStore = useAppStore()
const { userInfo } = toRefs(userStore)

//打开登录弹框
const openLogin = () => {
    if (isEmptyObject(userInfo.value)) {
        userStore.toggleShowLogin(true)
        userStore.setLoginPopupType()
    }
}

const getUserInfo = () => {
    userStore.getUser()
}

getUserInfo()
</script>
<style lang="scss" scoped>
// .sidebar {
//     height: calc(100vh - var(--header-height));
// }
</style>
