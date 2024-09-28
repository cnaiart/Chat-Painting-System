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
                        @keyup.enter="getLists"
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
                    <el-button type="primary" @click="getLists">查询</el-button>
                    <el-button @click="requestPagination">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div>
                <el-button
                    v-perms="['draw.draw_category/add']"
                    type="primary"
                    @click="openPop('add')"
                >
                    <template #icon>
                        <icon name="el-icon-Plus" />
                    </template>
                    新增
                </el-button>
            </div>

            <el-table
                size="large"
                class="mt-4"
                v-loading="pager.loading"
                :data="pager.lists"
                row-key="id"
                :expand-row-keys="expand"
                :tree-props="{ children: 'children' }"
            >
                <el-table-column label="分类名称" prop="name" min-width="120" />
                <el-table-column
                    label="状态"
                    min-width="100"
                    v-perms="['draw.draw_category/status']"
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
                            v-perms="['draw.draw_category/edit']"
                            type="primary"
                            link
                            @click="openPop('edit', row)"
                        >
                            编辑
                        </el-button>
                        <el-button
                            v-perms="['draw.draw_category/delete']"
                            type="danger"
                            link
                            @click="handleDelete(row.id, row.sample_count)"
                        >
                            删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>
        <edit-popup v-if="showEdit" ref="editRef" @success="getLists" />
    </div>
</template>
<script lang="tsx" setup name="drawCategory">
import EditPopup from './edit.vue'
import {
    getDrawCategoryList,
    delDrawCategory,
    editDrawCategoryStatus
} from '@/api/ai_draw/draw_category'
import feedback from '@/utils/feedback'

const expand = ref<string[]>([])
//弹框ref
const editRef = shallowRef<InstanceType<typeof EditPopup>>()
//搜索参数
const queryParams = reactive({
    name: '',
    status: ''
})
const pager = reactive({
    loading: true,
    lists: [] as any
})
//是/否显示编辑弹框
const showEdit = ref(true)

//打开弹框
const openPop = (type: string, value: any = {}) => {
    editRef.value?.open(type, value)
}

const getLists = async () => {
    try {
        const data = await getDrawCategoryList(queryParams)
        pager.lists = data
    } catch (error) {
        console.log('获取分类列表失败', error)
    }
    pager.loading = false
}

const requestPagination = () => {
    queryParams.name = ''
    queryParams.status = ''
    getLists()
    expand.value = []
}

//删除
const handleDelete = async (id: number, sample_count: number) => {
    await feedback.confirm('确定要删除吗？')
    await delDrawCategory({ id })
    getLists()
}

//修改状态
const changeStatus = (id: any) => {
    editDrawCategoryStatus({ id })
}

getLists()
</script>
