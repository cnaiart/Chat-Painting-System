<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
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
                        :fetch-fun="getPromptExampleList"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div>
                <el-button
                    v-perms="['draw.draw_prompt_example/add']"
                    type="primary"
                    @click="openPop('add')"
                >
                    <template #icon>
                        <icon name="el-icon-Plus" />
                    </template>
                    新增
                </el-button>
                <el-button
                    v-perms="['draw.draw_prompt_example/delete']"
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
                <el-table-column label="示例标题" prop="prompt" min-width="140" />
                <el-table-column label="示例内容" prop="prompt_en" min-width="140" />
                <el-table-column
                    label="状态"
                    min-width="100"
                    v-perms="['draw.draw_prompt_example/status']"
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
                <el-table-column label="操作" width="150" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-perms="['draw.draw_prompt_example/edit']"
                            type="primary"
                            :link="true"
                            @click="openPop('edit', row.id)"
                        >
                            编辑
                        </el-button>
                        <el-button
                            v-perms="['draw.draw_prompt_example/delete']"
                            type="danger"
                            :link="true"
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
        <edit-popup v-if="showEdit" ref="editRef" @success="getLists" @close="showEdit = false" />
    </div>
</template>
<script lang="ts" setup name="drawPromptExample">
import { usePaging } from '@/hooks/usePaging'
import {
    getPromptExampleList,
    delPromptExample,
    editPromptExampleStatus
} from '@/api/ai_draw/draw_example'

import EditPopup from './edit.vue'
import feedback from '@/utils/feedback'

const showEdit = ref(false)
//弹框ref
const editRef = shallowRef<InstanceType<typeof EditPopup>>()

//搜索参数
const queryParams = reactive({
    status: ''
})
const multipleSelection = ref<any[]>([])

const handleSelectionChange = (val: any[]) => {
    multipleSelection.value = val
}

//打开弹框
const openPop = async (type: string, id: number | unknown) => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open(type, id)
}

//删除
const handleDelete = async (id: number[]) => {
    await feedback.confirm('确定要删除？')
    await delPromptExample({ id })
    getLists()
}

//修改状态
const changeStatus = (id: any) => {
    editPromptExampleStatus({ id })
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getPromptExampleList,
    params: queryParams
})

getLists()
</script>
