<script setup lang="ts">
import { useVModel } from '@vueuse/core'

const props = withDefaults(
  defineProps<{
    label: string
    modelValue: string
    type: 'text' | 'number'
    errors: {
      $uid: string
      $message: string
    }[]
  }>(),
  {
    type: 'text',
    id: Date.now(),
    errors: () => []
  }
)

const emit = defineEmits(['update:modelValue'])
const value = useVModel(props, 'modelValue', emit)
</script>

<template>
  <div class="flex items-center space-x-4">
    <label :for="props.id" class="flex-none w-32">{{ props.label }}</label>
    <div class="flex-1">
      <input
        v-model="value"
        :id="props.id"
        :type="props.type"
        :class="[
          'border h-9 px-2 rounded w-full',
          !props.errors.length
            ? 'border-gray-400 focus:outline-blue-500'
            : 'border-red-500 focus:outline-red-500'
        ]"
      />
      <div
        class="text-sm text-red-500 flex flex-wrap"
        v-for="error of props.errors"
        :key="error.$uid"
      >
        <div>{{ error.$message }}</div>
      </div>
    </div>
  </div>
</template>
