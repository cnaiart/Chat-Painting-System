<template>
    <div>
        <el-card class="!border-none mt-4" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="接口类型">
                    <el-select class="w-[280px]" v-model="queryParams.ai_key">
                        <el-option
                            v-for="(item, key) in modelList"
                            :key="key"
                            :label="item"
                            :value="key"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="状态">
                    <el-select class="w-[280px]" v-model="queryParams.status">
                        <el-option label="全部" value />
                        <el-option label="开启" :value="1" />
                        <el-option label="关闭" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="getLists">查询</el-button>
                    <el-button @click="resetPage">重置</el-button>
                    <export-data
                        class="ml-2.5"
                        :fetch-fun="getKeyPoolLists"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card shadow="never" class="!border-none mt-4">
            <el-tabs v-model="activeTab" @tabChange="changeTabs">
                <el-tab-pane
                    v-for="(item, index) in tabLists"
                    :label="`${item.name}`"
                    :name="item.type"
                    :key="index"
                >
                </el-tab-pane>
            </el-tabs>

            <div class="mb-[10px]">
                <el-button
                    v-perms="['setting.KeyPool/edit']"
                    type="primary"
                    @click="handleEdit('add')"
                >
                    + 新增密钥
                </el-button>

                <el-button
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
                <el-table-column label="接口类型" prop="ai_key_desc" min-width="120" />
                <!-- <el-table-column label="登录账号" prop="account" min-width="120" /> -->
                <el-table-column label="密钥  " prop="key" min-width="200">
                    <template #default="{ row }">
                        <div class="line-clamp-3 cursor-pointer" v-copy="row.key">
                            {{ row.key }}
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="状态" min-width="100" v-perms="['setting.KeyPool/status']">
                    <template #default="{ row }">
                        <el-switch
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                            @change="changeStatus(row.id)"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="自定义api域名" min-width="150">
                    <template #default="{ row }">
                        {{ row.notice?.api }}
                    </template>
                </el-table-column>

                <el-table-column label="停用原因" prop="notice" min-width="200">
                    <template #default="{ row }">
                        {{ row.notice?.notice }}
                    </template>
                </el-table-column>
                <el-table-column label="最近调用时间" prop="use_time" min-width="180" />
                <el-table-column label="更新时间" prop="update_time" min-width="180" />
                <el-table-column label="创建时间" prop="create_time" min-width="180" />
                <el-table-column label="操作" fixed="right" min-width="180">
                    <template #default="{ row }">
                        <el-button
                            v-if="row.change_btn"
                            v-perms="['setting.KeyPool/edit']"
                            type="primary"
                            link
                            @click="handleEdit('edit', row)"
                        >
                            更换
                        </el-button>
                        <el-button
                            v-else
                            v-perms="['setting.KeyPool/edit']"
                            type="primary"
                            link
                            @click="handleEdit('edit', row)"
                        >
                            编辑
                        </el-button>
                        <el-button
                            v-perms="['setting.KeyPool/del']"
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

        <balancePop ref="balanceRef"></balancePop>
        <edit-popup v-if="showEdit" ref="editRef" @success="getLists" />
    </div>
</template>
<script setup lang="ts">
import { usePaging } from '@/hooks/usePaging'
import {
    getKeyPoolLists,
    getKeyPoolAiModel,
    statusKeyPool,
    delKeyPool,
    getKeyPoolConfig,
    setKeyPoolConfig
} from '@/api/setting/ai_key'
import feedback from '@/utils/feedback'
import EditPopup from './edit.vue'

//是/否显示编辑弹框
const showEdit = ref(true)
//编辑弹框ref
const editRef = shallowRef<InstanceType<typeof EditPopup>>()
const modelList = ref<any[]>([])
const queryParams = reactive({
    type: 1,
    ai_key: '',
    keyword: '',
    start_time: '',
    end_time: '',
    status: ''
})

const activeTab = ref(1)
const tabLists = [
    {
        name: 'AI对话',
        type: 1
    },
    {
        name: 'AI绘画',
        type: 2
    },
    {
        name: '语音播报',
        type: 3
    },
    {
        name: '艺术二维码',
        type: 4
    },
    {
        name: '语音输入',
        type: 5
    }
]

const { pager, getLists, resetParams } = usePaging({
    fetchFun: getKeyPoolLists,
    params: queryParams
})
const multipleSelection = ref<any[]>([])

const handleSelectionChange = (val: any[]) => {
    multipleSelection.value = val
}

const resetPage = () => {
    queryParams.status = ''
    queryParams.ai_key = ''
    queryParams.keyword = ''
    multipleSelection.value = []
    getLists()
}

const changeTabs = (type: any) => {
    queryParams.type = type
    multipleSelection.value = []
    getLists()
    getAiModel()
}

const getAiModel = async () => {
    try {
        const data = await getKeyPoolAiModel({
            type: queryParams.type
        })
        modelList.value = data
    } catch (error) {
        console.log('获取ai模型失败=>', error)
    }
}

//修改状态
const changeStatus = (id: any) => {
    statusKeyPool({ id })
    feedback.msgSuccess('操作成功')
}

// 编辑
const handleEdit = (mode: string, value?: any) => {
    // queryParams.type | 当前类型 1对话 2绘画，mode: add|edit
    editRef.value?.open(queryParams.type, mode, value)
}

//  删除
const handleDelete = async (ids: number[]) => {
    await feedback.confirm('确定要删除？')
    await delKeyPool({ ids })
    feedback.msgSuccess('操作成功')
    getLists()
}

getLists()
getAiModel()
</script>
