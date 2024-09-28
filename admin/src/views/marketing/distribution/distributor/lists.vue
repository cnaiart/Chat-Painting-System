<template>
    <el-card shadow="never" class="!border-none">
        <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
            <el-form-item label="用户信息">
                <el-input
                    class="w-[280px]"
                    v-model="queryParams.user_keyword"
                    placeholder="请输入用户ID编号/用户昵称"
                    clearable
                />
            </el-form-item>
            <el-form-item label="邀请人">
                <el-input
                    class="w-[280px]"
                    v-model="queryParams.leader_keyword"
                    placeholder="请输入邀请人ID/昵称/手机号码"
                    clearable
                />
            </el-form-item>
            <el-form-item label="分销状态">
                <el-select class="w-[280px]" v-model="queryParams.distribution_status">
                    <el-option value>全部</el-option>
                    <el-option :value="1" label="未冻结">未冻结</el-option>
                    <el-option :value="0" label="已冻结">已冻结</el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="成为分销商时间">
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
                    :fetch-fun="getdistributorLists"
                    :params="queryParams"
                    :page-size="pager.size"
                />
            </el-form-item>
        </el-form>
    </el-card>
    <el-card shadow="never" class="!border-none mt-2">
        <el-button v-perms="['distribution.distributor/add']" type="primary" @click="handleadd">
            开通分销商
        </el-button>
        <el-table size="large" v-loading="pager.loading" :data="pager.lists" class="mt-2">
            <el-table-column label="用户昵称" prop="nickname" min-width="190">
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
            <el-table-column label="可提现佣金" prop="user_money" min-width="150" />
            <el-table-column label="获得总佣金" prop="total_user_money" min-width="150" />
            <el-table-column label="下级人数" prop="below_num" min-width="120" />
            <el-table-column label="上级邀请人" prop="leader_nickname" min-width="150">
                <template #default="{ row }">
                    {{ row.leader_nickname || '系统' }}
                </template>
            </el-table-column>
            <el-table-column label="分销状态" prop="distribution_status_desc" min-width="120">
                <template #default="{ row }">
                    <el-tag :type="row.distribution_status == 1 ? 'success' : 'warning'">{{
                        row.distribution_status_desc
                    }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="成为分销商时间" prop="distribution_time" min-width="180" />
            <el-table-column label="操作" min-width="140" fixed="right">
                <template #default="{ row }">
                    <router-link
                        v-perms="['distribution.distributor/detail']"
                        :to="{
                            path: getRoutePath('distribution.distributor/detail'),
                            query: {
                                id: row.id
                            }
                        }"
                    >
                        <el-button link type="primary">详情</el-button>
                    </router-link>
                    <el-button
                        v-perms="['distribution.distributor/status']"
                        link
                        type="primary"
                        @click="handlechange(row.id, row.distribution_status)"
                    >
                        {{ row.distribution_status == 1 ? '冻结' : '取消冻结' }}
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </el-card>
    <addPop ref="addPopref" v-if="showadd" @close="handlecolse"></addPop>
</template>
<script setup lang="ts">
import { getRoutePath } from '@/router'
import addPop from './addPop.vue'
import { usePaging } from '@/hooks/usePaging'
import { getdistributorLists, changedistributor } from '@/api/marketing/distribution'
import feedback from '@/utils/feedback'

const queryParams = reactive({
    user_keyword: '',
    leader_keyword: '',
    distribution_status: '',
    start_time: '',
    end_time: ''
})
const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getdistributorLists,
    params: queryParams
})
getLists()
//冻结
const handlechange = async (id: any, type: any) => {
    await feedback.confirm(`确定${type == 1 ? '冻结' : '恢复'}该用户分销资格？`)
    await changedistributor({ id })
    getLists()
}
//弹框ref
const addPopref = shallowRef<InstanceType<typeof addPop>>()
//打开弹框
const showadd = ref(false)
const handleadd = async () => {
    showadd.value = true
    await nextTick()
    addPopref.value?.open()
}
const handlecolse = () => {
    showadd.value = false
    getLists()
}
</script>
