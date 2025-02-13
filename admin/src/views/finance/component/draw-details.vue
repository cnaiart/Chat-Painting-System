<template>
    <div>
        <el-form ref="formRef" class="mb-[-16px] mt-[16px]" :model="queryParams" :inline="true">
            <el-form-item label="用户信息">
                <el-input
                    class="w-[280px]"
                    v-model="queryParams.user_info"
                    placeholder="请输入用户编号/昵称/手机号"
                    clearable
                    @keyup.enter="resetPage"
                />
            </el-form-item>
            <el-form-item label="变动类型">
                <el-select class="w-[280px]" v-model="queryParams.change_type">
                    <el-option label="全部" value />
                    <el-option
                        v-for="(value, key) in optionsData.change_type"
                        :key="key"
                        :label="value"
                        :value="key"
                    />
                </el-select>
            </el-form-item>
            <el-form-item label="记录时间">
                <daterange-picker
                    v-model:startTime="queryParams.start_time"
                    v-model:endTime="queryParams.end_time"
                />
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="resetPage">查询</el-button>
                <el-button @click="resetParams">重置</el-button>
                <export-data
                    class="ml-2.5"
                    :fetch-fun="accountLog"
                    :params="queryParams"
                    :page-size="pager.size"
                />
            </el-form-item>
        </el-form>
        <el-table class="mt-[24px]" size="large" v-loading="pager.loading" :data="pager.lists">
            <el-table-column label="用户编号" prop="sn" min-width="100" />
            <el-table-column label="用户昵称" min-width="160">
                <template #default="{ row }">
                    <div class="flex items-center">
                        <image-contain
                            class="flex-none mr-2"
                            :src="row.avatar"
                            :width="40"
                            :height="40"
                            preview-teleported
                            fit="contain"
                        />
                        {{ row.nickname }}
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="变动条数" prop="change_amount" min-width="100">
                <template #default="{ row }">
                    <span :class="{ 'text-error': row.action == 2 }">
                        {{ row.change_amount }}条
                    </span>
                </template>
            </el-table-column>
            <el-table-column label="剩余条数" prop="left_amount" min-width="100">
                <template #default="{ row }"> {{ row.left_amount }}条 </template>
            </el-table-column>
            <el-table-column label="变动类型" prop="change_type_desc" min-width="120" />

            <el-table-column label="记录来源" prop="source_sn" min-width="100" />
            <el-table-column label="记录时间" prop="create_time" min-width="120" />
        </el-table>
        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </div>
</template>
<script lang="ts" setup name="articleLists">
import { getDrawChangeType, accountLog } from '@/api/finance'
import { useDictOptions } from '@/hooks/useDictOptions'
import { usePaging } from '@/hooks/usePaging'
const queryParams = reactive({
    type: 'draw',
    user_info: '',
    change_type: '',
    start_time: '',
    end_time: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: accountLog,
    params: queryParams
})

const { optionsData } = useDictOptions<{
    change_type: Record<number, string>
}>({
    change_type: {
        api: getDrawChangeType
    }
})

getLists()
</script>
