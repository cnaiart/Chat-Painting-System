<template>
    <div class="flex flex-col justify-center items-center">
        <div class="text-tx-regular mb-4">您还未登录，请先登录</div>
        <ElButton @click="toLogin">登录</ElButton>
    </div>
</template>
<script lang="ts" setup>
import { useUserStore } from '~~/stores/user'
import { ElButton } from 'element-plus'
const userStore = useUserStore()
const toLogin = () => {
    userStore.toggleShowLogin(true)
}
</script>

<style lang="scss" scoped></style>
