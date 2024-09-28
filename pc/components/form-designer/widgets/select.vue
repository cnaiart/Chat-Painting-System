<template>
  <ElSelect v-bind="$attrs" v-model="value">
    <ElOption
      v-for="(item, index) in options"
      :key="index"
      :label="item"
      :value="item"
    />
  </ElSelect>
</template>

<script setup lang="ts">
const props = withDefaults(
  defineProps<{
    defaultValue: string
    modelValue: any
    options: string[]
  }>(),
  {
    options: () => []
  }
)
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
.el-select {
  width: 100%;
}
</style>
