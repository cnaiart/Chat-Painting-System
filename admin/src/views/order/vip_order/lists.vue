<template>
    <el-card shadow="never" class="!border-none">
        <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
            <div class="flex flex-col items-center justify-center">
                <div class="font-medium text-[24px]">{{ pager.extend.order_num }}</div>
                <div>会员订单数</div>
            </div>
            <div class="flex flex-col items-center justify-center">
                <div class="font-medium text-[24px]">{{ pager.extend.total_amount }}</div>
                <div>累计金额</div>
            </div>
            <div class="flex flex-col items-center justify-center">
                <div class="font-medium text-[24px]">{{ pager.extend.refund_order_num }}</div>
                <div>退款订单</div>
            </div>
            <div class="flex flex-col items-center justify-center">
                <div class="font-medium text-[24px]">{{ pager.extend.refund_total_amount }}</div>
                <div>累计退款金额</div>
            </div>
            <div class="flex flex-col items-center justify-center">
                <div class="font-medium text-[24px]">{{ pager.extend.net_income }}</div>
                <div>净收入</div>
            </div>
        </div>
    </el-card>
    <el-card shadow="never" class="!border-none mt-4">
        <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
            <el-form-item label="用户信息">
                <el-input
                    class="w-[280px]"
                    v-model="queryParams.user_info"
                    placeholder="请输入用户ID编号/用户昵称"
                    clearable
                />
            </el-form-item>
            <el-form-item label="订单来源">
                <el-select class="w-[280px]" v-model="queryParams.terminal">
                    <el-option label="全部" value />
                    <el-option
                        v-for="(item, key) in ClientMap"
                        :key="key"
                        :label="item"
                        :value="key"
                    />
                </el-select>
            </el-form-item>
            <el-form-item label="支付时间">
                <daterange-picker
                    v-model:startTime="queryParams.start_time"
                    v-model:endTime="queryParams.end_time"
                />
            </el-form-item>
            <el-form-item label="退款状态">
                <el-select class="w-[280px]" v-model="queryParams.refund_status">
                    <el-option label="全部" value />
                    <el-option label="未退款" value="0"></el-option>
                    <el-option label="已退款" value="1"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="resetPage">查询</el-button>
                <el-button @click="resetParams">重置</el-button>
                <export-data
                    class="ml-2.5"
                    :fetch-fun="getMemberLists"
                    :params="queryParams"
                    :page-size="pager.size"
                />
            </el-form-item>
        </el-form>
    </el-card>
    <el-card class="!border-none mt-4" shadow="never">
        <el-table size="large" v-loading="pager.loading" :data="pager.lists">
            <el-table-column label="订单编号" prop="sn" min-width="100" />
            <el-table-column label="用户信息" min-width="180">
                <template #default="{ row }">
                    <el-popover placement="top" width="220px" trigger="hover">
                        <div class="flex items-center">
                            <span class="mr-4">头像: </span>
                            <el-avatar :size="50" :src="row?.avatar" />
                        </div>
                        <div class="mt-[20px]">
                            <span class="mr-4"> 昵称: </span>
                            <span>{{ row.nickname }}</span>
                        </div>
                        <div class="mt-[20px]">
                            <span class="mr-4">编号: </span>
                            <span>{{ row.user_sn }}</span>
                        </div>
                        <template #reference>
                            <div class="flex items-center">
                                <el-avatar class="flex-none" :size="50" :src="row?.avatar">
                                    {{ row.nickname }}
                                </el-avatar>
                                <div class="ml-[10px]">
                                    {{ row.nickname }}
                                </div>
                            </div>
                        </template>
                    </el-popover>
                </template>
            </el-table-column>
            <el-table-column label="购买套餐" prop="member_package" min-width="120" />
            <el-table-column label="实付金额" prop="order_amount" min-width="120" />
            <el-table-column label="支付状态" min-width="120">
                <template #default="{ row }">
                    <div>
                        {{ row.pay_status_text }}
                    </div>
                    <div v-if="row.refund_status != 0" class="text-warning">
                        {{ row.refund_status_text }}
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="支付时间" prop="pay_time_text" min-width="120" />
            <el-table-column label="操作" width="150" fixed="right">
                <template #default="{ row }">
                    <div class="flex">
                        <el-button
                            v-perms="['member.member_order/detail']"
                            type="primary"
                            link
                            @click="handleDetial(row.id)"
                        >
                            订单详情
                        </el-button>
                        <el-button
                            v-if="row.refund_status == 0"
                            v-perms="['member.member_order/refund']"
                            type="warning"
                            link
                            @click="handleRefund(row.id)"
                        >
                            退款
                        </el-button>
                    </div>
                </template>
            </el-table-column>
        </el-table>
        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </el-card>
    <Detial v-if="showEdit" ref="editRef"></Detial>
</template>

<script setup lang="ts">
import Detial from './detial.vue'
import feedback from '@/utils/feedback'
import { getMemberLists, MemberOrderRefund } from '@/api/order/vip'
import { usePaging } from '@/hooks/usePaging'
import { ClientMap } from '@/enums/appEnums'

/**
 * 处理弹出框
 */
const editRef = shallowRef<InstanceType<typeof Detial>>()
const showEdit = ref(false)
const handleDetial = async (id: any) => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open(id)
}
/**
 * 退款处理
 */
const handleRefund = async (id: any) => {
    await feedback.confirm('是否确认退款')
    await MemberOrderRefund({ id })
    getLists()
}
/**
 * 初始化数据
 */
const queryParams = reactive({
    user_info: '', //用户信息
    terminal: '', //订单来源
    refund_status: '', //退款状态
    start_time: '',
    end_time: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getMemberLists,
    params: queryParams
})

getLists()
</script>
