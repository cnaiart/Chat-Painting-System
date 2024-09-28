<template>
    <el-card shadow="never" class="!border-none mt-4">
        <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
            <el-form-item label="技能名称">
                <el-input
                    class="w-[280px]"
                    v-model="queryParams.name"
                    placeholder="请输入技能名称"
                    clearable
                    @keyup.enter="resetPage"
                />
            </el-form-item>
            <el-form-item label="所属类目">
                <el-select class="w-[280px]" v-model="queryParams.category_id">
                    <el-option label="全部" value></el-option>
                    <el-option
                        v-for="(item, index) in categoryList"
                        :key="index"
                        :label="item.name"
                        :value="item.id"
                    ></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="技能状态">
                <el-select class="w-[280px]" v-model="queryParams.status">
                    <el-option label="全部" value></el-option>
                    <el-option label="开启" value="1"></el-option>
                    <el-option label="关闭" value="0"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="resetPage">查询</el-button>
                <el-button @click="resetParams">重置</el-button>
                <export-data
                    class="ml-2.5"
                    :fetch-fun="skillModelLists"
                    :params="queryParams"
                    :page-size="pager.size"
                />
            </el-form-item>
        </el-form>
    </el-card>
    <el-card class="!border-none mt-4" shadow="never">
        <div class="mb-4">
            <el-button type="primary" v-perms="['skill.skill/add']" @click="handleAdd">
                新增技能
            </el-button>
            <el-button
                v-perms="['skill.skill/del']"
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
            v-loading="pager.loading"
            :data="pager.lists"
            @selection-change="handleSelectionChange"
        >
            <el-table-column type="selection" width="55" />
            <el-table-column label="图标" prop="sn" min-width="100">
                <template #default="{ row }">
                    <el-image :src="row.image" class="w-[44px] h-[44px]"></el-image>
                </template>
            </el-table-column>
            <el-table-column label="技能名称" prop="name" min-width="120" />
            <el-table-column label="描述" prop="describe" min-width="180" />
            <el-table-column label="所属类目" prop="category_name" min-width="180" />
            <el-table-column label="状态" min-width="100" v-perms="['skill.skill/status']">
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
            <el-table-column label="排序" prop="sort" min-width="100" />
            <el-table-column label="创建时间" prop="create_time" min-width="180" />
            <el-table-column label="操作" min-width="180">
                <template #default="{ row }">
                    <el-button
                        v-perms="['skill.skill/edit']"
                        type="primary"
                        link
                        @click="handleEdit(row)"
                    >
                        编辑
                    </el-button>
                    <el-button
                        v-perms="['skill.skill/del']"
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
    <Edit v-if="showEdit" ref="editRef" @success="getLists" @close="showEdit = false"></Edit>
</template>
<script setup lang="ts">
import { usePaging } from '@/hooks/usePaging'
import Edit from './edit.vue'
import { skillCategoryLists } from '@/api/skill/type'
import { skillModelLists, delSkillModel, changeSkillModelStatus } from '@/api/skill/admin'
import feedback from '@/utils/feedback'
const editRef = shallowRef<InstanceType<typeof Edit>>()
//搜索参数
const queryParams = reactive({
    name: '',
    category_id: '',
    status: ''
})
const showEdit = ref(false)

//分类列表
const categoryList: any = ref([])
const multipleSelection = ref<any[]>([])

const handleSelectionChange = (val: any[]) => {
    multipleSelection.value = val
}

//添加
const handleAdd = async () => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('add')
}
//编辑
const handleEdit = async (data: any) => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('edit')
    editRef.value?.setFormData(data)
}
//删除
const handleDelete = async (id: number[]) => {
    await feedback.confirm('确定要删除？')
    await delSkillModel({ id })
    getLists()
}

//获取分类列表
const getCategoryList = async () => {
    const { lists } = await skillCategoryLists()
    categoryList.value = lists
}

//修改状态
const changeStatus = (id: any) => {
    changeSkillModelStatus({ id })
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: skillModelLists,
    params: queryParams
})

getLists()
getCategoryList()
</script>
