<template>
    <div class="">
        <el-card class="!border-none" shadow="never">
            <el-page-header :content="$route.meta.title" @back="$router.back()" />
        </el-card>
        <el-form
            ref="formRef"
            :rules="rules"
            class="ls-form mt-4"
            :model="formData"
            label-width="180px"
        >
            <el-card shadow="never" class="!border-none">
                <div class="text-xl font-medium mb-[20px]">AI参数配置</div>
                <!-- <el-form-item label="敏感词库" prop="name">
                    <div class="w-80">
                        <el-radio v-model="formData.is_sensitive" :label="1">开启</el-radio>
                        <el-radio v-model="formData.is_sensitive" :label="0">关闭</el-radio>
                    </div>
                </el-form-item> -->
                <!-- <el-form-item label="AI接口" prop="api_type">
                    <div class="w-80">
                        <el-radio v-model="formData.api_type" label="open_ai">OpenAI</el-radio>
                    </div>
                </el-form-item> -->
                <el-form-item label="apiKey" prop="api_key">
                    <div>
                        <div>
                            <div
                                class="flex mb-1"
                                v-for="(item, index) in formData.api_key"
                                :key="index"
                            >
                                <el-input
                                    placeholder="请输入API接口密钥"
                                    class="w-[400px]"
                                    v-model="formData.api_key[index]"
                                ></el-input>
                                <el-button
                                    v-perms="['setting.aiSetting/queryBalance']"
                                    class="ml-2"
                                    type="primary"
                                    @click="checkBalance(formData.api_key[index])"
                                    >查询余额</el-button
                                >
                                <el-button
                                    type="primary"
                                    class="ml-2"
                                    v-if="index == 0"
                                    @click="addApiKey"
                                    plain
                                    >添加key</el-button
                                >
                                <el-button
                                    type="danger"
                                    class="ml-2"
                                    v-else
                                    @click="delApiKey(index)"
                                    plain
                                    >删除key</el-button
                                >
                            </div>
                        </div>
                        <div class="form-tips">需自备GPT账号，请根据您当地的法规酌情使用</div>
                    </div>
                </el-form-item>
                <el-form-item label="模型" prop="name">
                    <div>
                        <el-select class="w-[400px]" v-model="formData.model">
                            <el-option
                                v-for="item in formData.model_list"
                                :value="item"
                                :label="item"
                                :key="item"
                            ></el-option>
                        </el-select>
                        <div class="form-tips">默认：text-davinci-003</div>
                    </div>
                </el-form-item>
                <!--                <el-form-item label="最大长度(max_tokens)" prop="name">-->
                <!--                    <div>-->
                <!--                        <el-input-->
                <!--                            placeholder="请输入API接口密钥"-->
                <!--                            class="w-[280px]"-->
                <!--                            v-model="formData.max_tokens"-->
                <!--                        ></el-input>-->
                <!--                        <div class="form-tips">-->
                <!--                            每次回答的最大字符长度。如果设置过小，回答中间可能被切断。如果设置过大，则可能产生更多的计费。-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </el-form-item>-->
                <el-form-item label="参数设置" prop="context_num">
                    <div class="flex flex-wrap justify-between">
                        <div class="w-[45%]">
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                content="生成文本的最大长度，取值范围为1~5之间的整数"
                                placement="top-start"
                            >
                                <div class="flex items-center">
                                    <span class="mr-[4px] mt-[2px]">上下文总数</span>
                                    <el-icon size="16px"><QuestionFilled /></el-icon>
                                </div>
                            </el-tooltip>
                            <el-slider v-model="formData.context_num" :min="0" :max="5" />
                        </div>
                        <div class="w-[45%] ml-[10px]">
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                content="为每个输入消息生成多个回复，取值范围为1~5之间的整数。"
                                placement="top-start"
                            >
                                <div class="flex items-center">
                                    <span class="mr-[4px] mt-[2px]">回复条数</span>
                                    <el-icon size="16px"><QuestionFilled /></el-icon>
                                </div>
                            </el-tooltip>
                            <el-slider v-model="formData.n" :min="1" :max="5" />
                        </div>
                        <div class="w-[45%]">
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                content="用于控制生成文本的多样性，取值范围为0~1之间的浮点数，建议取值0.9左右。"
                                placement="top-start"
                            >
                                <div class="flex items-center">
                                    <span class="mr-[4px] mt-[2px]">随机属性</span>
                                    <el-icon size="16px"><QuestionFilled /></el-icon>
                                </div>
                            </el-tooltip>
                            <el-slider v-model="formData.top_p" step="0.1" :min="0" :max="1" />
                        </div>
                        <div class="w-[45%] ml-[10px]">
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                content="用于控制生成文本的随机性，取值范围为0~1之间的浮点数，建议取值0.7左右。"
                                placement="top-start"
                            >
                                <div class="flex items-center">
                                    <span class="mr-[4px] mt-[2px]">词汇属性</span>
                                    <el-icon size="16px"><QuestionFilled /></el-icon>
                                </div>
                            </el-tooltip>
                            <el-slider
                                v-model="formData.temperature"
                                step="0.1"
                                :min="0"
                                :max="1"
                            />
                        </div>
                        <div class="w-[45%]">
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                content="用于控制生成文本中是否出现给定的关键词，取值范围为0~1之间的浮点数，建议取值0.5左右。"
                                placement="top-start"
                            >
                                <div class="flex items-center">
                                    <span class="mr-[4px] mt-[2px]">话题属性</span>
                                    <el-icon size="16px"><QuestionFilled /></el-icon>
                                </div>
                            </el-tooltip>
                            <el-slider
                                v-model="formData.presence_penalty"
                                step="0.1"
                                :min="0"
                                :max="1"
                            />
                        </div>
                        <!-- <div class="w-[45%] ml-[10px]">
                            <el-tooltip
                                class="box-item"
                                effect="dark"
                                content="用于控制生成文本中重复的程度，取值范围为0~1之间的浮点数，建议取值0.5左右"
                                placement="top-start"
                            >
                                <div class="flex items-center">
                                    <span class="mr-[4px] mt-[2px]">重复属性</span>
                                    <el-icon size="16px"><QuestionFilled /></el-icon>
                                </div>
                            </el-tooltip>
                            <el-slider
                                v-model="formData.frequency_penalty"
                                step="0.1"
                                :min="0"
                                :max="1"
                            />
                        </div> -->
                    </div>
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-radio-group v-model="formData.status">
                        <el-radio :label="0">关闭</el-radio>
                        <el-radio :label="1">开启</el-radio>
                    </el-radio-group>
                </el-form-item>
                <!-- <el-form-item label="最大回复字数" prop="name">
                    <div>
                        <el-input class="w-[280px]" v-model="formData.new_user_give">
                            <template #append> 个字 </template>
                        </el-input>
                    </div>
                </el-form-item>
                <el-form-item label="随机属性" prop="name">
                    <div class="w-[280px]">
                        <el-slider v-model="formData.new_user_give" />
                    </div>
                </el-form-item>
                <el-form-item label="词汇属性" prop="name">
                    <div class="w-[280px]">
                        <el-slider v-model="formData.new_user_give" />
                    </div>
                </el-form-item>
                <el-form-item label="话题属性" prop="name">
                    <div class="w-[280px]">
                        <el-slider v-model="formData.new_user_give" />
                    </div>
                </el-form-item>
                <el-form-item label="重复属性" prop="name">
                    <div class="w-[280px]">
                        <el-slider v-model="formData.new_user_give" />
                    </div>
                </el-form-item> -->
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="text-xl font-medium mb-[20px]">域名配置</div>
                <el-form-item label="自定义API域名" prop="agency_api">
                    <div>
                        <div class="flex">
                            <el-input
                                placeholder="请输入自定义API域名"
                                class="w-[400px]"
                                v-model="formData.agency_api"
                            ></el-input>
                        </div>

                        <div class="form-tips">
                            反向代理API域名，不填写默认为：https://api.openai.com
                        </div>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>
        <footer-btns v-perms="['setting.aiSetting/setconfig']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
        <BalancePop ref="popRef"></BalancePop>
    </div>
