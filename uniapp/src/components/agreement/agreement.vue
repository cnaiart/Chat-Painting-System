<template>
    <view class="agreement" v-if="isOpenAgreement" :class="{ shake: isShake }">
        <view>
            <u-checkbox v-model="isActive" shape="circle">
                <view class="text-xs flex">
                    已阅读并同意
                    <view @click.stop>
                        <router-navigate
                            class="text-primary"
                            to="/packages/pages/agreement/agreement?type=service"
                        >
                            《服务协议》
                        </router-navigate>
                    </view>

                    和
                    <view @click.stop>
                        <router-navigate
                            class="text-primary"
                            to="/packages/pages/agreement/agreement?type=privacy"
                        >
                            《隐私协议》
                        </router-navigate>
                    </view>
                </view>
            </u-checkbox>
        </view>
    </view>
</template>
<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import { computed, ref } from 'vue'
const appStore = useAppStore()
const isActive = ref(false)
const isShake = ref(false)
const isOpenAgreement = computed(
    () => appStore.getLoginConfig.login_agreement == 1
)
const checkAgreement = () => {
    if (!isActive.value && isOpenAgreement.value) {
        uni.$u.toast('请勾选已阅读并同意《服务协议》和《隐私协议》')
        isShake.value = true
        setTimeout(() => {
            isShake.value = false
        }, 1000)
    } else if (!isOpenAgreement.value) {
        return true
    }
    return isActive.value
}
defineExpose({
    checkAgreement
})
</script>

<style lang="scss">
.shake {
    animation: shake 0.82s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
    transform: translate3d(0, 0, 0);
}
@keyframes shake {
    10%,
    90% {
        transform: translate3d(-1px, 0, 0);
    }
    20%,
    80% {
        transform: translate3d(2px, 0, 0);
    }
    30%,
    50%,
    70% {
        transform: translate3d(-4px, 0, 0);
    }
    40%,
    60% {
        transform: translate3d(4px, 0, 0);
    }
}
</style>
