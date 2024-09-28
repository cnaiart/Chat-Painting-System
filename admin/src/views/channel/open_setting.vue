<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-alert
                type="warning"
                title="温馨提示：填写微信开放平台配置，请前往微信开放平台创建移动应用并完成认证；该配置主要用于移动应用的微信登录和微信支付"
                :closable="false"
                show-icon
            />
        </el-card>
        <el-form ref="formRef" :model="formData" :rules="formRules" label-width="160px">
            <el-card class="!border-none mt-4" shadow="never">
                <div class="font-medium mb-7">微信开放平台</div>
                <el-form-item label="AppID" prop="app_id">
                    <div class="w-80">
                        <el-input v-model="formData.app_id" placeholder="请输入AppID" />
                    </div>
                </el-form-item>
                <el-form-item label="AppSecret" prop="app_secret">
                    <div>
                        <div class="w-80">
                            <el-input v-model="formData.app_secret" placeholder="请输入AppSecret" />
                        </div>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>
        <footer-btns v-perms="['channel.open_setting/setConfig']">
            <el-button type="primary" @click="handelSave">保存</el-button>
        </footer-btns>
    </div>
</template>
<script lang="ts" setup name="wxDevConfig">
import { getOpenSettingConfig, setOpenSettingConfig } from '@/api/channel/open_setting'
import type { FormInstance } from 'element-plus'

const formData = reactive({
    app_id: '',
    app_secret: ''
})

const formRef = shallowRef<FormInstance>()
const formRules = {
    app_id: [
        {
            required: true,
            message: '请输入AppID',
            trigger: ['blur', 'change']
        }
    ],
    app_secret: [
        {
            required: true,
            message: '请输入AppSecret',
            trigger: ['blur', 'change']
        }
    ]
}

const getDetail = async () => {
    const data = await getOpenSettingConfig()
    for (const key in formData) {
        //@ts-ignore
        formData[key] = data[key]
    }
}

const handelSave = async () => {
    await formRef.value?.validate()
    await setOpenSettingConfig(formData)
    getDetail()
}

getDetail()
</script>
