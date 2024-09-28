<template>
    <div class="">
        <el-form ref="formRef" class="ls-form" :model="formData" :rules="rules" label-width="120px">
            <el-card shadow="never" class="!border-none">
                <div class="text-xl font-medium mb-[20px]">绘画设置</div>

                <el-form-item label="免责声明">
                    <el-radio-group v-model="formData.disclaimer_status">
                        <el-radio :label="0">隐藏</el-radio>
                        <el-radio :label="1">显示</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="">
                    <div class="w-[420px]">
                        <el-input
                            v-model="formData.disclaimer_content"
                            :autosize="{ minRows: 7, maxRows: 7 }"
                            type="textarea"
                            show-word-limit
                            placeholder="请输入免责声明"
                        />
                    </div>
                </el-form-item>

                <el-form-item label="允许用户分享">
                    <div>
                        <el-switch
                            v-model="formData.is_allow_share"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">
                            开启后，允许用户分享绘画作品至广场
                        </div>
                    </div>
                </el-form-item>
                <template v-if="formData.is_allow_share">
                    <el-form-item label="分享一次奖励" prop="rewards">
                        <div>
                            <div class="flex">
                                <div>
                                    <el-input placeholder="请输入" v-model="formData.chat_rewards"></el-input>
                                </div>
                                <div class="ml-[10px]">条对话次数</div>
                            </div>
                            <div class="flex mt-[20px]">
                                <div>
                                    <el-input
                                        placeholder="请输入"
                                        v-model="formData.draw_rewards"
                                    ></el-input>
                                </div>
                                <div class="ml-[10px]">条绘画次数</div>
                            </div>
                        </div>
                    </el-form-item>
                    <el-form-item label="每天最多分享" prop="max_share">
                        <div class="flex">
                            <div>
                                <el-input placeholder="请输入" v-model="formData.max_share"></el-input>
                            </div>
                            <div class="ml-[10px]">次有奖励</div>
                        </div>
                    </el-form-item>
                    <el-form-item label="自动通过审核">
                        <div>
                            <el-switch
                                v-model="formData.is_auto_pass"
                                :active-value="1"
                                :inactive-value="0"
                            />
                            <div class="form-tips">
                                开启的话，用户分享绘画作品，无需后台人工审核，系统自动通过
                            </div>
                        </div>
                    </el-form-item>
                </template>
            </el-card>
        </el-form>
    </div>
    <footer-btns v-perms="['setting.draw_setting/setDrawSetting']">
        <el-button type="primary" @click="handleSubmit">保存</el-button>
    </footer-btns>
</template>
<script setup lang="ts">
import { getDrawConfig, setDrawConfig } from '@/api/setting/draw_setup'

const formData = ref<any>({
    is_allow_share: 0,
    is_auto_pass: 0,
    chat_rewards: '',
    draw_rewards: '',
    max_share: '',
    disclaimer_status: 0,
    disclaimer_content: ''
})

const rules = {
    // default_reply: [
    //     {
    //         required: true,
    //         message: '请输入默认回复内容'
    //     }
    // ]
}
/**
 * 初始化数据
 */
const getData = async () => {
    formData.value = await getDrawConfig()
}
getData()
/**
 * 保存数据
 */
const handleSubmit = async () => {
    await setDrawConfig(formData.value)
    getData()
}
</script>
