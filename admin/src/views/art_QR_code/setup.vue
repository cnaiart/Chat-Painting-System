<template>
    <el-card shadow="never" class="!border-none">
        <div class="font-medium text-lg">生成设置</div>
        <el-form class="mt-4" label-width="90px">
            <el-form-item label="示例提示">
                <div>
                    <el-switch
                        v-model="formData.status"
                        :active-value="1"
                        :inactive-value="0"
                    ></el-switch>
                    <div class="form-tips">开启的话，前台显示实例按钮</div>
                </div>
            </el-form-item>
            <el-form-item label="示例内容">
                <div class="w-[500px]">
                    <el-input
                        placeholder="Many pink flowers are blooming"
                        type="textarea"
                        rows="7"
                        v-model="formData.content"
                    />
                </div>
            </el-form-item>
        </el-form>
    </el-card>
    <FooterBtns>
        <el-button v-perms="['setting.atrQRCode/setting']" @click="submit" type="primary"
            >保存</el-button
        >
    </FooterBtns>
</template>

<script setup lang="ts">
import { getExample, setExample } from '@/api/art_QR_code/index'
import feedback from '@/utils/feedback'

const formData = ref({
    status: 1,
    content: ''
})

//提交
const submit = async () => {
    await setExample({ ...formData.value })
    feedback.msgSuccess('保存成功！')
    getData()
}

const getData = async () => {
    formData.value = await getExample()
}

getData()
</script>

<style scoped lang="scss"></style>
