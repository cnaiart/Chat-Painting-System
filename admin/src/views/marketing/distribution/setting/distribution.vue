<!-- 网站信息 -->
<template>
    <div class="user-setup">
        <el-card shadow="never" class="!border-none">
            <div class="font-medium mb-7">分销设置</div>
            <el-form ref="formRef" :model="formData" label-width="120px">
                <el-form-item label="功能状态">
                    <div>
                        <el-switch
                            v-model="formData.is_open"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">关闭分销功能时，不会产生新的分销佣金</div>
                    </div>
                </el-form-item>
                <el-form-item label="分销条件">
                    <div>
                        <el-radio-group v-model="formData.condition" class="ml-4">
                            <el-radio :label="1">无条件</el-radio>
                            <el-radio :label="2">申请分销</el-radio>
                        </el-radio-group>
                        <div class="form-tips" v-if="formData.condition == 1">
                            无需用户提交分销申请，全部用户都是分销商；仅对新注册的用户生效，旧的用户默认为申请分销
                        </div>
                        <div class="form-tips" v-if="formData.condition == 2">
                            用户需要提交分销申请，后台通过后成为分销商；仅对新注册的用户生效
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="自动通过审核" v-if="formData.condition == 2">
                    <div>
                        <el-radio-group v-model="formData.auto_audit" class="ml-4">
                            <el-radio :label="1">开启</el-radio>
                            <el-radio :label="0">关闭</el-radio>
                        </el-radio-group>
                        <div class="form-tips">用户提交分销申请后，无需审核，自动通过</div>
                    </div>
                </el-form-item>
                <el-form-item label="分销层级">
                    <div>
                        <el-radio-group v-model="formData.level" class="ml-4">
                            <el-radio :label="1">一级分销</el-radio>
                            <el-radio :label="2">二级分销</el-radio>
                        </el-radio-group>
                        <div class="form-tips">
                            允许发放佣金的分销层级，等级默认佣金比例在 分销等级 进行设置
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="一级分佣比例">
                    <div>
                        <el-input placeholder="请输入" v-model="formData.first_ratio">
                            <template #append>%</template>
                        </el-input>
                        <div class="form-tips">填0~100之间的数字</div>
                    </div>
                </el-form-item>
                <el-form-item label="二级分佣比例">
                    <div>
                        <el-input placeholder="请输入" v-model="formData.second_ratio">
                            <template #append>%</template>
                        </el-input>
                        <div class="form-tips">填0~100之间的数字</div>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>

        <footer-btns v-perms="['distribution.config/setConfig']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="userSetup">
import { getConfig, setdistributionConfig } from '@/api/marketing/distribution'

// 表单数据
const formData = ref({
    is_open: '',
    condition: 2,
    auto_audit: 0,
    level: 2,
    first_ratio: '',
    second_ratio: ''
})
const getData = async () => {
    formData.value = await getConfig()
}
const handleSubmit = async () => {
    await setdistributionConfig(formData.value)
    getData()
}
getData()
</script>

<style lang="scss" scoped></style>
