<template>
    <div>
        <!-- Header Start -->
        <el-card shadow="never" class="!border-none">
            <el-page-header content="分销订单" @back="$router.back()" />
        </el-card>
        <!-- Header End -->
        <!-- body -->
        <el-card shadow="never" class="!border-none mt-[10px]">
            <div class="text-xl font-medium">分销商信息</div>
            <el-form ref="formRef" class="ls-form mt-4" :model="formData" label-width="120px">
                <el-form-item label="用户信息："> 1 </el-form-item>
            </el-form>
        </el-card>
        <el-card shadow="never" class="!border-none mt-[10px]">
            <div class="text-xl font-medium">分销订单</div>
            <el-form ref="formRef" class="mt-4" :model="queryParams" :inline="true">
                <el-form-item label="用户信息">
                    <el-input class="w-[280px]" v-model="queryParams" placeholder="请输入用户ID编号/用户昵称" clearable />
                </el-form-item>
                <el-form-item label="订单类型">
                    <el-select class="w-[280px]">
                        <el-option value="">全部</el-option>
                        <el-option value="">充值订单</el-option>
                        <el-option value="">开通会员订单</el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>

            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="用户昵称" prop="sn" min-width="190" />
                <el-table-column label="可提现佣金" prop="sn" min-width="190" />
                <el-table-column label="获得总佣金" prop="sn" min-width="190" />
                <el-table-column label="上级分销商" prop="sn" min-width="190" />
                <el-table-column label="分销资格" prop="sn" min-width="190" />
                <el-table-column label="分销状态" prop="sn" min-width="190" />
                <el-table-column label="注册时间" prop="sn" min-width="190" />
            </el-table>
        </el-card>
    </div>
</template>
<script setup language="ts">
const pager = ref({
    loading: false,
    lists: []
})
</script>
