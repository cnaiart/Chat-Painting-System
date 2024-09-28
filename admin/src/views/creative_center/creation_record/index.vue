<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="用户信息">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.user_info"
                        placeholder="请输入用户ID/用户昵称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="关键词">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.keyword"
                        placeholder="请输入关键词"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="审核状态">
                    <el-select class="w-[280px]" v-model="queryParams.censor_status">
                        <el-option label="全部" value="" />
                        <el-option label="未审核" :value="0" />
                        <el-option label="合规" :value="1" />
                        <el-option label="不合规" :value="2" />
                        <el-option label="疑似" :value="3" />
                        <el-option label="审核失败" :value="4" />
                    </el-select>
                </el-form-item>
                <el-form-item label="创作模型">
                    <el-select class="w-[280px]" v-model="queryParams.category_id">
                        <el-option label="全部" value="" />
                        <el-option
                            v-for="(item, index) in categoryList"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="提问时间">
                    <daterange-picker
                        v-model:startTime="queryParams.start_time"
                        v-model:endTime="queryParams.end_time"
                    />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                    <export-data
                        class="ml-2.5"
                        :fetch-fun="chatRecordsLists"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div class="mb-4">
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
                <el-table-column label="ID" prop="id" min-width="80" />
                <el-table-column label="用户信息" min-width="180">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <image-contain
                                class="flex-none"
                                v-if="row.avatar"
                                :src="row.avatar"
                                :width="48"
                                :height="48"
                                :preview-src-list="[row.avatar]"
                                preview-teleported
                                fit="contain"
                            />
                            <span class="ml-4">{{ row.nickname }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    label="提问时间"
                    prop="create_time"
                    min-width="180"
                    show-tooltip-when-overflow
                />
                <el-table-column label="创作类别" prop="other_desc" min-width="180" />
                <el-table-column label="用户输入" prop="ask" min-width="280">
                    <template #default="{ row }">
                        <AskPop :content="row.ask" title="用户输入"></AskPop>
                    </template>
                </el-table-column>
                <el-table-column label="对话模型" prop="model_desc" min-width="120" />
                <el-table-column label="审核状态" min-width="180">
                    <template #default="{ row }">
                        <div>
                            <el-tag class="mr-2" v-if="row.censor_status == 1" type="success">
                                {{ row.censor_status_desc }}
                            </el-tag>
                            <template v-else-if="row.censor_status >= 2">
                                <el-tag class="mr-2 cursor-pointer" type="danger">
                                    {{ row.censor_status_desc }}
                                </el-tag>
                                <span
                                    class="text-error text-sm cursor-pointer"
                                    @click="openAuditDesc(row)"
                                >
                                    查看原因
                                </span>
                            </template>
                            <el-tag
                                class="mr-2"
                                v-else-if="row.censor_status == 0"
                                type="warning"
                                >{{ row.censor_status_desc }}</el-tag
                            >
                            <el-tag class="mr-2" v-else type="danger">
                                {{ row.censor_status_desc }}
                            </el-tag>
                        </div>
                    </template>
                </el-table-column>
                <!-- <el-table-column label="消耗tokens" prop="use_tokens" min-width="180" /> -->
                <el-table-column label="操作" min-width="180" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            type="primary"
                            v-perms="['chat_records/see']"
                            link
                            @click="openPop(row.reply)"
                        >
                            查看回复
                        </el-button>
                        <el-button
                            type="danger"
                            v-perms="['chat_records/del']"
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
        <auditPop ref="auditRef"></auditPop>
        <replyPop ref="popRef"></replyPop>
    </div>
</template>
<script lang="ts" setup name="dialogueRecord">
import { chatRecordsLists, chatRecordsDel, chatRecordCategoryList } from '@/api/ai_qa/chat_records'
import { usePaging } from '@/hooks/usePaging'
import replyPop from './replyPop.vue'
import feedback from '@/utils/feedback'
import { isString } from 'lodash-es'
import AuditPop from './auditPop.vue'

const queryParams = reactive({
    type: '2',
    user_info: '', //用户信息
    keyword: '', //关键词
    censor_status: '',
    start_time: '',
    end_time: '',
    category_id: '' //分类id
})

const categoryList = ref<{ id: number; name: string }[]>([])
//弹框ref
const popRef = shallowRef()
// 审核弹窗
const auditRef = shallowRef()
const multipleSelection = ref<any[]>([])

//打开弹框
const openPop = (reply: any) => {
    popRef.value.open(reply)
}

const openAuditDesc = (record: any) => {
    auditRef.value.open(record)
}

const handleSelectionChange = (val: any[]) => {
    multipleSelection.value = val
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: chatRecordsLists,
    params: queryParams
})
//获取创作分类列表
const getCategoryList = async () => {
    categoryList.value = await chatRecordCategoryList({ type: 2 })
}

const handleDelete = async (id: number | number[]) => {
    await feedback.confirm('确定要删除？')
    await chatRecordsDel({ id })
    feedback.msgSuccess('操作成功')
    getLists()
}

const getUserInput = (ask: any) => {
    if (isString(ask)) {
        return ask
    } else {
        const askObj = ask[0] || {}
        return askObj.value ? `${askObj.title}：${askObj.value}` : ''
    }
}

getLists()
getCategoryList()
</script>
