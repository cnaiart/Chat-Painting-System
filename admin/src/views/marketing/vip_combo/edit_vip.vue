<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-page-header :content="title" @back="$router.back()" />
        </el-card>
        <el-form
            class="mt-4"
            ref="formRef"
            :model="formData"
            label-width="120px"
            :rules="formRules"
        >
            <el-card shadow="never" class="!border-none">
                <div class="text-lg font-medium">套餐信息</div>
                <div class="mt-4">
                    <!-- 套餐名称 -->
                    <el-form-item label="套餐名称" prop="name">
                        <div class="flex w-full">
                            <el-input
                                v-model="formData.name"
                                placeholder="请输入套餐名称，如：月度会员、季度会员"
                                clearable
                                class="w-[360px]"
                            />
                        </div>
                    </el-form-item>
                    <!-- 套餐时长 -->
                    <el-form-item prop="duration">
                        <template #label>
                            <div>
                                <span class="text-error">*</span>
                                套餐时长
                            </div>
                        </template>
                        <div class="flex">
                            <el-input
                                v-model="formData.duration"
                                placeholder="请输入整数"
                                clearable
                                class="w-[360px]"
                                :disabled="!!formData.is_perpetual"
                            >
                                <template #append>
                                    <el-select v-model="formData.duration_type" class="w-[80px]">
                                        <el-option :value="1" label="个月"></el-option>
                                        <el-option :value="2" label="天"></el-option>
                                    </el-select>
                                </template>
                            </el-input>
                            <el-checkbox
                                class="ml-[4px]"
                                v-model="formData.is_perpetual"
                                :true-label="1"
                                :false-label="0"
                                >永久</el-checkbox
                            >
                        </div>
                    </el-form-item>
                    <!-- 实际售价 -->
                    <el-form-item label="实际售价" prop="sell_price">
                        <div class="flex">
                            <el-input
                                v-model="formData.sell_price"
                                placeholder="请输入实际售价"
                                clearable
                                class="w-[360px]"
                            >
                                <template #append>元</template>
                            </el-input>
                        </div>
                    </el-form-item>
                    <!-- 划线价 -->
                    <el-form-item label="划线价">
                        <div class="w-[360px]">
                            <el-input
                                v-model="formData.lineation_price"
                                clearable
                                class="w-[360px]"
                                placeholder="请输入划线价"
                            >
                                <template #append>元</template>
                            </el-input>
                        </div>
                    </el-form-item>
                    <!-- 挽回优惠 -->
                    <el-form-item label="挽回优惠">
                        <div>
                            <el-radio-group v-model="formData.is_retrieve" class="ml-4">
                                <el-radio :label="1">开启</el-radio>
                                <el-radio :label="0">关闭</el-radio>
                            </el-radio-group>
                            <div class="form-tips flex">
                                用户返回上一页点击放弃支付时弹出的优惠金额
                                <el-popover placement="right" :width="200" trigger="hover">
                                    <template #reference>
                                        <el-button link type="primary">查看</el-button>
                                    </template>
                                    <img src="./images/vip_coupon.jpg" />
                                </el-popover>
                            </div>
                        </div>
                    </el-form-item>
                    <!-- 优惠金额 -->
                    <el-form-item
                        label="优惠金额"
                        v-if="formData.is_retrieve == 1"
                        prop="retrieve_amount"
                    >
                        <div>
                            <el-input
                                v-model="formData.retrieve_amount"
                                clearable
                                class="w-[360px]"
                                placeholder="请输入优惠金额"
                            >
                                <template #append>元</template>
                            </el-input>
                            <div class="form-tips">
                                开启挽回优惠后，用户付款金额=实际售价-优惠金额
                            </div>
                        </div>
                    </el-form-item>

                    <el-form-item label="套餐标签">
                        <div>
                            <el-input
                                class="w-[360px]"
                                v-model="formData.tag"
                                placeholder="请输入"
                            ></el-input>
                            <div class="form-tips">填写就显示 不填不显示</div>
                        </div>
                    </el-form-item>
                    <!-- 排序 -->
                    <el-form-item label="排序">
                        <div>
                            <el-input class="w-[360px]" v-model="formData.sort"></el-input>
                            <div class="form-tips">默认为0，数值越大越排前面</div>
                        </div>
                    </el-form-item>
                    <!-- 是否上架 -->
                    <el-form-item label="是否上架" prop="status">
                        <div>
                            <el-switch
                                v-model="formData.status"
                                :active-value="1"
                                :inactive-value="0"
                            />
                        </div>
                    </el-form-item>
                </div>
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-lg font-medium flex items-center">会员权益</div>
                <div class="mt-4">
                    <el-form-item label="会员权益">
                        <div v-if="optionsData.benefitsLists.length">
                            <div>
                                <el-button link type="primary" @click="handleCheckAllChange">
                                    全选
                                </el-button>
                            </div>
                            <div>
                                <el-checkbox-group v-model="formData.benefits_ids">
                                    <el-checkbox
                                        v-for="(item, index) in optionsData.benefitsLists"
                                        :key="index"
                                        :label="item.id"
                                    >
                                        {{ item.name }}
                                    </el-checkbox>
                                </el-checkbox-group>
                            </div>
                        </div>
                    </el-form-item>
                </div>
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-lg font-medium">上限设置</div>
                <div class="mt-4">
                    <el-form-item label="每日对话上限">
                        <div>
                            <el-input
                                v-model="formData.chat_limit"
                                placeholder="请输入每日对话上限次数"
                                class="w-[360px]"
                            ></el-input>
                            <div class="form-tips">请输入大于0的次数，不填则表示不限制</div>
                        </div>
                    </el-form-item>
                </div>
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-lg font-medium">额外赠送</div>
                <div class="mt-4">
                    <el-form-item label="赠送对话条数">
                        <div>
                            <el-input
                                v-model="formData.give_chat_number"
                                placeholder="为空或者填0表示不赠送"
                                class="w-[360px]"
                            >
                                <template #append>条</template>
                            </el-input>
                        </div>
                    </el-form-item>
                    <el-form-item label="赠送绘画条数">
                        <div>
                            <el-input
                                v-model="formData.give_draw_number"
                                placeholder="为空或者填0表示不赠送"
                                class="w-[360px]"
                            >
                                <template #append>条</template>
                            </el-input>
                        </div>
                    </el-form-item>
                </div>
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-lg font-medium">购买限制</div>
                <div class="mt-4">
                    <el-form-item label="是否限购" prop="is_quota">
                        <div>
                            <el-switch
                                v-model="formData.is_quota"
                                :active-value="1"
                                :inactive-value="0"
                            />
                        </div>
                    </el-form-item>
                    <el-form-item label="限制每人可购买" prop="quota_value">
                        <div>
                            <el-input v-model="formData.quota_value" class="w-[360px]">
                                <template #append>次</template>
                            </el-input>
                            <div class="form-tips">限制每人可重复购买当前会员套餐多少次</div>
                        </div>
                    </el-form-item>
                    <el-form-item label="限购提示语">
                        <div>
                            <el-input
                                v-model="formData.quota_tips"
                                class="w-[360px]"
                                type="textarea"
                                rows="4"
                            />
                            <div class="form-tips flex items-center">
                                <div>自定义限购提示语文案</div>
                                <el-popover placement="top-start" width="auto" trigger="hover">
                                    <template #reference>
                                        <el-button link type="primary"> 查看示例 </el-button>
                                    </template>

                                    <img src="./images/limit_tips.png" alt="" class="w-[400px]" />
                                </el-popover>
                            </div>
                        </div>
                    </el-form-item>
                </div>
            </el-card>
            <footer-btns>
                <el-button type="primary" @click="handleSubmit">保存</el-button>
            </footer-btns>
        </el-form>
    </div>
