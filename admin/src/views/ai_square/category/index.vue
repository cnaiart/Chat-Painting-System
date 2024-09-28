<template>
    <div>
        <el-card class="!border-none" shadow="never">
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
                        <el-option label="全部" value="" />
                        <el-option label="开启" :value="1" />
                        <el-option label="关闭" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="getData">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                    <export-data
                        class="ml-2.5"
                        :fetch-fun="squareCategoryList"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div>
                <el-button
                    v-perms="['draw.draw_square_category/add']"
                    type="primary"
                    @click="handleAdd"
                >
                    <template #icon>
                        <icon name="el-icon-Plus" />
                    </template>
                    新增
                </el-button>
            </div>
            <el-table size="large" class="mt-4" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="分类名称" prop="name" min-width="120" />
                <el-table-column label="分类图片" min-width="130">
                    <template #default="{ row }">
                        <el-image
                            v-if="row.image"
                            :src="row.image"
                            class="w-[50px] h-[50px]"
                        ></el-image>
                    </template>
                </el-table-column>
                <el-table-column
                    label="状态"
                    min-width="100"
                    v-perms="['draw.draw_square_category/status']"
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
                            v-perms="['draw.draw_square_category/edit']"
                            type="primary"
                            link
                            @click="handleEdit(row)"
                        >
                            编辑
                        </el-button>
                        <el-button
                            v-perms="['draw.draw_square_category/del']"
                            type="danger"
                            link
                            @click="handleDelete(row.id, row.model_count)"
                        >
                            删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>
        <edit-popup v-if="showEdit" ref="editRef" @success="getData" @close="showEdit = false" />
    </div>
</template>
<script lang="ts" setup name="drawSquareCate">
import EditPopup from './edit.vue'
import {
    squareCategoryList,
    delSquareCategory,
    changeSquareCategoryStatus
} from '@/api/ai_square/category'
import feedback from '@/utils/feedback'
const editRef = shallowRef<InstanceType<typeof EditPopup>>()
//搜索参数
const queryParams = reactive({
    name: '',
    status: ''
})
const pager = reactive({
    loading: true,
    lists: []
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
const handleDelete = async (id: number, model_count: number) => {
    await feedback.confirm('确定要删除？')
    await delSquareCategory({ id })
    getData()
}

//修改状态
const changeStatus = (id: any) => {
    changeSquareCategoryStatus({ id })
}

const resetParams = () => {
    queryParams.name = ''
    queryParams.status = ''
    getData()
}

const getData = async () => {
    pager.loading = true
    try {
        const { lists } = await squareCategoryList(queryParams)
        pager.lists = lists
        pager.loading = false
    } catch (error) {
        pager.loading = false
        console.log('获取绘画分类失败=>', error)
    }
}

getData()
</script>
