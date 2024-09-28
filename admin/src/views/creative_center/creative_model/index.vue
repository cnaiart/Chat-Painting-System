<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="模型名称">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.name"
                        placeholder="请输入模型名称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="所属类目">
                    <el-select class="w-[280px]" v-model="queryParams.category_id">
                        <el-option label="全部" value="" />
                        <el-option
                            v-for="(item, index) in categoryList"
                            :key="index"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="模型状态">
                    <el-select class="w-[280px]" v-model="queryParams.status">
                        <el-option label="全部" value="" />
                        <el-option label="开启" :value="1" />
                        <el-option label="关闭" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                    <export-data
                        class="ml-2.5"
                        :fetch-fun="creationModelList"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div>
                <router-link
                    v-perms="['creation.creationModel/add', 'creation.creationModel/add:edit']"
                    :to="getRoutePath('creation.creationModel/add:edit')"
                >
                    <el-button type="primary">
                        <template #icon>
                            <icon name="el-icon-Plus" />
                        </template>
                        新增创作模型
                    </el-button>
                </router-link>

                <el-button
                    v-perms="['creation.creationModel/del']"
                    class="ml-2"
                    type="default"
                    :plain="true"
                    :disabled="!multipleSelection.length"
                    @click="handleDelete(multipleSelection.map((item) => item.id))"
                >
                    批量删除
                </el-button>
            </div>
            <el-table
                size="large"
                class="mt-4"
                v-loading="pager.loading"
                :data="pager.lists"
                @selection-change="handleSelectionChange"
            >
                <el-table-column type="selection" width="55" />
                <el-table-column label="图标" min-width="100">
                    <template #default="{ row }">
                        <el-image :src="row.image" class="w-[44px] h-[44px]"></el-image>
                    </template>
                </el-table-column>
                <el-table-column label="模型名称" prop="name" min-width="120" />
                <el-table-column label="模型描述" prop="tips" min-width="150" />
                <el-table-column label="所属类目" prop="category_name" min-width="120" />
                <el-table-column
                    label="状态"
                    min-width="100"
                    v-perms="['creation.creationModel/status']"
                >
                    <template #default="{ row }">
                        <el-switch
                            @change="changeStatus(row.id)"
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="访问数据/次" min-width="160">
                    <template #default="{ row }">
                        <div>今日访问：{{ row.day_use_count }}</div>
                        <div>累计访问：{{ row.all_use_count }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="使用人数" min-width="160">
                    <template #default="{ row }">
                        <div>今日使用：{{ row.day_user_count }}</div>
                        <div>累计使用：{{ row.all_user_count }}</div>
                    </template>
                </el-table-column>

                <el-table-column label="排序" prop="sort" min-width="120" />
                <el-table-column label="创建时间" prop="create_time" min-width="100" />
                <el-table-column label="操作" width="150" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link>
                            <router-link
                                v-perms="[
                                    'creation.creationModel/edit',
                                    'creation.creationModel/add:edit'
                                ]"
                                :to="{
                                    path: getRoutePath('creation.creationModel/add:edit'),
                                    query: {
                                        id: row.id,
                                        mode: 'edit'
                                    }
                                }"
                            >
                                编辑
                            </router-link>
                        </el-button>
                        <el-button
                            v-perms="['creation.creationModel/del']"
                            type="danger"
                            link
                            @click="handleDelete([row.id])"
                        >
                            删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>
<script lang="ts" setup name="problemExample">
import { usePaging } from '@/hooks/usePaging'
import { creationCategoryList } from '@/api/creative_center/creative_category'
import { getRoutePath } from '@/router'
import {
    creationModelList,
    delCreationModel,
    changeCreationModelStatus
} from '@/api/creative_center/creative_model'
import feedback from '@/utils/feedback'

//分类列表
const categoryList: any = ref([])
//搜索参数
const queryParams = reactive({
    name: '',
    category_id: '',
    status: ''
})
const multipleSelection = ref<any[]>([])

//删除
const handleDelete = async (id: number[]) => {
    await feedback.confirm('确定要删除？')
    await delCreationModel({ id })
    getLists()
}

const handleSelectionChange = (val: any[]) => {
    multipleSelection.value = val
}

//获取分类列表
const getCategoryList = async () => {
    const { lists } = await creationCategoryList()
    categoryList.value = lists
}

//修改状态
const changeStatus = async (id: any) => {
    await changeCreationModelStatus({ id })
    getLists()
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: creationModelList,
    params: queryParams
})

getLists()
getCategoryList()
</script>
