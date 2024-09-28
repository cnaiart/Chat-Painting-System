<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="内容搜索">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.content"
                        placeholder="请输入内容关键词"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="示例类目">
                    <el-select class="w-[280px]" v-model="queryParams.category_id">
                        <el-option
                            v-for="(item, key) in categoryList"
                            :key="key"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="示例状态">
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
                        :fetch-fun="getQuestionSampleList"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div>
                <el-button
                    v-perms="['question.questionSample/add']"
                    type="primary"
                    @click="openPop('add')"
                >
                    <template #icon>
                        <icon name="el-icon-Plus" />
                    </template>
                    新增
                </el-button>
                <el-button
                    v-perms="['question.questionSample/del']"
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
                <el-table-column label="示例类目" prop="category_name" min-width="120" />
                <el-table-column label="示例内容" prop="content" min-width="100" />
                <el-table-column
                    label="状态"
                    min-width="100"
                    v-perms="['question.questionSample/status']"
                >
                    <template #default="{ row }">
                        <el-switch
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                            @change="changeStatus(row.id)"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="排序" prop="sort" min-width="120" />
                <el-table-column label="创建时间" prop="create_time" min-width="100" />
                <el-table-column label="操作" width="150" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-perms="['question.questionSample/edit']"
                            type="primary"
                            link
                            @click="openPop('edit', row)"
                        >
                            编辑
                        </el-button>
                        <el-button
                            v-perms="['question.questionSample/del']"
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
        <edit-popup v-if="showEdit" ref="editRef" @success="getLists" />
    </div>
</template>
<script lang="ts" setup name="problemExample">
import { usePaging } from '@/hooks/usePaging'
import {
    getQuestionSampleList,
    delQusetionSample,
    editQusetionSampleStatus
} from '@/api/ai_qa/problem_example'
import { getQuestionCategoryList } from '@/api/ai_qa/problem_category'
import EditPopup from './edit.vue'
import feedback from '@/utils/feedback'

//弹框ref
const editRef = shallowRef<InstanceType<typeof EditPopup>>()
const showEdit = ref(true)
//搜索参数
const queryParams = reactive({
    content: '',
    category_id: '',
    status: ''
})
//分类列表
const categoryList: any = ref([])
const multipleSelection = ref<any[]>([])

//获取分类列表
const getCategoryList = async () => {
    const { lists } = await getQuestionCategoryList()
    categoryList.value = lists
}

//打开弹框
const openPop = (type: string, value: any = {}) => {
    editRef.value?.open(type, value)
}

const handleSelectionChange = (val: any[]) => {
    multipleSelection.value = val
}

//删除
const handleDelete = async (id: number[]) => {
    await feedback.confirm('确定要删除？')
    await delQusetionSample({ id })
    getLists()
}

//修改状态
const changeStatus = (id: any) => {
    editQusetionSampleStatus({ id })
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getQuestionSampleList,
    params: queryParams
})

getLists()
getCategoryList()
</script>