</template>

<script setup lang="ts">
import { addMenmber, detialMenmber, editlMenmber, getBenefitsListsAll } from '@/api/marketing/vip'
import { useDictOptions } from '@/hooks/useDictOptions'

const router = useRouter()
const { query } = useRoute()
const title = computed(() => {
    return query.id ? '编辑会员套餐' : '新增会员套餐'
})
//表单ref
const formRef = shallowRef()

//表单数据
const formData = ref({
    id: '',
    name: '',
    duration: '',
    duration_type: 1,
    is_perpetual: 0,
    sell_price: '',
    lineation_price: '',
    give_draw_number: '',
    is_retrieve: 0,
    retrieve_amount: '',
    benefits_ids: [] as any[],
    sort: 0,
    status: 1,
    chat_limit: '',
    give_chat_number: '',
    tag: '',
    is_quota: 0, //是否限购 1-是 0-否
    quota_value: '', //限购数量
    quota_tips: '' //限购提示语
})

const handleCheckAllChange = () => {
    formData.value.benefits_ids =
        optionsData.benefitsLists.length != formData.value.benefits_ids.length
            ? optionsData.benefitsLists.map((item) => item.id)
            : []
}

//表单校验规则
const formRules = reactive({
    name: [
        {
            required: true,
            message: '请输入套餐名称',
            trigger: ['blur']
        }
    ],
    duration: [
        {
            validator: (rule: object, value: string, callback: any) => {
                if (!formData.value.is_perpetual && !formData.value.duration) {
                    return callback(new Error('请输入套餐时长'))
                }
                callback()
            },
            trigger: ['blur', 'change']
        }
    ],
    sell_price: [
        {
            required: true,
            message: '请输入实际售价',
            trigger: ['blur']
        }
    ],
    retrieve_amount: [
        {
            required: true,
            message: '请输入优惠金额',
            trigger: ['blur']
        }
    ],
    status: [
        {
            required: true,
            message: '是否上架',
            trigger: ['blur']
        }
    ],
    is_quota: [
        {
            required: true,
            message: '是否限购',
            trigger: ['blur']
        }
    ],
    quota_value: [
        {
            validator: (rule: any, value: any, callback: any) => {
                if (value == 0 && formData.value.is_quota) {
                    return callback(new Error('限购次数不得小于或等于0'))
                }
                callback()
            },
            trigger: ['blur']
        }
    ]
})

const { optionsData } = useDictOptions<{
    benefitsLists: any[]
}>({
    benefitsLists: {
        api: getBenefitsListsAll
    }
})

//获取套餐详情
const getDetail = async (id: number) => {
    const data = await detialMenmber({
        id
    })
    formData.value = data
}

//提交
const handleSubmit = async () => {
    await formRef.value?.validate()
    query.id ? await editlMenmber(formData.value) : await addMenmber(formData.value)
    router.back()
}

onMounted(() => {
    query.id && getDetail(Number(query.id))
})
</script>

<style scoped lang="scss"></style>
