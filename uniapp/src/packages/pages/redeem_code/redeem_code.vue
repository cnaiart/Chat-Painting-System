<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="redeem-code">
        <view
            v-if="redeemCodeConfig.is_show"
            class="flex justify-between bg-primary-light-9 text-primary px-[24rpx] py-[30rpx]"
        >
            <view class="flex flex-1 pr-[20rpx] items-center">
                <view class="w-[124rpx]">购买链接:</view>
                <view class="flex-1 w-full">
                    <!--#ifndef APP-PLUS-->
                    <u-notice-bar
                        padding="0"
                        bg-color="none"
                        mode="horizontal"
                        :close-icon="false"
                        :volume-icon="false"
                        :color="$theme.primaryColor"
                        :list="[redeemCodeConfig.buy_site]"
                    ></u-notice-bar>
                    <!--#endif-->
                    <!--#ifdef APP-PLUS-->
                    <view>{{ redeemCodeConfig.buy_site }}</view>
                    <!--#endif-->
                </view>
            </view>
            <view class="underline flex-none" @click="onCopy(redeemCodeConfig.buy_site)"
                >复制链接</view
            >
        </view>

        <view
            class="m-[24rpx] px-[30rpx] py-[50rpx] bg-white rounded"
            v-if="redeemCodeConfig.is_open"
        >
            <view class="py-[10rpx] px-[20rpx] bg-[#f5f5f5] rounded">
                <u-input v-model="code" placeholder="请输入卡密编号"></u-input>
            </view>
            <view class="flex items-center justify-center mt-[30px]">
                <u-button
                    type="primary"
                    shape="circle"
                    size="medium"
                    :customStyle="{
                        width: '250rpx',
                        height: '80rpx',
                        fontSize: '30rpx'
                    }"
                    :loading="isQuery"
                    @click="queryRedeem"
                >
                    查询
                </u-button>
            </view>
        </view>
        <view v-else class="py-[400rpx]">
            <u-empty text="功能未开启"></u-empty>
        </view>
        <u-popup v-model="showCheckResult" mode="center" border-radius="24">
            <view class="w-[600rpx] px-[24rpx]">
                <view class="p-[30rpx] text-lg text-center font-medium">
                    查询结果
                </view>
                <view class="h-[200rpx] px-[20rpx]">
                    <view class="flex mt-[20rpx]">
                        <text>卡密类型：</text>
                        <text class="ml-[20rpx]">{{
                            checkResult.type_desc
                        }}</text>
                    </view>
                    <view class="flex mt-[20rpx]">
                        <text>卡密面额：</text>
                        <text class="ml-[20rpx]">{{
                            checkResult.content
                        }}</text>
                    </view>
                    <view class="flex mt-[20rpx]">
                        <text>兑换时间：</text>
                        <text class="ml-[20rpx]">{{
                            checkResult.failure_time
                        }}</text>
                    </view>
                    <view class="flex mt-[20rpx]">
                        <text>有效期至：</text>
                        <text class="ml-[20rpx]">{{
                                checkResult.valid_time
                            }}</text>
                    </view>


                </view>
                <view class="py-[30rpx] px-[16rpx] bg-white">
                    <u-button
                        type="primary"
                        shape="circle"
                        size="medium"
                        :customStyle="{
                            width: '100%',
                            height: '82rpx',
                            fontSize: '30rpx'
                        }"
                        :loading="isUse"
                        @click="onUseRedeemCode"
                    >
                        立即兑换
                    </u-button>
                </view>
            </view>
        </u-popup>
        <!--    底部导航    -->
        <tabbar></tabbar>
        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
    </view>
</template>
<script setup lang="ts">
import { ref, computed } from 'vue'
import { useCopy } from '@/hooks/useCopy'
import { useAppStore } from '@/stores/app'
import { useLockFn } from '@/hooks/useLockFn'
import type { RedeemCodeResponse } from '@/api/redeem_code'
import { checkRedeemCode, useRedeemCode } from '@/api/redeem_code'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

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
    const { copy } = useCopy()
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
        showCheckResult.value = false
        uni.$u.toast('兑换成功')
        code.value = ''
    } catch (error) {
        console.log('兑换卡密失败=>', error)
    }
})
</script>
