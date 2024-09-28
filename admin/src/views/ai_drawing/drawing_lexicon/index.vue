<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="内容搜索">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.prompt"
                        placeholder="请输入内容关键词"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="示例类目">
                    <el-cascader
                        class="w-80"
                        v-model="queryParams.category_id"
                        :options="categoryList"
                        :props="props"
                        :clearable="true"
                    />
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
                        :fetch-fun="getPromptList"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div>
                <el-button
                    v-perms="['draw.draw_prompt/add']"
                    type="primary"
                    @click="openPop('add')"
                >
                    <template #icon>
                        <icon name="el-icon-Plus" />
                    </template>
                    新增
                </el-button>
                <el-button
                    v-perms="['draw.draw_prompt/delete']"
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
                <el-table-column label="关键词英文" prop="prompt_en" min-width="100" />
                <el-table-column label="关键词中文" prop="prompt" min-width="100" />
                <el-table-column label="所属类别" prop="cate_name" min-width="120" />
                <el-table-column label="状态" min-width="100" v-perms="['draw.draw_prompt/status']">
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
                <el-table-column label="操作" width="150" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-perms="['draw.draw_prompt/edit']"
                            type="primary"
                            link
                            @click="openPop('edit', row)"
                        >
                            编辑
                        </el-button>
                        <el-button
                            v-perms="['draw.draw_prompt/delete']"
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
<script lang="ts" setup name="drawPrompt">
import { usePaging } from '@/hooks/usePaging'
import { getPromptList, delPrompt, editPromptStatus } from '@/api/ai_draw/draw_prompt'
import { getDrawCategoryList } from '@/api/ai_draw/draw_category'
import EditPopup from './edit.vue'
import feedback from '@/utils/feedback'

//弹框ref
const editRef = shallowRef<InstanceType<typeof EditPopup>>()
const showEdit = ref(true)
const props = reactive<any>({
    multiple: false,
    checkStrictly: true,
    label: 'name',
    value: 'id',
    children: 'children',
    emitPath: false
})
//搜索参数
const queryParams = reactive({
    prompt: '',
    category_id: '',
    status: ''
})
//分类列表
const categoryList: any = ref([])
const multipleSelection = ref<any[]>([])

const handleSelectionChange = (val: any[]) => {
    multipleSelection.value = val
}

//获取分类列表
const getCategoryList = async () => {
    const lists = await getDrawCategoryList()
    categoryList.value = lists
}

//打开弹框
const openPop = (type: string, value: any = {}) => {
    editRef.value?.open(type, value)
}

//删除
const handleDelete = async (id: number[]) => {
    await feedback.confirm('确定要删除？')
    await delPrompt({ id })
    getLists()
}

//修改状态
const changeStatus = (id: any) => {
    editPromptStatus({ id })
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getPromptList,
    params: queryParams
})

getLists()
getCategoryList()
</script>
