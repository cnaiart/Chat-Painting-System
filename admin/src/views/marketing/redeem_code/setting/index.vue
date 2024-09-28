<!-- 卡密设置 -->
<template>
    <div class="user-setup">
        <el-form ref="formRef" :model="formData" label-width="120px">
            <el-card shadow="never" class="!border-none mt-4">
                <div class="font-medium mb-7">卡密设置</div>
                <el-form-item label="功能状态">
                    <div>
                        <el-switch
                            v-model="formData.is_open"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">默认关闭，开启的话在前台可以使用该功能</div>
                    </div>
                </el-form-item>
            </el-card>
            <el-card shadow="never" class="!border-none mt-4">
                <div class="font-medium mb-7">自定义卡密</div>

                <el-form-item label="是否显示">
                    <div>
                        <el-switch
                            v-model="formData.is_show"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">
                            是否显示卡密地址。显示的话在前台卡密兑换页面显示第三方卡密链接，点击可以跳转第三方购买
                        </div>
                    </div>
                </el-form-item>

                <el-form-item label="卡密购买地址">
                    <div class="w-[400px]">
                        <el-input
                            type="textarea"
                            :rows="10"
                            placeholder="请输入自定义卡密链接"
                            v-model="formData.buy_site"
                        >
                        </el-input>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>
        <footer-btns v-perms="['cardcode.cardCode/setConfig']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="redeemCodeSetup">
import { cardcodeConfigGet, cardcodeConfigSet } from '@/api/marketing/redeem_code'
import feedback from '@/utils/feedback'

const formData = reactive({
    is_show: 0,
    buy_site: '',
    is_open: 0
})
const getData = async () => {
    // formData.value = await cardcodeConfigGet()
    const data = await cardcodeConfigGet()
    Object.keys(formData).map((item) => {
        //@ts-ignore
        formData[item] = data[item]
    })
}
const handleSubmit = async () => {
    await cardcodeConfigSet(formData)
    feedback.msgSuccess('保存成功')
    getData()
}
getData()
</script>

<style lang="scss" scoped></style>
