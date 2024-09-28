<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="用户信息">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.keyword"
                        placeholder="用户编号/昵称/手机号码/邮箱"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="注册时间">
                    <daterange-picker
                        v-model:startTime="queryParams.create_time_start"
                        v-model:endTime="queryParams.create_time_end"
                    />
                </el-form-item>
                <el-form-item label="注册来源">
                    <el-select class="w-[280px]" v-model="queryParams.channel">
                        <el-option label="全部" value />
                        <el-option
                            v-for="(item, key) in ClientMap"
                            :key="key"
                            :label="item"
                            :value="key"
                        />
                        <el-option label="后台创建" :value="7" />
                    </el-select>
                </el-form-item>
                <el-form-item label="开通会员">
                    <el-select class="w-[280px]" v-model="queryParams.is_member">
                        <el-option label="全部" value="" />
                        <el-option label="未开通" :value="0" />
                        <el-option label="已开通" :value="1" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                    <export-data
                        class="ml-2.5"
                        :fetch-fun="getUserList"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div class="mb-4">
                <el-button type="primary" @click="handleAddAccountPop"> 创建用户 </el-button>
            </div>
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="用户ID" prop="sn" min-width="120" />
                <el-table-column label="头像" min-width="100">
                    <template #default="{ row }">
                        <el-avatar :src="row.avatar" :size="50" />
                    </template>
                </el-table-column>
                <el-table-column label="用户昵称" prop="nickname" min-width="100" />
                <el-table-column label="手机号码" prop="mobile" min-width="120" />
                <el-table-column label="邮箱账号" prop="email" min-width="120" />
                <el-table-column label="注册来源" prop="channel" min-width="100" />
                <el-table-column label="余额" min-width="160">
                    <template #default="{ row }">
                        <div>对话：{{ row.balance }}</div>
                        <div>绘画：{{ row.balance_draw }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="会员" min-width="100">
                    <template #default="{ row }">
                        <div class="text-[#4073FA]">{{ row.member_desc }}</div>
                        <!-- <div v-if="row.is_end" class="text-[#FBAE00]">已到期</div> -->
                    </template>
                </el-table-column>
                <el-table-column label="到期时间" prop="member_end_time_desc" min-width="180">
                    <template #default="{ row }">
                        <div>{{ row.member_end_time_desc }}</div>
                        <div v-if="row.is_end" class="text-[#FBAE00]">已到期</div>
                    </template>
                </el-table-column>
                <el-table-column label="累计消费" prop="total_amount" min-width="120" />

                <el-table-column label="注册时间" prop="create_time" min-width="120" />
                <el-table-column label="操作" fixed="right" min-width="180">
                    <template #default="{ row }">
                        <el-button v-perms="['user.user/detail']" type="primary" link>
                            <router-link
                                :to="{
                                    path: getRoutePath('user.user/detail'),
                                    query: {
                                        id: row.id
                                    }
                                }"
                            >
                                详情
                            </router-link>
                        </el-button>
                        <el-button
                            v-perms="['user.user/blacklist']"
                            type="primary"
                            link
                            @click="handledisable(row.id, row.is_blacklist)"
                        >
                            {{ row.is_blacklist ? '移出黑名单' : '加入黑名单' }}
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <add-account
            v-if="showAddAccount"
            ref="addAccountRef"
            @success="resetPage"
            @close="showAddAccount = false"
        />
    </div>
</template>
<script lang="ts" setup name="consumerLists">
import { usePaging } from '@/hooks/usePaging'
import { getRoutePath } from '@/router'
import { getUserList, disable } from '@/api/consumer'
import { ClientMap } from '@/enums/appEnums'
import feedback from '@/utils/feedback'
import AddAccount from '@/views/consumer/components/add-account.vue'

const queryParams = reactive({
    keyword: '',
    channel: '',
    create_time_start: '',
    create_time_end: '',
    is_member: ''
})
const addAccountRef = shallowRef<typeof AddAccount>()
const showAddAccount = ref<boolean>(false)

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getUserList,
    params: queryParams
})

/**
 * 黑名单
 */
const handledisable = async (id: number, status: number) => {
    await feedback.confirm(`是否${status ? '移出黑名单' : '加入黑名单'}`)
    await disable({ id })
    await getLists()
}

const handleAddAccountPop = async () => {
    showAddAccount.value = true
    await nextTick()
    addAccountRef.value.open()
}

onActivated(() => {
    getLists()
})
getLists()
</script>
