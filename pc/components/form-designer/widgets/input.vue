<template>
  <ElInput v-bind="props" v-model="value" />
</template>

<script setup lang="ts">
import type { InputProps } from 'element-plus'
interface Props extends Partial<InputProps> {}
const props = withDefaults(defineProps<Props>(), {
  showWordLimit: true
})
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
</script>
<style lang="scss" scoped></style>
