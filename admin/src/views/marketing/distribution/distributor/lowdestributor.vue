<template>
    <div>
        <!-- Header Start -->
        <el-card shadow="never" class="!border-none">
            <el-page-header content="下级人数列表" @back="$router.back()" />
        </el-card>
        <!-- Header End -->
        <!-- body -->
        <el-card shadow="never" class="!border-none mt-[10px]">
            <div class="text-xl font-medium">分销商信息</div>
            <el-form ref="formRef" class="ls-form mt-4" :model="pager">
                <el-form-item label="用户信息："> {{ pager.extend.info?.user_name }} </el-form-item>
                <el-form-item label="下级人数："> {{ pager.extend.info?.below_num }} </el-form-item>

                <el-form-item label="下级分销商人数：">{{ pager.extend.info?.below_distribution_num }}
                </el-form-item>
                <el-form-item label="下一级人数：">{{ pager.extend.info?.below_first_num }}（分销商：{{
                    pager.extend.info?.below_first_distribution_num
                }}人）
                </el-form-item>
                <el-form-item label="下二级人数">{{ pager.extend.info?.below_second_num }} （分销商：{{
                    pager.extend.info?.below_second_distribution_num
                }}人）</el-form-item>
            </el-form>
        </el-card>
        <el-card shadow="never" class="!border-none mt-[10px]">
            <div class="text-xl font-medium">下级列表</div>
            <el-form ref="formRef" class="mt-4" :model="queryParams" :inline="true">
                <el-form-item label="用户信息">
                    <el-input class="w-[280px]" v-model="queryParams.user_keyword" placeholder="请输入用户ID编号/用户昵称" clearable />
                </el-form-item>
                <el-form-item label="分销资格">
                    <el-select class="w-[280px]" v-model="queryParams.is_distribution">
                        <el-option value>全部</el-option>
                        <el-option value="1" label="未冻结">未冻结</el-option>
                        <el-option value="0" label="已冻结">已冻结</el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
            <el-tabs v-model="activeTab" @tab-change="handleTabChange">
                <el-tab-pane v-for="(item, index) in tabLists" :label="`${item.name}`" :name="index" :key="index">
                    <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                        <el-table-column label="用户昵称" prop="nickname" min-width="190">
                            <template #default="{ row }">
                                <div class="flex items-center">
                                    <el-avatar :src="row.avatar" :size="50" class="mr-2" />{{
                                        row.nickname
                                    }}
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column label="可提现佣金" prop="user_money" min-width="190" />
                        <el-table-column label="获得总佣金" prop="total_user_money" min-width="190" />
                        <el-table-column label="上级邀请人" prop="leader_nickname" min-width="190" />
                        <el-table-column label="分销商" prop="sn" min-width="190">
                            <template #default="{ row }">
                                <div>{{ row.is_distribution == 1 ? '已开通' : '未开通' }}</div>
                                <div>{{ row.distribution_time }}</div>
                            </template>
                        </el-table-column>
                        <el-table-column label="分销状态" prop="sn" min-width="190">
                            <template #default="{ row }">
                                <el-tag :type="row.distribution_status == 1 ? 'success' : 'warning'">{{
                                    row.distribution_status_desc }}</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="注册时间" prop="create_time" min-width="190" />
                    </el-table>
                </el-tab-pane>
            </el-tabs>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>
<script setup lang="ts">
import { getbelowLists } from '@/api/marketing/distribution'
import { usePaging } from '@/hooks/usePaging'

const route = useRoute()
const queryParams = reactive({
    id: route.query.id,
    user_keyword: '',
    is_distribution: '',
    type: ''
})
const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getbelowLists,
    params: queryParams
})
getLists()
const activeTab = ref(0)
const tabLists = ref([
    {
        name: '全部',
        type: '',
        numKey: 'all_num'
    },
    {
        name: '下一级人数',
        type: 1,
        numKey: 'first_num'
    },
    {
        name: '下二级人数',
        type: 2,
        numKey: 'second_num'
    }
])
const handleTabChange = (index: any) => {
    queryParams.type = tabLists.value[index].type as string
    resetPage()
}
</script>
