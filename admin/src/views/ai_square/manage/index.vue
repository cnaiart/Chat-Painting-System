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
                <el-form-item label="所属分类">
                    <el-select class="w-[280px]" v-model="queryParams.category_id">
                        <el-option label="全部" value="" />
                        <el-option
                            v-for="item in catePager.lists"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="审核状态">
                    <el-select class="w-[280px]" v-model="queryParams.verify_status">
                        <el-option label="全部" value="" />
                        <el-option label="待审核" :value="0" />
                        <el-option label="审核通过" :value="1" />
                        <el-option label="审核不通过" :value="2" />
                    </el-select>
                </el-form-item>
                <el-form-item label="是否显示">
                    <el-select class="w-[280px]" v-model="queryParams.is_show">
                        <el-option label="全部" value="" />
                        <el-option label="显示" :value="1" />
                        <el-option label="隐藏" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item label="添加来源">
                    <el-select class="w-[280px]" v-model="queryParams.source">
                        <el-option label="全部" value="" />
                        <el-option label="后台添加" :value="1" />
                        <el-option label="前台用户" :value="2" />
                    </el-select>
                </el-form-item>
                <el-form-item label="创建时间">
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
                        :fetch-fun="getDrawSquareList"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div class="mb-4">
                <el-button
                    v-perms="['draw.draw_square/add']"
                    type="primary"
                    @click="handleAdd"
                >
                    <template #icon>
                        <icon name="el-icon-Plus" />
                    </template>
                    新增绘画
                </el-button>
                <el-button
                    v-perms="['draw.draw_square/verifyStatus']"
                    type="default"
                    :plain="true"
                    :disabled="!multipleSelection.length"
                    @click="handleAudit(multipleSelection.map((item) => item.id))"
                >
                    批量审核
                </el-button>
                <el-button
                    v-perms="['draw.draw_square/del']"
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
                <el-table-column label="图片" min-width="130">
                    <template #default="{ row }">
                        <image-contain
                            class="flex-none"
                            :src="row?.thumbnail"
                            :width="70"
                            :preview-src-list="[row?.image]"
                            :preview-teleported="true"
                            :hide-on-click-modal="true"
                            fit="contain"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="用户输入/中文提示词" prop="prompts_cn" min-width="280">
                    <template #default="{ row }">
                        <Popup
                            ref="popRef"
                            title="提示词"
                            width="700px"
                            clickModalClose
                            cancelButtonText="取消"
                            confirmButtonText="确定"
                        >
                            <template #trigger>
                                <div
                                    class="line-clamp-3 cursor-pointer"
                                >
                                    {{ row.prompts_cn || '-' }}
                                </div>
                            </template>
                            <div class="mt-[6px]">{{ row.prompts_cn || '-' }}</div>
                        </Popup>
                    </template>
                </el-table-column>
                <el-table-column label="英文提示词" prop="prompts" min-width="280">
                    <template #default="{ row }">
                        <Popup
                            ref="popRef"
                            title="提示词"
                            width="700px"
                            clickModalClose
                            cancelButtonText="取消"
                            confirmButtonText="确定"
                        >
                            <template #trigger>
                                <div
                                    class="line-clamp-3 cursor-pointer"
                                >
                                    {{ row.prompts }}
                                </div>
                            </template>
                            <div class="mt-[6px]">{{ row.prompts }}</div>
                        </Popup>
                    </template>
                </el-table-column>
                <el-table-column label="所属分类" prop="category_name" min-width="120" />
                <el-table-column label="添加来源" prop="source_desc" min-width="120" />
                <el-table-column label="用户信息" min-width="200">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <image-contain
                                class="flex-none"
                                v-if="row?.user_info.image"
                                :src="row?.user_info.image"
                                :width="48"
                                :height="48"
                                :preview-src-list="[row?.user_info.image]"
                                :preview-teleported="true"
                                :hide-on-click-modal="true"
                                fit="contain"
                            />
                            <span class="ml-4">{{ row?.user_info.name }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="审核状态" min-width="140">
                    <template #default="{ row }">
                        <div>
                            <el-tag class="ml-2" v-if="row.verify_status == 1" type="success">
                                {{ row.verify_status_desc }}
                            </el-tag>
                            <el-tag class="ml-2" v-else-if="row.verify_status == 0" type="warning">
                                {{ row.verify_status_desc }}
                            </el-tag>
                            <el-tag
                                class="ml-2 cursor-pointer"
                                v-else
                                type="danger"
                                @click="handleReply(row.verify_result)"
                            >
                                {{ row.verify_status_desc }}
                            </el-tag>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    label="状态"
                    min-width="100"
                    v-perms="['draw.draw_square/isShow']"
                >
                    <template #default="{ row }">
                        <el-switch
                            @change="changeStatus(row.id)"
                            v-model="row.is_show"
                            :active-value="1"
                            :inactive-value="0"
                        />
                    </template>
                </el-table-column>
                <el-table-column
                    label="创建时间"
                    prop="create_time"
                    min-width="180"
                    show-tooltip-when-overflow
                />
                <el-table-column label="操作" min-width="180" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-perms="['draw.draw_square/edit']"
                            type="primary"
                            link
                            @click="handleEdit(row.id)"
                        >
                            编辑
                        </el-button>
                        <el-button
                            v-if="!row.verify_status"
                            v-perms="['draw.draw_square/verifyStatus']"
                            type="primary"
                            link
                            @click="handleAudit([row.id])"
                        >
                            审核
                        </el-button>
                        <el-button
                            type="danger"
                            v-perms="['draw.draw_square/del']"
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
        <EditPopup
            v-if="showEdit"
            ref="editRef"
            @success="getLists"
            @close="showEdit = false"
        ></EditPopup>
        <AuditPopup
            v-if="showAudit"
            ref="auditRef"
            @success="getLists"
            @close="showAudit = false"
        ></AuditPopup>
        <ReplyPopup
            v-if="showReply"
            ref="replyRef"
            @success="getLists"
            @close="showReply = false"
        ></ReplyPopup>
    </div>
</template>
<script lang="ts" setup name="drawSquareLists">
import {
    getDrawSquareList,
    delDrawSquare,
    editDrawSquareStatus
} from '@/api/ai_square/manage'
import { squareCategoryList } from '@/api/ai_square/category'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

import EditPopup from './edit.vue'
import AuditPopup from './audit.vue'
import ReplyPopup from './reply.vue'

const queryParams = reactive({
    verify_status: '',
    user_info: '', //用户信息
    keyword: '', //关键词
    category_id: '',
    is_show: '',
    source: '',
    start_time: '',
    end_time: ''
})
const catePager = reactive({
    loading: true,
    lists: []
})

//弹框ref
const editRef = shallowRef()
const auditRef = shallowRef()
const replyRef = shallowRef()
const showEdit = ref<boolean>(false)
const showAudit = ref<boolean>(false)
const showReply = ref<boolean>(false)
const multipleSelection = ref<any[]>([])

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getDrawSquareList,
    params: queryParams
})

const getData = async () => {
    catePager.loading = true
    try {
        const { lists } = await squareCategoryList()
        catePager.lists = lists
        catePager.loading = false
    } catch (error) {
        catePager.loading = false
        console.log('获取绘画分类失败=>', error)
    }
}

const handleSelectionChange = (val: any[]) => {
    multipleSelection.value = val
}

//添加
const handleAdd = async () => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('add')
}
//编辑
const handleEdit = async (id: number) => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('edit', id)
}

//修改状态
const changeStatus = async (id: any) => {
    try {
        await editDrawSquareStatus({ id })
    } catch (error) {
        console.log('修改状态失败=>', error)
    }
}

// 审核
const handleAudit = async (ids: number[]) => {
    showAudit.value = true
    await nextTick()
    auditRef.value?.open(ids)
}

// 查看审核原因
const handleReply = async (result: string) => {
    showReply.value = true
    await nextTick()
    replyRef.value?.open(result)
}

// 删除
const handleDelete = async (id: number[]) => {
    await feedback.confirm('确定要删除？')
    await delDrawSquare({ id })
    getLists()
}

getData()
getLists()
</script>
