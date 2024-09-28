<template>
    <div>
        <el-card shadow="never" class="!border-none mt-[10px]">
            <el-form ref="formRef" class="mt-4" :model="queryParams" :inline="true">
                <el-form-item label="订单编号">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.order_sn"
                        placeholder="请输入订单编号"
                        clearable
                    />
                </el-form-item>
                <el-form-item label="用户信息">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.user_info"
                        placeholder="请输入ID编号/昵称/手机号码"
                        clearable
                    />
                </el-form-item>
                <el-form-item label="分销商信息">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.distributor_info"
                        placeholder="请输入分销商ID/昵称/手机号码"
                        clearable
                    />
                </el-form-item>
                <el-form-item label="支付时间">
                    <daterange-picker
                        class="w-[280px]"
                        v-model:startTime="queryParams.start_time"
                        v-model:endTime="queryParams.end_time"
                    />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                    <export-data
                        class="ml-2.5"
                        :fetch-fun="getorderLists"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card shadow="never" class="!border-none mt-[10px]">
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="用户昵称" min-width="190">
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
                                    <el-avatar :size="50" :src="row?.avatar">
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
                <el-table-column label="订单编号" min-width="190">
                    <template #default="{ row }">
                        <div>{{ row.order_type_desc }}</div>
                        <div>{{ row.order_sn }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="实付金额" min-width="190">
                    <template #default="{ row }"> ￥{{ row.order_amount }} </template>
                </el-table-column>
                <el-table-column label="一级分销奖励" min-width="190">
                    <template #default="{ row }">
                        <template v-if="row.first_name">
                            <div>分销商：{{ row.first_name || '-' }}</div>
                            <div>佣金比例：{{ row.first_ratio + '%' || '-' }}</div>
                            <div>分销奖励：￥{{ row.first_reward || '-' }}</div>
                        </template>
                    </template>
                </el-table-column>
                <el-table-column label="二级分销奖励" min-width="190">
                    <template #default="{ row }">
                        <template v-if="row.second_name">
                            <div>分销商：{{ row.second_name || '-' }}</div>
                            <div>佣金比例：{{ row.second_ratio + '%' || '-' }}</div>
                            <div>分销奖励：￥{{ row.second_reward || '-' }}</div>
                        </template>
                    </template>
                </el-table-column>
                <el-table-column label="支付时间" prop="pay_time_desc" min-width="190" />
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>
<script setup language="ts">
import { usePaging } from '@/hooks/usePaging'
import { getorderLists } from '@/api/marketing/distribution'
import useAppStore from '@/stores/modules/app'
const { getImageUrl } = useAppStore()
const queryParams = reactive({
    order_sn: '',
    user_info: '',
    distributor_info: '',
    start_time: '',
    end_time: ''
})
const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getorderLists,
    params: queryParams
})
getLists()
</script>
