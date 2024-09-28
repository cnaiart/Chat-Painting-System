<template>
  <el-checkbox-group v-bind="$attrs" v-model="value">
    <el-checkbox-button
      v-for="(item, index) in options"
      :key="index"
      :label="item"
      :name="item"
    />
  </el-checkbox-group>
</template>
<script setup lang="ts">
interface Props {
  modelValue: string[]
  options: string[]
  defaultValue: string[]
}
const props = withDefaults(defineProps<Props>(), {})
const emit = defineEmits<{
  (event: 'update:modelValue', value: any): void
}>()
const value = computed({
  get() {
    return props.modelValue
  },
  set(value) {
    emit('update:modelValue', value)
  }
})

onBeforeMount(() => {
  if (props.defaultValue) {
    value.value = props.defaultValue
  }
})
</script>
<style lang="scss" scoped>
.el-checkbox-group {
  :deep(.el-checkbox-button) {
    height: 36px;
    margin-bottom: 10px;
    margin-right: 13px;
    &.is-checked {
      .el-checkbox-button__inner {
        @apply bg-primary-light-9 text-primary;
      }
    }
    .el-checkbox-button__inner {
      height: 36px;
      border-radius: 8px;
      border: 1px solid transparent;
      padding: 10px 16px;
      @apply bg-page text-tx-primary;
      box-shadow: none;
    }
  }
}
</style>
