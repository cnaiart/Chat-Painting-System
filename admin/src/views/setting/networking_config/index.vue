<template>
    <div class="notice-config">
        <el-form ref="formRef" :rules="rules" :model="formData" label-width="120px">
            <el-card shadow="never" class="!border-none">
                <div class="font-medium mb-7">联网配置</div>
                <el-form-item label="联网功能" prop="network_is_open">
                    <div>
                        <el-switch
                            v-model="formData.network_is_open"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <span class="mt-1 ml-2">
                            {{ formData.network_is_open ? '开启' : '关闭' }}
                        </span>
                        <div class="form-tips">
                            <p>开启后，前台显示联网功能，用户可自行选择是否开启；</p>
                            <p>国内模型基本已自带联网搜索，目前联网搜索只对GPT有效</p>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="额外扣费" prop="network_balance">
                    <div>
                        <el-input
                            type="number"
                            class="w-[375px]"
                            v-model="formData.network_balance"
                            placeholder="请输入额外扣费"
                            clearable
                        />
                        <div class="form-tips">填写0表示不额外扣对话条数</div>
                    </div>
                </el-form-item>
                <el-form-item label="搜索条数" prop="search_limit">
                    <div>
                        <el-input
                            type="number"
                            class="w-[375px]"
                            v-model="formData.search_limit"
                            placeholder="请输入搜索条数"
                            clearable
                        />
                        <div class="form-tips">联网搜索多少条内容，填写1-30之间的整数</div>
                    </div>
                </el-form-item>

                <el-form-item label="联网API" prop="network_api">
                    <div>
                        <el-input
                            class="w-[375px]"
                            v-model="formData.network_api"
                            placeholder="请输入联网API"
                            clearable
                        />
                        <div class="form-tips">
                            现在联网只支持使用https://lite.duckduckgo.com，如果想要国内服务器也可以使用联网功能，请填写https://lite.duckduckgo.com的反向代理API域名，不填写默认为：https://lite.duckduckgo.com
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="联网指令" prop="network_system">
                    <div>
                        <el-input
                            type="textarea"
                            class="w-[375px]"
                            v-model="formData.network_system"
                            placeholder="请输入联网指令"
                            clearable
                            resize="none"
                            :rows="6"
                        />
                        <div class="form-tips flex items-center">
                            ｛networkData｝就是联网搜索的数据，｛date}是日期
                            <el-button
                                v-copy="
                                    `{networkData} ，根据上面信息对我的问题进行回答，回答内容仅可能丰富，如有问到今天日期之类的问题，不要回答其他信息，立即直接回答:{date}`
                                "
                                link
                                type="primary"
                                >复制示例指令</el-button
                            >
                        </div>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>

        <footer-btns v-perms="['setting.ai_setting/setNetworkConfig']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="networkingConfig">
import { getNetworkingConfig, setNetworkingConfig } from '@/api/setting/networking_config'
import type { FormInstance, FormRules } from 'element-plus'
const formRef = ref<FormInstance>()

// 表单数据
const formData = reactive({
    network_is_open: 1,
    search_limit: '',
    network_api: '',
    network_balance: '',
    network_system: ''
})

// 表单验证
const rules = reactive<FormRules>({
    search_limit: [
        {
            required: true,
            message: '请输入搜索条数'
        }
    ],
    network_balance: [
        {
            required: true,
            message: '请输入额外扣费'
        }
    ]
})

// 获取公告设置数据
const getData = async () => {
    try {
        const data = await getNetworkingConfig()
        for (const key in formData) {
            //@ts-ignore
            formData[key] = data[key]
        }
        console.log(formData)
    } catch (error) {
        console.log('获取=>', error)
    }
}

// 保存公告设置数据
const handleSubmit = async () => {
    await formRef.value?.validate()
    try {
        await setNetworkingConfig(formData)
        await getData()
    } catch (error) {
        console.log('保存=>', error)
    }
}

getData()
</script>

<style lang="scss" scoped></style>
