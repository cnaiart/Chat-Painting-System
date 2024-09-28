<template>
    <div>
        <NuxtLayout name="default">
            <div v-if="centerData?.is_open != 1" class="w-full">
                <el-card
                    shadow="never"
                    class="!border-none h-full w-full !rounded-[12px]"
                >
                    <div class="text-xl">推广功能未开启!</div>
                </el-card>
            </div>
            <div class="h-full w-full" v-if="centerData?.is_open == 1">
                <ElScrollbar height="100%">
                    <el-card
                        shadow="never"
                        class="!border-none h-full !rounded-[12px]"
                    >
                        <div class="font-medium text-[18px]">分销规则</div>
                        <div class="mt-[23px] flex">
                            <div
                                v-if="centerData?.config.level >= 1"
                                class="bg-gradient-to-r from-[#E1AB7A] to-[#FFE8CF] px-[20px] py-[15px] w-[300px] rounded-md shadow relative"
                            >
                                <div class="text-[18px] text-[#6A3D15]">
                                    一级分销
                                </div>
                                <div
                                    class="text-[14px] mt-[12px] text-[#6A3D15]"
                                >
                                    成为分销商，下级消费您获得{{
                                        centerData?.config?.first_ratio
                                    }}%奖励
                                </div>
                                <div
                                    class="py-[6px] px-[15px] absolute top-0 right-0 bg-[#D3AF8D] rounded-bl-lg"
                                >
                                    <span class="text-[13px] text-[#6A3D15]"
                                        >分佣{{
                                            centerData?.config?.first_ratio
                                        }}%</span
                                    >
                                </div>
                            </div>
                            <div
                                v-if="centerData?.config.level == 2"
                                class="bg-gradient-to-r from-[#B36FFC] to-[#E3D0FF] px-[20px] py-[15px] w-[300px] rounded-md shadow ml-[20px] relative"
                            >
                                <div class="text-[18px] text-[#3E2077]">
                                    二级分销
                                </div>
                                <div
                                    class="text-[14px] mt-[12px] text-[#3E2077]"
                                >
                                    成为分销商，下2级消费您获得{{
                                        centerData?.config?.second_ratio
                                    }}%奖励
                                </div>
                                <div
                                    class="py-[6px] px-[15px] absolute top-0 right-0 bg-[#AE80F1] rounded-bl-lg"
                                >
                                    <span class="text-[13px] text-[#3E2077]"
                                        >分佣{{
                                            centerData?.config?.second_ratio
                                        }}%</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="font-medium text-[18px] mt-[30px]">
                            我的收益
                        </div>
                        <!--非分销商-->
                        <div
                            class="mt-[23px]"
                            v-if="
                                !centerData?.user?.is_distribution &&
                                centerData?.config?.condition == 2
                            "
                        >
                            <div class="text-[14px] text-[#999999]">
                                抱歉，您当前还不是分销商，请先申请成为分销商
                            </div>
                            <el-button
                                class="mt-[20px]"
                                type="primary"
                                @click="openApplyPop"
                                >申请成为分销商</el-button
                            >
                        </div>
                        <!--分销商-->
                        <div
                            class="mt-[23px]"
                            v-if="
                                centerData?.user?.is_distribution ||
                                centerData?.config?.condition == 1
                            "
                        >
                            <div
                                class="bg-[#F8F8F8] pt-[16px] pb-[30px] px-[20px] flex justify-between"
                            >
                                <div class="flex w-[70%] items-start">
                                    <div class="flex-1">
                                        <div class="text-[#333333] text-[13px]">
                                            可提现余额（元）
                                        </div>
                                        <div
                                            class="text-primary text-[32px] my-[10px]"
                                        >
                                            {{ centerData?.user?.user_money }}
                                        </div>
                                        <!-- <NuxtLink :to="`/promotion/withdrawal`"> -->
                                        <el-button
                                            type="primary"
                                            @click="toWithdrawal"
                                            >去提现</el-button
                                        >
                                        <!-- </NuxtLink> -->
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-[#333333] text-[13px]">
                                            今日收益（元）
                                        </div>
                                        <div class="text-[32px] my-[10px]">
                                            +{{ centerData?.user?.today_money }}
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-[#333333] text-[13px]">
                                            已提现（元）
                                        </div>
                                        <div class="text-[32px] my-[10px]">
                                            {{
                                                centerData?.user
                                                    ?.withdrawn_money
                                            }}
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-[#333333] text-[13px]">
                                            累计收益（元）
                                        </div>
                                        <div class="text-[32px] my-[10px]">
                                            {{
                                                centerData?.user
                                                    ?.total_user_money
                                            }}
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="text-primary self-start flex-none flex items-center cursor-pointer"
                                    @click="openIncomePop"
                                >
                                    <Icon name="el-icon-Memo" :size="16" />
                                    <span>收益明细</span>
                                </div>
                            </div>
                            <div class="font-medium text-[18px] mt-[30px]">
                                推广链接
                            </div>
                            <div class="text-[14px] mt-[23px]">
                                <div>
                                    <span>推广链接：</span>
                                    <span class="text-[#4153FF]">{{
                                        centerData?.config?.pc_promotion_url
                                    }}</span>
                                    <el-button class="ml-2" @click="copyLink"
                                        >复制</el-button
                                    >
                                </div>
                            </div>
                            <div class="font-medium text-[18px] mt-[30px]">
                                邀请列表
                            </div>
                            <el-tabs
                                class="mt-[23px]"
                                v-model="params.level"
                                @tab-change="tabChange"
                            >
                                <el-tab-pane
                                    :label="`全部(${pager.extend.all})`"
                                    name=""
                                ></el-tab-pane>
                                <el-tab-pane
                                    :label="`直接用户(${pager.extend.first})`"
                                    :name="1"
                                ></el-tab-pane>
                                <el-tab-pane
                                    :label="`间接用户(${pager.extend.second})`"
                                    :name="2"
                                ></el-tab-pane>
                            </el-tabs>
                            <div class="w-full">
                                <el-table size="large" :data="pager.lists">
                                    <el-table-column label="用户昵称">
                                        <template #default="{ row }">
                                            <div class="flex items-center">
                                                <el-image
                                                    class="w-[48px] h-[48px]"
                                                    :src="row.avatar"
                                                ></el-image>
                                                <div class="ml-2">
                                                    {{ row.nickname }}
                                                </div>
                                            </div>
                                        </template>
                                    </el-table-column>
                                    <el-table-column
                                        label="订单量"
                                        prop="order_num"
                                    ></el-table-column>
                                    <el-table-column
                                        label="累计消费"
                                        prop="total_amount"
                                    ></el-table-column>
                                    <el-table-column
                                        label="邀请人数"
                                        prop="invite_num"
                                    ></el-table-column>
                                    <el-table-column
                                        label="分销资格"
                                        prop="is_distribution_desc"
                                    >
                                    </el-table-column>
                                    <el-table-column
                                        label="注册时间"
                                        prop="create_time"
                                    ></el-table-column>
                                </el-table>

                                <div class="flex justify-end mt-4">
                                    <pagination
                                        v-model="pager"
                                        @change="getLists"
                                    />
                                </div>
                            </div>
                        </div>
                    </el-card>
                </ElScrollbar>
                <ApplyDistribution
                    v-if="showPop"
                    ref="applyPopRef"
                    @close-pop="closePop"
                ></ApplyDistribution>
                <IncomeDetail
                    ref="incomeDetailPopRef"
                    @close-pop="closePop"
                    v-if="showPop"
                ></IncomeDetail>
            </div>
        </NuxtLayout>
    </div>
</template>

<script setup lang="ts">
import { usePaging } from '@/utils/usePaging'
import {
    ElCard,
    ElButton,
    ElTabs,
    ElTabPane,
    ElTable,
    ElTableColumn,
    ElScrollbar,
    ElImage
} from 'element-plus'
import { distributionCenter, fansList } from '@/api/promotion'

const router = useRouter()

//申请分销弹框
const applyPopRef = shallowRef()
const showPop = ref(false)

const incomeDetailPopRef = shallowRef()

//请求参数
const params = reactive({
    level: '' //1-直接用户 2-间接用户
})

//分页组件
const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: fansList,
    params: params
})

const tabChange = () => {
    getLists()
}

//分销数据
const centerData: any = ref()
const getCenterData = async () => {
    centerData.value = await distributionCenter()
}

//打开收益明细
const openIncomePop = async () => {
    showPop.value = true
    await nextTick()
    incomeDetailPopRef.value.open()
}

//打开申请分销弹框
const openApplyPop = async () => {
    if (centerData.value.apply_detail?.status == 1) {
        feedback.msgWarning('正在审核中！')
        return
    }
    showPop.value = true
    await nextTick()
    applyPopRef.value.open()
}

//跳转至提现页面
const toWithdrawal = () => {
    if (centerData.value.withdraw_config.type.length == 0) {
        feedback.alertWarning('后台未设置提现方式！')
        return
    }
    router.push('/promotion/withdrawal')
}

//复制链接
const copyLink = () => {
    copy(centerData.value.config.pc_promotion_url)
}

//关闭弹框
const closePop = () => {
    showPop.value = false
}

onMounted(() => {
    getLists()
    getCenterData()
})
definePageMeta({
    auth: true,
    layout: false
})
</script>

<style scoped lang="scss">
// ::v-deep .el-table {
//     width: 100%;
//     .el-table__header-wrapper table,
//     .el-table__body-wrapper table {
//         width: 100% !important;
//     }
//     .el-table__body,
//     .el-table__footer,
//     .el-table__header {
//         table-layout: auto;
//     }
// }
</style>
