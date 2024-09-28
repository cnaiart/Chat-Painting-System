<template>
    <div class="mt-5" v-if="selectData.length">
        <el-table ref="tableDataRef" :data="selectData">
            <el-table-column label="软件名称" prop="product_name" min-width="280">
                <template #default="{ row }">
                    <div class="flex items-center">
                        <el-image style="width: 80px; height: 54px" :src="row.image" />
                        <div class="ml-2">{{ row.product_name }}</div>
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="开发团队" prop="partner_name" min-width="180" />
            <el-table-column label="应用类型" prop="type_desc" min-width="110">
                <template #default="scope">
                    <div v-if="scope.row.type == 1">PHP版本</div>
                    <div v-if="scope.row.type == 2">Java版本</div>
                </template>
            </el-table-column>
            <el-table-column label="价格" prop="price_range" min-width="160" />
            <el-table-column label="操作" min-width="120" fixed="right">
                <template #default="scope">
                    <div class="flex">
                        <el-button
                            type="primary"
                            link
                            @click="handleDeleteItem(scope.$index)"
                            :disabled="status === 2"
                            >移除</el-button
                        >
                    </div>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script lang="ts" setup>
import { computed, withDefaults } from 'vue'

const props = withDefaults(
    defineProps<{
        modelValue: any
        status: number
    }>(),
    {
        modelValue: [],
        status: 0
    }
)

const emit = defineEmits(['update:modelValue'])

const selectData: any = computed(() => {
    return props.modelValue || []
})

const handleDeleteItem = (index: number) => {
    selectData.value.splice(index, 1)
    emit('update:modelValue', selectData.value)
}

console.log('props', props.status)
</script>

<style lang="scss" scoped>
.move {
    @media screen and (max-width: 1536px) {
        @apply overflow-scroll;
    }
}
</style>
