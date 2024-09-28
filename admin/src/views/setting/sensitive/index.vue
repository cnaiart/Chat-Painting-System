<template>
    <el-card shadow="never" class="!border-none">
        <el-form class="ls-form" :model="formData" label-width="120px">
            <div class="text-xl font-medium mb-[20px]">功能状态</div>
            <el-form-item label="内置敏感词库" prop="name">
                <div>
                    <el-switch
                        :active-value="1"
                        :inactive-value="0"
                        v-model="formData.is_sensitive"
                        @change="handleSubmit"
                    ></el-switch>
                    <div class="form-tips">过滤敏感词，默认开启</div>
                </div>
            </el-form-item>
        </el-form>
    </el-card>
    <el-card shadow="never" class="!border-none mt-4">
        <el-form ref="formRef" class="mb-[16px]" :model="queryParams" :inline="true">
            <el-form-item label="关键词">
                <el-input
                    class="w-[280px]"
                    v-model="queryParams.keyword"
                    placeholder="请输入"
                    clearable
                />
            </el-form-item>
            <el-form-item label="状态">
                <el-select class="w-[280px]" v-model="queryParams.status">
                    <el-option value>全部</el-option>
                    <el-option :value="1" label="已开启">已开启</el-option>
                    <el-option :value="0" label="已关闭">已关闭</el-option>
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="resetPage">查询</el-button>
                <el-button @click="resetParams">重置</el-button>
                <export-data
                    class="ml-2.5"
                    :fetch-fun="getlists"
                    :params="queryParams"
                    :page-size="pager.size"
                />
            </el-form-item>
        </el-form>
        <el-button
            v-perms="['setting.SensitiveWord/add']"
            type="primary"
            @click="handleedit('add')"
        >
            新增敏感词
        </el-button>
        <el-table size="large" v-loading="pager.loading" :data="pager.lists" class="mt-4">
            <el-table-column label="ID" prop="id" width="80"> </el-table-column>
            <el-table-column label="敏感词" prop="word" min-width="280" />
            <el-table-column label="状态" min-width="100">
                <template #default="{ row }">
                    <el-tag type="success" v-if="row.status == 1">已开启</el-tag>
                    <el-tag v-if="row.status == 0">已关闭</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="创建时间" prop="create_time" min-width="100" />
            <el-table-column label="操作" min-width="100">
                <template #default="{ row }">
                    <el-button
                        v-perms="['setting.SensitiveWord/edit']"
                        link
                        type="primary"
                        @click="handleedit('edit', row)"
                        >编辑</el-button
                    >
                    <el-button
                        v-perms="['setting.SensitiveWord/del']"
                        link
                        type="danger"
                        @click="handledel(row.id)"
                    >
                        删除</el-button
                    >
                </template>
            </el-table-column>
        </el-table>
        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </el-card>
    <editPop ref="editPopref" v-if="showedit" @close="handlecolse"></editPop>
</template>
<script setup lang="ts">
import { getRoutePath } from '@/router'
import { usePaging } from '@/hooks/usePaging'
import {
    getlists,
    delSensitive,
    getSensitiveConfig,
    setSensitiveConfig
} from '@/api/setting/sensitive'
import editPop from './edit.vue'
const queryParams = reactive({
    keyword: '',
    status: ''
})
const formData = ref({
    is_sensitive: 1
})
const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getlists,
    params: queryParams
})
getLists()

//弹框ref
const editPopref = shallowRef<InstanceType<typeof editPop>>()
//打开弹框
const showedit = ref(false)
const handleedit = async (type: any, row?: any) => {
    showedit.value = true
    await nextTick()
    editPopref.value?.open(type)
    if (row) {
        editPopref.value?.setFormData(row)
    }
}
const handlecolse = () => {
    showedit.value = false
    getLists()
}
//删除
const handledel = async (id: number) => {
    await delSensitive({ id })
    getLists()
}

const getSensitive = async () => {
    formData.value = await getSensitiveConfig()
}

const handleSubmit = async () => {
    try {
        await setSensitiveConfig(formData.value)
    } finally {
        getSensitive()
    }
}
getSensitive()
</script>
