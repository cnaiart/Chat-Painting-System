<template>
    <div class="mt-[15px]">
        <div class="text-base">模型选择</div>

        <el-select v-model="value" placeholder="请选择风格" class="w-full mt-[10px]">
            <el-option
                v-for="(item) in modelList"
                :key="item.model_name"
                :label="item.model_name"
                :value="item.model_name"
            />
        </el-select>
<!--        <div class="style-container overflow-hidden">-->
<!--            <div-->
<!--                v-for="(item, index) in modelList"-->
<!--                :key="index"-->
<!--                class="float-left"-->
<!--                :class="{-->
<!--                    'style-active': item.model_name == value-->
<!--                }"-->
<!--                @click="value = item.model_name"-->
<!--            >-->
<!--                <div class="py-[3px] text-center">-->
<!--                    {{ item.model_name }}-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    </div>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelList?: any
        modelValue?: any
    }>(),
    {
        template: [],
        modelValue: ''
    }
)
const { modelValue: value } = useVModels(props, emit)

watchEffect(() => {
    const modelList = props.modelList
    if (modelList.length !== 0) {
        value.value = modelList[0].model_name
    }
})
</script>

<style scoped>
.style-container>div {
    cursor: pointer;
    margin-top: 10px;
    margin-right: 10px;
    border-radius: 4px;
    padding: 8px;
    border: 2px solid #e5e5e5;
}
.style-container>div:nth-child(3n) {
    margin-right: 0px;
}
.style-container .style-active {
    border: 2px solid var(--el-color-primary);
}
</style>
