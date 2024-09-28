<template>
    <el-card shadow="never" class="!border-none">
        <div class="text-lg font-medium">内容审核</div>
        <div class="mt-4">
            <el-form label-width="120px" :ref="formRef" :model="formData" :rules="rules">
                <el-form-item label="问题审核">
                    <div>
                        <el-switch
                            v-model="formData.ask_open"
                            :active-value="1"
                            :inactive-value="0"
                        ></el-switch>
                        <div class="form-tips">
                            默认关闭，开启后，问题会预先提交至百度，审核通过后才会回答。请填写相应的appid和key
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="内容审核">
                    <div>
                        <el-switch
                            v-model="formData.is_open"
                            :active-value="1"
                            :inactive-value="0"
                        ></el-switch>
                        <div class="form-tips">
                            默认关闭，开启后，后台计划任务扫描内容提交至百度，审核违规会隐藏内容，有一定时间延迟。请填写相应的appid和key
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="提示词审核">
                    <div>
                        <el-switch
                            v-model="formData.prompt_open"
                            :active-value="1"
                            :inactive-value="0"
                        ></el-switch>
                        <div class="form-tips">
                            绘画提示词审核，默认关闭，开启后，后台计划任务扫描内容提交至百度，审核违规会隐藏内容，有一定时间延迟。
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="图片审核">
                    <div>
                        <el-switch
                            v-model="formData.image_open"
                            :active-value="1"
                            :inactive-value="0"
                        ></el-switch>
                        <div class="form-tips">
                            默认关闭，开启后，后台计划任务扫描内容提交至百度，审核违规会隐藏内容，有一定时间延迟。
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="APPID" prop="app_id">
                    <div>
                        <el-input
                            v-model="formData.app_id"
                            class="w-[400px]"
                            placeholder="请输入APPID"
                        ></el-input>
                        <div class="form-tips">
                            如果您已开通，可直接填写；如果未开通，点击
                            <a
                                href="https://ai.baidu.com/solution/censoring?track=cp:ainsem|pf:pc|pp:chanpin-neirongshenhe|pu:neirongshenhe-baidu|ci:|kw:10008266"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                <el-button type="primary" link class="ml-2"> 前往开通 </el-button>
                            </a>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="APIKey" prop="api_key">
                    <el-input
                        v-model="formData.api_key"
                        class="w-[400px]"
                        placeholder="请输入APIKey"
                    ></el-input>
                </el-form-item>
                <el-form-item label="Secret Key" prop="secret_key">
                    <el-input
                        v-model="formData.secret_key"
                        class="w-[400px]"
                        placeholder="请输入Secret Key"
                    ></el-input>
                </el-form-item>
            </el-form>
        </div>
    </el-card>
    <footer-btns>
        <el-button type="primary" @click="submit">保存</el-button>
    </footer-btns>
</template>

<script setup lang="ts">
import { getConfig, setConfig } from '@/api/setting/examine'
import { onMounted, reactive, ref, shallowRef } from 'vue'
import type { FormInstance, FormRules } from 'element-plus'

//表单ref
const formRef = ref<FormInstance>()

//表单数据
const formData = ref({
    ask_open: 0, // 问题审核
    is_open: 0, //内容审核
    prompt_open: 0, // 绘画提示词审核
    image_open: 0, // 绘画图片审核
    app_id: '',
    api_key: '',
    secret_key: ''
})
//获取页面配置
const getPageConfig = async () => {
    formData.value = await getConfig()
}

const rules = reactive<FormRules>({
    app_id: [{ required: true, message: '请输入appid', trigger: 'change' }],
    api_key: [{ required: true, message: '请输入api_key', trigger: 'change' }],
    secret_key: [{ required: true, message: '请输入secret_key', trigger: 'change' }]
})

//保存
const submit = async () => {
    await setConfig(formData.value)
    getPageConfig()
}

onMounted(() => {
    getPageConfig()
})
</script>

<style scoped lang="scss"></style>
