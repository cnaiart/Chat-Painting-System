<template>
    <div class="draw-square-setting">
        <el-card shadow="never" class="!border-none mt-4">
            <div class="font-medium mb-7">绘画广场设置</div>
            <el-form ref="formRef" :model="formData" label-width="120px">
                <el-form-item label="显示用户信息">
                    <div>
                        <el-switch
                            v-model="formData.is_show_user"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">
                            开启后，前台绘画广场图片显示分享用户的信息，默认开启
                        </div>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>

        <footer-btns v-perms="['draw.draw_square/setConfig']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="drawSquareSetting">
import type { DrawSquareSetFormType } from '@/api/ai_square/setting'
import { drawSquareGetConfig, drawSquareSetConfig } from '@/api/ai_square/setting'
import feedback from '@/utils/feedback'

const formData = reactive<DrawSquareSetFormType>({
    is_allow_share: 0,
    chat_rewards: 0,
    draw_rewards: 0,
    max_share: 0,
    is_auto_pass: 0,
    is_show_user: 0
})
const getData = async () => {
    const data = await drawSquareGetConfig()
    Object.keys(formData).map((item) => {
        //@ts-ignore
        formData[item] = data[item]
    })
}
const handleSubmit = async () => {
    await drawSquareSetConfig(formData)
    getData()
}
getData()
</script>

<style lang="scss" scoped></style>
