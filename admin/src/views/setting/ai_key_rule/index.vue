<template>
    <div>
        <el-card shadow="never" class="!border-none">
            <el-form class="ls-form" :model="formData" label-width="120px">
                <div class="text-xl font-medium mb-[20px]">功能状态</div>
                <el-form-item label="key池规则" prop="key_auto_down">
                    <div>
                        <el-switch
                            :active-value="1"
                            :inactive-value="0"
                            v-model="formData.key_auto_down"
                            @change="handleSubmit"
                        ></el-switch>
                        <div class="form-tips">
                            开启时，如果Key已失效的话会自动下架Key，目前该设置只对ChatGPT的Key有效
                        </div>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="接口类型">
                    <el-select class="w-[280px] mr-3" v-model="queryParams.ai_key">
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
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                    <export-data
                        class="ml-2.5"
                        :fetch-fun="getKeyDownRuleLists"
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
                    v-perms="['setting.key_down_rule/add']"
                    type="primary"
                    @click="handleEdit('add')"
                >
                    + 新增规则
                </el-button>
            </div>

            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="接口类型" prop="ai_key_desc" min-width="100" />
                <el-table-column label="停用规则" prop="rule" min-width="200" />
                <el-table-column label="停用提示" prop="prompt" min-width="140" />
                <el-table-column
                    label="状态"
                    min-width="100"
                    v-perms="['setting.key_down_rule/status']"
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
                <el-table-column label="创建时间" prop="create_time" min-width="180" />
                <el-table-column label="更新时间" prop="update_time" min-width="180" />
                <el-table-column label="操作" fixed="right" min-width="180">
                    <template #default="{ row }">
                        <el-button
                            v-perms="['setting.key_down_rule/edit']"
                            type="primary"
                            link
                            @click="handleEdit('edit', row)"
                        >
                            编辑
                        </el-button>
                        <el-button
                            v-perms="['setting.key_down_rule/del']"
                            type="danger"
                            :link="true"
                            @click="handleDelete(row.id)"
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
import { getKeyPoolAiModel, getKeyPoolConfig, setKeyPoolConfig } from '@/api/setting/ai_key'
import { getKeyDownRuleLists, statusKeyDownRule, delKeyDownRule } from '@/api/setting/ai_key_rule'
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
const formData = ref({
    key_auto_down: 1
})
const tabLists = [
    {
        name: 'AI对话',
        type: 1
    },
    {
        name: 'AI绘画',
        type: 2
    }
]

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getKeyDownRuleLists,
    params: queryParams
})

const changeTabs = (type: any) => {
    queryParams.type = type
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
    statusKeyDownRule({ id })
    feedback.msgSuccess('操作成功')
}

// 编辑
const handleEdit = (mode: string, value?: any) => {
    // queryParams.type | 当前类型 1对话 2绘画，mode: add|edit
    editRef.value?.open(queryParams.type, mode, value)
}

//  删除
const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除？')
    await delKeyDownRule({ id })
    feedback.msgSuccess('操作成功')
    getLists()
}

const getKeyPool = async () => {
    formData.value = await getKeyPoolConfig()
}

const handleSubmit = async () => {
    try {
        await setKeyPoolConfig(formData.value)
    } finally {
        getKeyPool()
    }
}
getKeyPool()

getLists()
getAiModel()
</script>
