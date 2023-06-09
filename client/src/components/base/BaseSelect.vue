<script setup lang="ts">
import { useVModel } from '@vueuse/core'

const props = withDefaults(
  defineProps<{
    label: string
    modelValue: any
    options: { value: string; text: string }[]
  }>(),
  {
    id: Date.now(),
    options: () => []
  }
)

const emit = defineEmits(['update:modelValue'])
const value = useVModel(props, 'modelValue', emit)
</script>

<template>
  <div class="flex items-center space-x-4">
    <label :for="props.id" class="flex-none w-32">{{ props.label }}</label>
    <select
      v-model="value"
      :id="props.id"
      class="border border-gray-400 h-9 px-2 rounded focus:outline-blue-500 flex-1"
    >
      <option v-for="(option, index) in props.options" :key="index" :value="option.value">
        {{ option.text }}
      </option>
    </select>
  </div>
</template>
