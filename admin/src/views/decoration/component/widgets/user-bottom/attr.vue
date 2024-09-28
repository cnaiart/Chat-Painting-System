<template>
    <div>
        <el-form label-width="70px">
            <el-form-item label="标题名称">
                <el-input v-model="content.title" class="w-[300px]" placeholder="请输入"></el-input>
            </el-form-item>
            <el-form-item label="内容文案">
                <el-input v-model="content.content" rows="4" type="textarea" class="w-[300px]" placeholder="请输入"></el-input>
            </el-form-item>
            <el-form-item label="支持复制">
                <div class="flex-1">
                    <el-switch v-model="content.canCopy" :active-value="1" :inactive-value="0"></el-switch>
                </div>
            </el-form-item>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import type options from './options'
type OptionsType = ReturnType<typeof options>
defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({})
    }
})
</script>

<style lang="scss" scoped></style>
