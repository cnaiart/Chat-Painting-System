<!-- 网站信息 -->
<template>
    <div class="user-setup">
        <el-card shadow="never" class="!border-none">
            <div class="font-medium mb-7">提现设置</div>
            <el-form ref="formRef" :model="formData" label-width="120px">
                <el-form-item label="提现方式">
                    <div>
                        <el-checkbox-group v-model="formData.type">
                            <el-checkbox label="1">支付宝</el-checkbox>
                            <el-checkbox label="2">微信零钱</el-checkbox>
                            <el-checkbox label="3">微信收款码</el-checkbox>
                            <el-checkbox label="4">支付宝收款码</el-checkbox>
                        </el-checkbox-group>
                        <div class="form-tips">微信零钱接口默认为【商家转账到零钱】；</div>
                    </div>
                </el-form-item>

                <el-form-item label="最低提现金额">
                    <div>
                        <div class="w-[200px]">
                            <el-input placeholder="请输入" v-model="formData.min_money">
                                <template #append>元</template>
                            </el-input>
                        </div>
                        <div class="form-tips">
                            提现需满足最低提现金额才能提交提现申请，保留2位小数
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="最高提现金额">
                    <div>
                        <div class="w-[200px]">
                            <el-input placeholder="请输入" v-model="formData.max_money">
                                <template #append>元</template>
                            </el-input>
                        </div>
                        <div class="form-tips">提现允许的最高提现金额，保留2位小数</div>
                    </div>
                </el-form-item>
                <el-form-item label="提现手续费">
                    <div>
                        <div class="w-[200px]">
                            <el-input placeholder="请输入" v-model="formData.handling_fee">
                                <template #append>%</template>
                            </el-input>
                        </div>
                        <div class="form-tips">填写0或者不填写则表示不扣手续费，保留2位小数</div>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card shadow="never" class="!border-none mt-4">
            <div class="font-medium mb-7">提现说明</div>
            <el-form ref="formRef" :model="formData" label-width="120px">
                <el-form-item label="是否显示">
                    <div>
                        <el-switch v-model="formData.open" :active-value="1" :inactive-value="0" />
                        <div class="form-tips">默认显示，如隐藏的话，则在提现页面将不显示</div>
                    </div>
                </el-form-item>

                <el-form-item label="提现说明内容">
                    <div class="w-[400px]">
                        <el-input
                            type="textarea"
                            :rows="10"
                            placeholder="请输入"
                            v-model="formData.explain"
                        >
                        </el-input>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>

        <footer-btns v-perms="['distribution.withdraw/setConfig']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="userSetup">
import { whitdrawConfig, setConfig } from '@/api/marketing/distribution'

const formData = ref({
    open: 1,
    type: [],
    min_money: '',
    max_money: '',
    handling_fee: '',
    explain: ''
})
const getData = async () => {
    // formData.value = await whitdrawConfig()
    const data = await whitdrawConfig()
    Object.keys(formData.value).map((item) => {
        //@ts-ignore
        formData.value[item] = data[item]
    })
}
const handleSubmit = async () => {
    await setConfig(formData.value)
    getData()
}
getData()
</script>

<style lang="scss" scoped></style>
