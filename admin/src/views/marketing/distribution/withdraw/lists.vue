<template>
    <div>
        <el-card shadow="never" class="!border-none mt-[10px]">
            <el-form ref="formRef" class="mt-4" :model="queryParams" :inline="true">
                <el-form-item label="用户信息">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.user_info"
                        placeholder="请输入用户ID/昵称/手机号码"
                        clearable
                    />
                </el-form-item>
                <el-form-item label="提现方式">
                    <el-select class="w-[280px]" v-model="queryParams.type">
                        <el-option :value="1" label="支付宝"></el-option>
                        <el-option :value="2" label="微信零钱"></el-option>
                        <el-option :value="3" label="微信收款码"></el-option>
                        <el-option :value="3" label="支付宝收款码"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="申请时间">
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
                        :fetch-fun="getwithdrawLists"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card shadow="never" class="!border-none mt-[10px]">
            <!-- 选项卡 -->
            <el-tabs v-model="queryParams.status" @tab-change="handleTabChange">
                <template v-for="(tabItem, tabIndex) in tabsParams.TabsEnumMap" :key="tabIndex">
                    <el-tab-pane
                        :label="`${tabItem.label}(${pager.extend[tabItem?.type] || 0})`"
                        :name="tabItem.name"
                    />
                </template>
            </el-tabs>

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
                <el-table-column label="提现金额" prop="money" min-width="190" />
                <el-table-column label="手续费" prop="handling_fee" min-width="190">
                    <template #default="{ row }">
                        {{ row.handling_fee }}({{ row.handling_fee_ratio }})
                    </template>
                </el-table-column>
                <el-table-column label="到账金额" prop="left_money" min-width="190" />
                <el-table-column label="提现方式" prop="type_desc" min-width="190" />
                <el-table-column label="提现状态" min-width="190">
                    <template #default="{ row }">
                        <el-tag type="primary" v-if="row.status == 1">{{ row.status_desc }}</el-tag>
                        <el-tag type="warning" v-if="row.status == 2">{{ row.status_desc }}</el-tag>
                        <el-tag type="success" v-if="row.status == 3">{{ row.status_desc }}</el-tag>
                        <el-tag type="danger" v-if="row.status == 4">{{ row.status_desc }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="申请时间" prop="create_time" min-width="190" />
                <el-table-column label="操作" min-width="190" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-perms="['distribution.withdraw/detail']"
                            @click="handledetial(row.id)"
                            type="primary"
                            link
                        >
                            详情
                        </el-button>
                        <el-button
                            v-perms="['distribution.withdraw/verify']"
                            @click="handlecheck(row.id)"
                            type="primary"
                            link
                            v-if="row.status == 1"
                        >
                            审核
                        </el-button>
                        <el-button
                            v-perms="['distribution.withdraw/transfer']"
                            @click="handletransfer(row.id)"
                            type="primary"
                            link
                            v-if="row.status == 2"
                        >
                            转账
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
    <detial ref="detialPopref" v-if="showdetial" @close="showdetial = false"></detial>
    <check ref="checkPopref" v-if="showcheck" @close="handlecheckclose"></check>
    <transfer ref="transferPopref" v-if="showtransfer" @close="handlletransferclose"></transfer>
</template>
<script setup lang="ts">
import check from './check.vue'
import transfer from './transfer.vue'
import detial from './detial.vue'
import { getwithdrawLists } from '@/api/marketing/distribution'
import { usePaging } from '@/hooks/usePaging'

const queryParams = reactive({
    status: '',
    user_info: '',
    type: '',
    start_time: '',
    end_time: ''
})
const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getwithdrawLists,
    params: queryParams
})
getLists()
const activeTab = ref(0)

// Tab类型
enum TabsEnum {
    ALL_NUM = 'all_num',
    WAIT_NUM = 'wait_num',
    ING_NUM = 'ing_num',
    SUCCESS_NUM = 'success_num',
    FAIL_NUM = 'fail_num'
}

const tabsParams = reactive({
    TabsEnumMap: [
        {
            label: '全部',
            name: '',
            type: TabsEnum.ALL_NUM
        },
        {
            label: '待审核',
            name: 1,
            type: TabsEnum.WAIT_NUM
        },
        {
            label: '提现中',
            name: 2,
            type: TabsEnum.ING_NUM
        },
        {
            label: '提现成功',
            name: 3,
            type: TabsEnum.SUCCESS_NUM
        },
        {
            label: '提现失败',
            name: 4,
            type: TabsEnum.FAIL_NUM
        }
    ]
})
const handleTabChange = (name: string) => {
    queryParams.status = name
    resetPage()
}
//弹框ref审核
const checkPopref = shallowRef()
//打开弹框
const showcheck = ref(false)
const handlecheck = async (val: any) => {
    showcheck.value = true
    await nextTick()
    checkPopref.value?.open(val)
}
//转账弹框
const transferPopref = shallowRef()
//打开弹框
const showtransfer = ref(false)
const handletransfer = async (val: any) => {
    showtransfer.value = true
    await nextTick()
    transferPopref.value?.open(val)
}
const detialPopref = shallowRef()
//打开弹框
const showdetial = ref(false)
const handledetial = async (val: any) => {
    showdetial.value = true
    await nextTick()
    detialPopref.value?.open(val)
}
const handlecheckclose = () => {
    showcheck.value = false
    getLists()
}
const handlletransferclose = () => {
    showtransfer.value = false
    getLists()
}
</script>
