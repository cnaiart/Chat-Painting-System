<template>
    <div class="h-full bg-white rounded-[12px] p-[20px]">
        <div
            v-if="redeemCodeConfig.is_show"
            class="flex text-[#F7A935] mt-[20px] p-[12px] rounded max-w-[1000px] mx-auto justify-center"
            style="
                background: linear-gradient(
                    -88.46deg,
                    rgba(252, 245, 232, 0) 1.34%,
                    rgba(252, 245, 232, 1) 50.41%,
                    rgba(252, 245, 245, 0) 99.48%
                );
            "
        >
            <div class="flex pr-[20rpx]">
                <span class="mr-[10px]">购买链接:</span>
                <span>{{ redeemCodeConfig.buy_site }}</span>
            </div>
            <div
                class="underline cursor-pointer ml-[30px]"
                @click="onCopy(redeemCodeConfig.buy_site)"
            >
                复制链接
            </div>
        </div>
        <div class="flex justify-center mt-[60px]">
            <el-input
                v-model="code"
                size="large"
                style="width: 500px"
                placeholder="请输入卡密编号"
            />
            <el-button
                type="primary"
                size="large"
                class="ml-[20px]"
                :loading="isQuery"
                @click="queryRedeem"
            >
                查询
            </el-button>
        </div>
    </div>

    <ElDialog v-model="showCheckResult" width="400" append-to-body>
        <template #header>
            <div class="text-lg text-center font-medium">查询结果</div>
        </template>
        <div class="h-full">
            <el-form-item label="卡密类型：">
                {{ checkResult.type_desc }}
            </el-form-item>
            <el-form-item label="卡密面额：">
                {{ checkResult.content }}
            </el-form-item>
            <el-form-item label="兑换时间：">
                {{ checkResult.failure_time }}
            </el-form-item>
            <el-form-item label="有效期至：" v-if="checkResult.valid_time">
                {{ checkResult.valid_time }}
            </el-form-item>
        </div>
        <div class="flex-1 flex justify-center items-center bg-white pt-[20px]">
            <el-button
                type="primary"
                size="large"
                :loading="isUse"
                @click="onUseRedeemCode"
            >
                立即兑换
            </el-button>
        </div>
    </ElDialog>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { useAppStore } from '~/stores/app'
import { copy } from '@/utils/util'
import { useLockFn } from '~/composables/useLockFn'
import type { RedeemCodeResponse } from '~/api/redeem_code'
import { checkRedeemCode, useRedeemCode } from '~/api/redeem_code'
import feedback from '~/utils/feedback'

definePageMeta({
    auth: true,
    layout: false
})

const appStore = useAppStore()
// 兑换码
const code = ref<string>('')
// 显示查询结果
const showCheckResult = ref<boolean>(false)
// 查询结果
const checkResult = ref<RedeemCodeResponse>({
    content: '',
    failure_time: '',
    id: '',
    sn: '',
    type: '',
    type_desc: '',
    valid_time: ''
})

// 获取卡密信息设置
const redeemCodeConfig = computed(() => appStore.getCardCodeConfig)

const onCopy = (text: string) => {
    copy(text)
}

const { isLock: isQuery, lockFn: queryRedeem } = useLockFn(async () => {
    try {
        const data = await checkRedeemCode({ sn: code.value })
        showCheckResult.value = true
        checkResult.value = data
    } catch (error) {
        code.value = ''
        console.log('查询卡密失败=>', error)
    }
})

const { isLock: isUse, lockFn: onUseRedeemCode } = useLockFn(async () => {
    try {
        await useRedeemCode({ sn: code.value })
        feedback.msgSuccess('兑换成功')
        showCheckResult.value = false
        code.value = ''
    } catch (error) {
        console.log('兑换卡密失败=>', error)
    }
})
</script>
