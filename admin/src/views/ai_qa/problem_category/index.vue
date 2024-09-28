<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="分类名称">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.name"
                        placeholder="请输入分类名称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="类目状态">
                    <el-select class="w-[280px]" v-model="queryParams.status">
                        <el-option label="全部" value="" />
                        <el-option label="开启" :value="1" />
                        <el-option label="关闭" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div>
                <el-button
                    v-perms="['question.questionCategory/add']"
                    type="primary"
                    @click="openPop('add')"
                >
                    <template #icon>
                        <icon name="el-icon-Plus" />
                    </template>
                    新增
                </el-button>
            </div>

            <el-table size="large" class="mt-4" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="分类图标" min-width="120">
                    <template #default="{ row }">
                        <el-image
                            v-if="row.image"
                            :src="row.image"
                            class="w-[47px] h-[47px]"
                        ></el-image>
                    </template>
                </el-table-column>
                <el-table-column label="示例类目" prop="name" min-width="120" />
                <el-table-column label="示例数量" min-width="100" prop="sample_count" />
                <el-table-column
                    label="状态"
                    min-width="100"
                    v-perms="['question.questionCategory/status']"
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
                <el-table-column label="排序" prop="sort" min-width="120" />
                <el-table-column label="创建时间" prop="create_time" min-width="100" />

                <el-table-column label="操作" width="150" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-perms="['question.questionCategory/edit']"
                            type="primary"
                            link
                            @click="openPop('edit', row)"
                        >
                            编辑
                        </el-button>
                        <el-button
                            v-perms="['question.questionCategory/del']"
                            type="danger"
                            link
                            @click="handleDelete(row.id, row.sample_count)"
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
        <edit-popup v-if="showEdit" ref="editRef" @success="getLists" />
    </div>
</template>
<script lang="tsx" setup name="problemExample">
import { usePaging } from '@/hooks/usePaging'
import EditPopup from './edit.vue'
import {
    getQuestionCategoryList,
    delQusetionCategory,
    editQusetionCategoryStatus
} from '@/api/ai_qa/problem_category'
import feedback from '@/utils/feedback'
import { ifStatement } from '@babel/types'

//弹框ref
const editRef = shallowRef<InstanceType<typeof EditPopup>>()
//搜索参数
const queryParams = reactive({
    name: '',
    status: ''
})
//是/否显示编辑弹框
const showEdit = ref(true)

//打开弹框
const openPop = (type: string, value: any = {}) => {
    editRef.value?.open(type, value)
}

//删除
const handleDelete = async (id: number, sample_count: number) => {
    if (sample_count != 0) {
        feedback.msgWarning('请解除示例后再试！')
        return
    }
    await feedback.confirm('确定要删除？')
    await delQusetionCategory({ id })
    getLists()
}

//修改状态
const changeStatus = (id: any) => {
    editQusetionCategoryStatus({ id })
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getQuestionCategoryList,
    params: queryParams
})

getLists()
</script>
