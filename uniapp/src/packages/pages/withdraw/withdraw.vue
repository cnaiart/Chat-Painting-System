<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="bg-white h-[100vh]">
        <view class="h-[200rpx] container-top pt-[40rpx] px-[40rpx]">
            <view
                class="flex justify-between"
                style="color: var(--color-btn-text)"
            >
                <view> 可提现金额：{{ canWithdrawMoney }}</view>
                <view class="flex items-center" @click="toWithdrawRecordList">
                    <text class="mr-[10rpx]">提现记录</text>
                    <u-icon name="arrow-right" size="24" />
                </view>
            </view>
        </view>
        <view class="bg-white rounded-[30rpx] mt-[-90rpx] p-[40rpx]">
            <view
                class="flex justify-between items-center pb-[16rpx] container-bottom"
            >
                <view class="flex items-center flex-1">
                    <text class="text-[46rpx] text-main">¥</text>
                    <input
                        type="text"
                        class="text-[66rpx] h-[80rpx] pl-[10rpx]"
                        v-model="formData.money"
                        placeholder="0.00"
                        @input="handleCheckMoney"
                    />
                </view>
                <view
                    class="text-xs text-content"
                    @click="formData.money = canWithdrawMoney"
                >
                    全部提现
                </view>
            </view>
        </view>

        <view class="px-[40rpx]">
            <text class="font-medium">提现方式</text>
            <!-- <view
                class="bg-[#F8F8F8] p-[20rpx] flex justify-between items-center mt-[30rpx]"
                @click="showAccountPopup = true"
            >
                <view class="flex">
                    <u-image
                        width="40"
                        height="40"
                        src="@/static/images/payment/alipay.png"
                        alt=""
                    />
                    <text class="ml-[26rpx]">支付宝</text>
                    <text v-if="formData.account">
                        ({{ formData.account }})
                    </text>
                </view>
                <view>{{ formData.account == '' ? '添加' : '修改' }}账号</view>
            </view> -->

            <view class="mt-[10rpx] grid grid-cols-2 gap-2">
                <view
                    class="flex p-[12px] unselect_border rounded-lg"
                    :class="{ select_border: formData.type == item.id }"
                    v-for="item in withdrawList"
                    :key="item.id"
                    @click="selectType(item.id)"
                >
                    <u-image width="40" height="40" :src="item.image" alt="" />
                    <view class="ml-[10rpx]">{{ item.title }}</view>
                </view>
            </view>
            <view class="mt-[10px]">
                <input
                    class="bg-page p-[20rpx] rounded-md"
                    :placeholder="`请输入${
                        formData.type == 3 ? '微信' : '支付宝'
                    }账号`"
                    v-model="formData.account"
                    v-if="formData.type != 2"
                />
                <input
                    class="bg-page p-[20rpx] mt-[10rpx] rounded-md"
                    placeholder="请输入真实姓名"
                    v-model="formData.real_name"
                    v-if="formData.type != 2"
                />
                <uploader
                    v-if="formData.type == 3 || formData.type == 4"
                    v-model="moneyQrCode"
                    :maxUpload="1"
                    image-fit="aspectFill"
                    :deletable="true"
                    @change="chooseImg"
                />
            </view>
        </view>
        <view class="mx-[30rpx] mt-[100rpx]">
            <u-button
                type="primary"
                shape="circle"
                size="default"
                :customStyle="{
                    padding: '0 30rpx',
                    height: '80rpx'
                }"
                @click="handleSubmit"
            >
                立即提交
            </u-button>
        </view>
        <view class="px-[40rpx] mt-[160rpx]" v-if="withdrawConfig.open">
            <text class="font-medium">提现说明</text>
            <view
                style="white-space: break-spaces"
                class="mt-[30rpx] text-[#999999] break-all"
            >
                {{ withdrawConfig.explain }}
            </view>
        </view>

        <!--  添加账号  -->
        <AccountFull
            v-model:show="showAccountPopup"
            v-model:account="formData.account"
            v-model:real_name="formData.real_name"
        ></AccountFull>
    </view>
    <!-- #ifdef H5 -->
    <!--    悬浮菜单    -->
    <floating-menu></floating-menu>
    <!-- #endif -->
</template>
<script setup lang="ts">
import { reactive, ref, nextTick } from 'vue'
import { onShow } from '@dcloudio/uni-app'
import type { WithdrawApplyType } from '@/api/promotion'
import {
    getDistributionIndex,
    withdrawApply,
    withdrawWay
} from '@/api/promotion'
// import { useRouter } from 'uniapp-router-next-zm'
import { useRouter } from 'uniapp-router-next'
import AccountFull from './component/acount-full.vue'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'
import Uploader from '@/packages/components/uploader/uploader.vue'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const router = useRouter()
const appStore = useAppStore()
const useStore = useUserStore()
const showAccountPopup = ref<boolean>(false)
const canWithdrawMoney = ref<number>(0)
const moneyQrCode = ref([])
const withdrawConfig = ref<any>({
    open: 1,
    explain: ''
})
const formData = reactive<WithdrawApplyType>({
    money: '', // 提现金额
    account: '', // 支付宝账号
    real_name: '', // 支付宝姓名
    type: '1', //提现方式：1-支付宝；2-微信零钱；3-微信收款码；4-支付宝收款码；
    money_qr_code: ''
})

//缓存的支付信息
const cacheAli: any = ref({
    account: '',
    real_name: ''
})
const cacheWx: any = ref({
    account: '',
    real_name: ''
})

const getDistributionData = async () => {
    try {
        const { user, withdraw_config } = await getDistributionIndex()
        canWithdrawMoney.value = user.user_money
        formData.account = withdraw_config.ali_acccount
        formData.real_name = withdraw_config.ali_name
        withdrawConfig.value = withdraw_config
        getUserCache()
    } catch (error) {
        console.log('获取分销中心接口失败', error)
    }
}

/**
 * @return { void }
 * @description 处理金额
 */
const handleCheckMoney = async ({ detail }: any) => {
    let val = detail.value.replace(/(^\s*)|(\s*$)/g, '')
    if (!val) {
        formData.money = ''
        return
    }
    const reg = /[^\d.]/g
    // 只能是数字和小数点，不能是其他输入
    val = val.replace(reg, '')
    // 保证第一位只能是数字，不能是点
    val = val.replace(/^\./g, '')
    // 小数只能出现1位
    val = val.replace('.', '$#$').replace(/\./g, '').replace('$#$', '.')
    // 小数点后面保留2位
    val = val.replace(/^(\-)*(\d+)\.(\d\d).*$/, '$1$2.$3')
    await nextTick()
    formData.money = val
}

//提现方式
const withdrawList = ref()

//选中提现方式
const selectType = async (val: any) => {
    formData.type = val
    await nextTick()
    //设置缓存数据
    if (val == 1 || val == 4) {
        Object.keys(cacheAli.value).map((item) => {
            formData[item] = cacheAli.value[item]
        })
    }
    if (val == 3) {
        Object.keys(cacheWx.value).map((item) => {
            formData[item] = cacheWx.value[item]
        })
    }
}

//提取缓存数据
const getUserCache = () => {
    cacheAli.value.account = withdrawConfig.value?.ali_acccount
    cacheAli.value.real_name = withdrawConfig.value?.ali_name
    cacheWx.value.account = withdrawConfig.value?.wechat_acccount
    cacheWx.value.real_name = withdrawConfig.value?.wechat_name
}

//获取提现方式
const getWithdrawWay = async () => {
    withdrawList.value = await withdrawWay()
    // formData.type = withdrawList.value[0].id
    selectType(withdrawList.value[0].id)
}

//选择二维码
const chooseImg = async (value: string[]) => {
    console.log(value)
    formData.money_qr_code = value[0]
}

const handleSubmit = async () => {
    try {
        const { id } = await withdrawApply(formData)
        uni.$u.toast('申请成功')
        setTimeout(toWithdrawRecordList, 1000)
    } catch (error) {
        console.log('提现申请请求失败', error)
    }
}

const toWithdrawRecordList = () => {
    router.navigateTo('/packages/pages/withdraw_record/withdraw_record')
}

onShow(async () => {
    await getDistributionData()
    await getWithdrawWay()
})
</script>
<style lang="scss" scoped>
.container-top {
    background-color: $u-type-primary;
}
.container-bottom {
    border-bottom: 2rpx solid rgba(0, 0, 0, 0.1);
}
.select_border {
    border: 1px solid $u-type-primary !important;
}
.unselect_border {
    border: 1px solid $u-disabled-color;
}
</style>
