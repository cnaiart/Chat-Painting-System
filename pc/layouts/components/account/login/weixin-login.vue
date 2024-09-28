<template>
    <div class="pb-[10px]">
        <div class="flex flex-col items-center mt-[20px]">
            <div class="relative w-[180px] h-[180px]" v-loading="pending">
                <ElImage
                    v-if="data.url"
                    :src="data.url"
                    class="w-full h-full"
                />
                <div
                    v-if="status == WxLoginStatus.SCANNED_CODE"
                    class="absolute left-0 top-0 w-full h-full bg-mask"
                >
                    <div
                        class="h-full text-primary flex justify-center items-center"
                    >
                        <Icon name="el-icon-SuccessFilled" :size="30" />
                    </div>
                </div>
                <div
                    v-if="
                        status == WxLoginStatus.INVALID ||
                        status == WxLoginStatus.LOGIN_FAIL ||
                        status == WxLoginStatus.CODE_ERROR
                    "
                    class="absolute left-0 top-0 w-full h-full bg-overlay cursor-pointer"
                    @click="refresh"
                >
                    <div
                        class="h-full flex flex-col justify-center items-center text-white"
                    >
                        <div>点击刷新</div>
                    </div>
                </div>
            </div>

            <template v-if="status == WxLoginStatus.SCANNED_CODE">
                <div class="mt-3">扫码成功</div>
                <div class="mt-5 text-error text-sm">
                    请在微信公众号中确认登录
                </div>
            </template>
            <template v-if="status == WxLoginStatus.INVALID">
                <div class="mt-3 text-error">二维码失效</div>
                <div class="mt-5 text-sm">请在点击二维码刷新</div>
            </template>
            <template v-if="status == WxLoginStatus.LOGIN_FAIL">
                <div class="mt-3 text-error">登录失败，请重新登录</div>
                <div class="mt-5 text-sm">请在点击二维码刷新</div>
            </template>
            <template
                v-if="
                    status == WxLoginStatus.NORMAL ||
                    status == WxLoginStatus.LOGIN_SUCCESS ||
                    status == WxLoginStatus.CODE_ERROR
                "
            >
                <div class="mt-3">微信扫码登录/注册</div>
                <div class="mt-5 text-tx-secondary text-sm">
                    首次扫码关注公众号后将自动注册新账号
                </div>
            </template>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { ElImage } from 'element-plus'
import { WxLoginStatus, useCheckTicket, useGetCode } from './login'
import { useUserStore } from '~/stores/user'
const userStore = useUserStore()

const { state: code, getData: getCode } = useGetCode()
const { pending, data } = toRefs(code.value)
const { start, end, status } = useCheckTicket(data)

const refresh = async () => {
    status.value = WxLoginStatus.NORMAL
    try {
        await getCode()
        start()
    } catch (error) {
        status.value = WxLoginStatus.CODE_ERROR
    }
}

onMounted(() => {
    refresh()
})
onUnmounted(() => {
    end()
})
</script>

<style lang="scss" scoped></style>