</template>
<script setup lang="ts">
import { getConfig, setConfig } from '@/api/setting/AI'
import type { FormInstance } from 'element-plus'
import BalancePop from './components/balancePop.vue'
import feedback from '@/utils/feedback'
const { query } = useRoute()
//弹框ref
const popRef = shallowRef()

// const formData = ref({
//     api_key: [''],
//     api_type: 'open_ai',
//     is_sensitive: 0,
//     // max_tokens: 150,
//     model: 'text-davinci-003',
//     new_user_give: '',
//     agency_api: '',
//     global_directives: '',
//     model_list: [],

//     n: 0,
//     temperature: 0.7,
//     context_num: 2,
//     top_p: 0.9,
//     presence_penalty: 0.5,
//     frequency_penalty: 0.5,
//     status: 0
// })
/**
 * 获取初始化数据
 */
const getData = async () => {
    formData.value = await getConfig({
        key: query.key
    })
    if (formData.value.api_key.length === 0) {
        formData.value.api_key = ['']
    }
}

getData()

const formRef = ref<FormInstance>()
/**
 * 保存
 */
const handleSubmit = async () => {
    await formRef.value?.validate()
    await setConfig(formData.value)
}
/**
 * 表单校验
 */
const rules = {
    api_key: [
        {
            required: true,
            message: '请输入秘钥',
            trigger: ['blur']
        }
    ],
    api_type: [
        {
            required: true,
            message: '请选择',
            trigger: ['blur']
        }
    ]
}
//添加密钥
const addApiKey = () => {
    formData.value.api_key.push('')
}
//删除密钥
const delApiKey = (index: any) => {
    formData.value.api_key.splice(index, 1)
}

//查询余额
const checkBalance = (key: any) => {
    popRef.value.open(key)
}
</script>
