<template>
    <div>
        <el-card shadow="never" class="!border-none mt-[10px]">
            <el-form ref="formRef" class="mt-4" :model="queryParams" :inline="true">
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
                        placeholder="请输入邀请人ID编号/用户昵称"
                        clearable
                    />
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
                        :fetch-fun="applylists"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card shadow="never" class="!border-none mt-[10px]">
            <el-tabs v-model="activeTab" @tab-change="handleTabChange">
                <el-tab-pane
                    v-for="(item, index) in tabLists"
                    :label="`${item.name}(${pager.extend[item.numKey] || 0})`"
                    :name="index"
                    :key="index"
                >
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
                        <el-table-column label="姓名" prop="name" min-width="190" />
                        <el-table-column label="手机号码" prop="mobile" min-width="190" />
                        <el-table-column
                            label="上级邀请人"
                            prop="leader_nickname"
                            min-width="190"
                        />
                        <el-table-column label="审核状态" min-width="190">
                            <template #default="{ row }">
                                <el-tag type="success" v-if="row.status == 2">审核通过</el-tag>
                                <el-tag type="warning" v-if="row.status == 1">待审核</el-tag>
                                <el-tag type="danger" v-if="row.status == 3">审核拒绝</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="申请时间" prop="create_time" min-width="190" />
                        <el-table-column label="操作" min-width="190" fixed="right">
                            <template #default="{ row }">
                                <el-button
                                    v-perms="['distribution.distributionApply/detail']"
                                    @click="handledetial(row.id)"
                                    type="primary"
                                    link
                                >
                                    详情
                                </el-button>
                                <el-button
                                    v-perms="['distribution.distributionApply/audit']"
                                    v-if="row.status == 1"
                                    @click="handlecheck(row.id)"
                                    type="primary"
                                    link
                                >
                                    审核
                                </el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-tab-pane>
            </el-tabs>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
    <detial ref="detialPopref" v-if="showdetial" @close="showdetial = false"></detial>
    <check ref="checkPopref" v-if="showcheck" @close="handlecheckclose"></check>
</template>
<script setup lang="ts">
import { applylists } from '@/api/marketing/distribution'
import { usePaging } from '@/hooks/usePaging'

import detial from './detial.vue'
import check from './check.vue'
const queryParams = reactive({
    user_keyword: '',
    leader_keyword: '',
    start_time: '',
    end_time: '',
    status: ''
})
const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: applylists,
    params: queryParams
})
getLists()
const handleTabChange = (index: any) => {
    queryParams.status = tabLists.value[index].type as string
    resetPage()
}
const activeTab = ref(0)
const tabLists = ref([
    {
        name: '全部',
        type: '',
        numKey: 'all_count'
    },
    {
        name: '待审核',
        type: '1',
        numKey: 'audit_count'
    },
    {
        name: '审核通过',
        type: '2',
        numKey: 'pass_count'
    },
    {
        name: '审核拒绝',
        type: '3',
        numKey: 'refuse_count'
    }
])
//弹框ref
const detialPopref = shallowRef()
//打开弹框
const showdetial = ref(false)
const handledetial = async (val: any) => {
    showdetial.value = true
    await nextTick()
    detialPopref.value?.open(val)
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
const handlecheckclose = () => {
    showcheck.value = false
    getLists()
}
</script>
