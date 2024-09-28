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
                        v-model="queryParams.prompt"
                        placeholder="请输入关键词"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="绘画模型">
                    <el-select class="w-[280px]" v-model="queryParams.model">
                        <el-option label="全部" value></el-option>
                        <el-option
                            v-for="(item, key) in modelList"
                            :key="key"
                            :label="item"
                            :value="key"
                        ></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="生成结果">
                    <el-select class="w-[280px]" v-model="queryParams.status">
                        <el-option label="全部" value></el-option>
                        <el-option label="生成中" :value="1"></el-option>
                        <el-option label="生成失败" :value="2"></el-option>
                        <el-option label="生成成功" :value="3"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="生成时间">
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
                        :fetch-fun="drawRecordsLists"
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
                <el-table-column label="用户信息" min-width="200">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <image-contain
                                class="flex-none"
                                v-if="row.avatar"
                                :src="row.avatar"
                                :width="48"
                                :height="48"
                                :preview-src-list="[row.avatar]"
                                :preview-teleported="true"
                                :hide-on-click-modal="true"
                                fit="contain"
                            />
                            <span class="ml-4">{{ row.nickname }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    label="生成时间"
                    prop="create_time"
                    min-width="180"
                    show-tooltip-when-overflow
                />
                <el-table-column label="用户输入" prop="prompt" min-width="280">
                    <template #default="{ row }">
                        <Popup
                            ref="popRef"
                            title="用户输入"
                            width="700px"
                            clickModalClose
                            cancelButtonText="取消"
                            confirmButtonText="确定"
                        >
                            <template #trigger>
                                <div
                                    class="line-clamp-2 cursor-pointer"
                                >
                                  {{ row.prompt }}
                                </div>
                            </template>
                            <div>{{ row.prompt }}</div>
                            <template v-if="row.prompt_en">
                                <div class="mt-[20px]">用户输入翻译：</div>
                                <div class="mt-[6px]">{{ row.prompt_en }}</div>
                            </template>
                        </Popup>
                    </template>
                </el-table-column>
                <el-table-column label="生成结果" min-width="160">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <span v-if="row.status == 1">生成中...</span>
                            <image-contain
                                class="flex-none"
                                v-else-if="row.status == 3"
                                :src="row.thumbnail"
                                :width="64"
                                :height="64"
                                :preview-src-list="[row.image]"
                                :preview-teleported="true"
                                :hide-on-click-modal="true"
                                fit="contain"
                            />
                            <span
                                v-else
                                class="text-error cursor-pointer"
                                @click="feedback.alert('生成失败: ' + row.fail_reason, '失败原因')"
                            >
                                生成失败
                            </span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="绘画模型" prop="model_text" min-width="140" />
                <el-table-column label="上传图片" min-width="160">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <image-contain
                                class="flex-none"
                                v-if="row.image_base"
                                :src="row.image_base"
                                :width="64"
                                :height="64"
                                :preview-src-list="[row.image_base]"
                                :preview-teleported="true"
                                :hide-on-click-modal="true"
                                fit="contain"
                            />
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="消耗条数" prop="use_tokens" min-width="120" />
                <el-table-column label="操作" min-width="180" fixed="right">
                    <template #default="{ row }">
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
    </div>
</template>
<script lang="ts" setup name="dialogueRecord">
import { drawRecordsLists, drawRecordsDel, drawModel } from '@/api/ai_draw/draw_records'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

const queryParams = reactive({
    type: '1',
    status: '',
    user_info: '', //用户信息
    prompt: '', //关键词
    model: '',
    start_time: '',
    end_time: ''
})

// 模型列表
const modelList = ref([])
const multipleSelection = ref<any[]>([])

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: drawRecordsLists,
    params: queryParams
})

const getDrawModel = async () => {
    try {
        modelList.value = await drawModel()
    } catch (error) {
        console.log('获取绘画模型', error)
    }
}

const handleSelectionChange = (val: any[]) => {
    multipleSelection.value = val
}

const handleDelete = async (ids: number[]) => {
    await feedback.confirm('确定要删除？')
    await drawRecordsDel({ ids })
    await feedback.msgSuccess('删除成功')
    getLists()
}

getLists()
getDrawModel()
</script>
