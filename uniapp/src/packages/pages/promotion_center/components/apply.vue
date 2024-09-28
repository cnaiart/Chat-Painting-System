<template>
    <view class="apply bg-white min-h-screen">
        <!--     申请中    -->
        <view
            class="pt-[45%] text-center"
            v-if="applyData?.status == 1 && !canApply"
        >
            <u-icon name="clock-fill" color="#ff9c1e" size="100"></u-icon>
            <view class="font-medium text-xl my-[30rpx]">审核中</view>
            <view class="text-muted">正在审核中，请勿重复提交</view>
        </view>
        <!--     申请失败    -->
        <view
            class="pt-[45%] px-[40rpx] text-center"
            v-if="applyData?.status == 3 && !canApply"
        >
            <u-icon
                name="close-circle-fill"
                color="#ee4136"
                size="100"
            ></u-icon>
            <view class="font-medium text-xl my-[30rpx]">审核拒绝</view>
            <view class="text-[#ee4136] mb-[80rpx]">{{
                applyData?.audit_remark
            }}</view>
            <u-button
                type="primary"
                shape="circle"
                @click="
                    () => {
                        applyData = []
                        canApply = true
                    }
                "
            >
                重新申请
            </u-button>
        </view>
        <!--     未申请    -->
        <view class="px-[30rpx]" v-if="!applyData?.status || canApply">
            <view class="text-[40rpx] font-medium text-center py-[80rpx]">
                申请成为分销商
            </view>
            <u-form :label-width="150">
                <u-form-item label="姓名">
                    <u-input
                        class="flex-1"
                        v-model="formData.name"
                        :border="false"
                        placeholder="请输入姓名"
                    />
                </u-form-item>

                <u-form-item label="手机号">
                    <u-input
                        class="flex-1"
                        v-model="formData.mobile"
                        placeholder="请输入手机号"
                    />
                </u-form-item>
            </u-form>
            <view class="my-[40rpx]">
                <u-checkbox v-model="isActiveAgreement" shape="circle">
                    <view class="flex text-xs">
                        <text>阅读并同意</text>
                        <router-navigate
                            class="text-primary"
                            to="/packages/pages/agreement/agreement?type=distribution"
                        >
                            <text class="container-user">《用户分销协议》</text>
                        </router-navigate>
                    </view>
                </u-checkbox>
            </view>
            <view>
                <u-button
                    type="primary"
                    shape="circle"
                    @click="handleApplyLock"
                    :loading="isLock"
                >
                    立即申请
                </u-button>
            </view>
        </view>
    </view>
</template>
<script setup lang="ts">
import { distributionApply } from '@/api/promotion'
import { useLockFn } from '@/hooks/useLockFn'
import { reactive, ref, watchEffect } from 'vue'

const props = defineProps<{
    applyDetail: Record<string, any>
}>()
const emit = defineEmits<{
    (event: 'reload'): void
}>()
const formData = reactive({
    name: '',
    mobile: ''
})
const canApply = ref(false)
const isActiveAgreement = ref(false)
const applyData = ref<any>([])

watchEffect(() => {
    applyData.value = props.applyDetail
})

const { lockFn: handleApplyLock, isLock } = useLockFn(async () => {
    if (!isActiveAgreement.value) {
        uni.$u.toast('请阅读并同意《用户分销协议》')
        return
    }
    await distributionApply(formData)
    applyData.value.status = 1
    canApply.value = false
    emit('reload')
})
</script>
