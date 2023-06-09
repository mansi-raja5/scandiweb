<script setup lang="ts">
import { ref } from 'vue'

import { useMutation, useQuery } from '@tanstack/vue-query'
import { Products } from '@/api/products.api'
import type { Id } from '@/types'

const { refetch: fetchProducts, data: products } = useQuery({
  queryKey: ['products'],
  queryFn: Products.list,
  keepPreviousData: true
})

const selectedProductIds = ref<string[]>([])

const isSelected = (id: string) => {
  return selectedProductIds.value.includes(id)
}

const { mutateAsync: deleteProducts } = useMutation({
  mutationFn: (productIds: Id[]) => Products.remove(productIds),
  onSuccess(response) {
    console.log('deleteProduct', response)
  }
})

const onDeleteSelectedProducts = async () => {
  //const deletePromises = selectedProductIds.value.map((id) => deleteProducts([id]))
  //await Promise.all(deletePromises)
  await deleteProducts(selectedProductIds.value);
  selectedProductIds.value = [];
  fetchProducts();
}
</script>

<template>
  <div class="container mx-auto pb-16">
    <div class="2xl:mt-16 flex justify-between items-center">
      <h1 class="text-2xl font-semibold">Products List</h1>

      <div class="flex items-center space-x-4">
        <select class="border rounded-lg border-gray-400 h-9 px-4">
          <option value="" disabled>None</option>
          <option value="remove">Mass Delete</option>
        </select>
        <button
          type="button"
          class="border rounded-lg border-gray-400 h-9 px-4 hover:bg-blue-500 hover:border-blue-500 hover:text-white"
          @click="onDeleteSelectedProducts"
        >
          Apply
        </button>
        <RouterLink
          to="/products/new"
          class="border rounded-lg border-gray-400 h-9 px-4 flex items-center justify-center hover:bg-blue-500 hover:border-blue-500 hover:text-white"
        >
          Add
        </RouterLink>
      </div>
    </div>

    <hr class="mt-3 border-gray-400" />

    <div class="grid grid-cols-2 2xl:grid-cols-6 gap-4 mt-4 2xl:mt-8">
      <div
        v-for="product in products || []"
        :key="product.product_id"
        :class="[
          'border rounded-lg p-4 relative',
          isSelected(product.product_id) ? 'border-blue-400 bg-blue-50' : 'border-gray-400 bg-white'
        ]"
      >
        <input
          v-model="selectedProductIds"
          :value="product.product_id"
          multiple
          type="checkbox"
          class="absolute top-2 left-2"
        />

        <div class="flex justify-center items-center flex-col">
          <h4 class="font-medium text-lg">{{ product.product_name }}</h4>
          <div>Category: {{ product.product_type_key }}</div>
          <div>Price {{ product.product_price }}$</div>

          <div v-for="attribute in product.attributes" :key="attribute.attribute_id">
            {{ attribute.attribute_label || `[${attribute.attribute_id}]` }}:
            {{ attribute.attribute_value }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
