<template>
    <div>
        <el-form label-width="100px">
            <el-form-item label="局限性提示" required>
                <el-switch v-model="content.show" :active-value="1" :inactive-value="0"></el-switch>
            </el-form-item>
            <el-form-item label="标题文字" required>
                <el-input
                    class="w-[300px]"
                    placeholder="请输入"
                    v-model="content.title"
                ></el-input>
            </el-form-item>
            <el-form-item label="副标题文字" required>
                <el-input
                    class="w-[300px]"
                    placeholder="请输入"
                    :autosize="{ minRows: 2, maxRows: 4 }"
                    v-model="content.sub_title"
                ></el-input>
            </el-form-item>
        </el-form>
    </div>
</template>

<script lang="ts" setup>
import type { PropType } from 'vue'
import type options from './options'

type OptionsType = ReturnType<typeof options>
const props = defineProps({
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
