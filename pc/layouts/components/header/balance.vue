<template>
    <div class="balance flex items-center mr-[20px]" v-if="userStore.isLogin">
        <div class="px-[10px] flex py-[5px]">
            <div class="balance-item">
                <span>对话：{{ userStore?.userInfo?.balance }}条</span>
            </div>
            ；
            <div v-if="appStore.getDrawConfig.is_open" class="balance-item">
                <span>绘画：{{ userStore?.userInfo?.balance_draw }}条</span>
            </div>
        </div>
        <NuxtLink v-if="appStore.getIsShowRecharge" to="/openvip?mode=recharge">
            <div class="recharge-btn">充值</div>
        </NuxtLink>
    </div>
</template>

<script setup lang="ts">
import { useAppStore } from '~/stores/app'
import { useUserStore } from '~/stores/user'
const appStore = useAppStore()
const userStore = useUserStore()
</script>

<style lang="scss" scoped>
.balance {
    background-color: #fff7f3;
    font-size: 12px;
    border-radius: 100px;
}

.recharge-btn {
    padding: 5px 12px;
    border-radius: 100px;
    background: linear-gradient(89.14deg, #ffcb58 0%, #f7630e 100%);
    @apply text-sm text-white cursor-pointer;
}
</style>
