<template>
    <el-card shadow="never" class="!border-none mt-4">
        <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
            <el-form-item label="类别名称">
                <el-input
                    class="w-[280px]"
                    v-model="queryParams.name"
                    placeholder="请输入类别名称"
                    clearable
                    @keyup.enter="resetPage"
                />
            </el-form-item>
            <el-form-item label="类别状态">
                <el-select class="w-[280px]" v-model="queryParams.status">
                    <el-option label="全部" value=""></el-option>
                    <el-option label="开启" value="1"></el-option>
                    <el-option label="关闭" value="0"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="resetPage">查询</el-button>
                <el-button @click="resetParams">重置</el-button>
                <export-data
                    class="ml-2.5"
                    :fetch-fun="skillCategoryLists"
                    :params="queryParams"
                    :page-size="pager.size"
                />
            </el-form-item>
        </el-form>
    </el-card>
    <el-card class="!border-none mt-4" shadow="never">
        <el-button
            v-perms="['skill.skillCategory/add']"
            type="primary"
            class="mb-4"
            @click="handleAdd"
        >
            新增技能类别
        </el-button>
        <el-table size="large" v-loading="pager.loading" :data="pager.lists">
            <el-table-column label="类别名称" prop="name" min-width="100" />
            <el-table-column label="被使用数" min-width="120" prop="skill_count" />
            <el-table-column label="状态" min-width="100" v-perms="['skill.skillCategory/status']">
                <template #default="{ row }">
                    <el-switch
                        @change="changeStatus(row.id)"
                        v-model="row.status"
                        :active-value="1"
                        :inactive-value="0"
                    />
                </template>
            </el-table-column>
            <el-table-column label="排序" prop="sort" min-width="120" />
            <el-table-column label="创建时间" prop="create_time" min-width="100" />
            <el-table-column label="操作" width="150" fixed="right">
                <template #default="{ row }">
                    <el-button
                        v-perms="['skill.skillCategory/edit']"
                        type="primary"
                        link
                        @click="handleEdit(row)"
                    >
                        编辑
                    </el-button>
                    <el-button
                        v-perms="['skill.skillCategory/del']"
                        type="danger"
                        link
                        @click="handleDelete(row.id)"
                    >
                        删除
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
        <!-- <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div> -->
    </el-card>
    <Edit v-if="showEdit" ref="editRef" @success="getLists" @close="showEdit = false"></Edit>
</template>
<script setup lang="ts">
import { usePaging } from '@/hooks/usePaging'
import Edit from './edit.vue'
import { skillCategoryLists, delSkillCategory, changeSkillCategoryStatus } from '@/api/skill/type'
import feedback from '@/utils/feedback'
const editRef = shallowRef<InstanceType<typeof Edit>>()
//搜索参数
const queryParams = reactive({
    name: '',
    status: ''
})
const showEdit = ref(false)
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
const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除？')
    await delSkillCategory({ id })
    getLists()
}

//修改状态
const changeStatus = (id: any) => {
    changeSkillCategoryStatus({ id })
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: skillCategoryLists,
    params: queryParams
})

getLists()
</script>
