<script lang="ts" setup>
import type { TProduct, TProductType } from '@/types'

import router from '@/router'
import { reactive } from 'vue'
import { RouterLink } from 'vue-router'
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators'

import { ref } from 'vue'
import { watch } from 'vue'
import { computed } from 'vue'
import { Products } from '@/api/products.api'
import { Attributes } from '@/api/attributes.api'
import { useMutation, useQuery } from '@tanstack/vue-query'

import BaseInput from '@/components/base/BaseInput.vue'
import BaseSelect from '@/components/base/BaseSelect.vue'

const productTypeKey = ref<TProductType>('furniture')

const { data: attributesData } = useQuery({
  keepPreviousData: true,
  queryKey: ['attributes', productTypeKey],
  queryFn: () => Attributes.list(productTypeKey.value)
})

const form = reactive<Pick<TProduct, 'product_name' | 'product_sku' | 'product_price'>>({
  product_sku: '',
  product_name: '',
  product_price: ''
})

const rules = computed(() => {
  return {
    product_sku: { required, $autoDirty: true },
    product_name: { required, $autoDirty: true },
    product_price: { required, $autoDirty: true }
  }
})

const v$ = useVuelidate(rules, form)

// Attributes
const attributes = reactive<{
  [key: string]: string
}>({})

const attributeRules = computed(() => {
  return (attributesData.value || []).reduce(
    (carry, attribute) => ({
      ...carry,
      [attribute.attribute_key]: {
        required,
        $autoDirty: true
      }
    }),
    {}
  )
})

const vAttributes = useVuelidate(attributeRules, attributes)

watch(attributesData, (v) => {
  v?.forEach((attribute) => {
    attributes[attribute.attribute_key] = attributes[attribute.attribute_key] || ''
  })
  vAttributes.value?.$reset()
})
// End Attributes

const { mutate: addNewProduct } = useMutation({
  mutationFn: (product: any) => Products.add(product),
  onError(error) {
    console.log('onError', error)
  },
  onSuccess() {
    v$.value.$reset()
    vAttributes.value?.$reset()
    console.log('Router object:', router);

    router.push('/')
  }
})

const onSubmit = () => {
  addNewProduct({
    user_id: 1,
    product_sku: form.product_sku,
    product_name: form.product_name,
    product_price: form.product_price,
    product_type_key: productTypeKey.value,
    attributes: (attributesData.value || []).map((attribute) => ({
      attribute_id: +attribute.attribute_id,
      attribute_value: attributes[attribute.attribute_key]
    }))
  })
}

const validate = (callback = () => {}) => {
  v$.value.$touch()
  v$.value.$validate()

  vAttributes.value.$touch()
  vAttributes.value.$validate()

  if (v$.value.$error || vAttributes.value.$error) {
    return
  }
  callback()
}
</script>

<template>
  <div class="container mx-auto pb-16">
    <form @submit.prevent="validate(onSubmit)">
      <div class="2xl:mt-16 flex justify-between items-center">
        <h1 class="text-2xl font-semibold flex items-center space-x-2">
          <RouterLink to="/">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 1024 1024">
              <path fill="currentColor" d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z" />
              <path
                fill="currentColor"
                d="m237.248 512l265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z"
              />
            </svg>
          </RouterLink>
          <div>Product Add</div>
        </h1>
        <div class="flex items-center space-x-4">
          <button
            type="submit"
            class="border rounded-lg border-gray-400 h-9 px-4 hover:bg-blue-500 hover:border-blue-500 hover:text-white"
          >
            Save
          </button>
        </div>
      </div>
      <hr class="mt-3 border-gray-400" />

      <div class="mt-4 2xl:mt-8 max-w-xl w-full flex flex-col space-y-4">
        <BaseInput
          v-model="form.product_sku"
          :errors="v$.product_sku.$errors"
          id="sku"
          label="SKU"
          type="text"
        />
        <BaseInput
          v-model="form.product_name"
          :errors="v$.product_name.$errors"
          id="name"
          label="Name"
          type="text"
        />
        <BaseInput
          v-model.number="form.product_price"
          :errors="v$.product_price.$errors"
          id="price"
          label="Price"
          type="number"
        />
        <BaseSelect
          v-model="productTypeKey"
          id="product_type_key"
          label="Category"
          :options="[
            { value: 'dvd', text: 'DVD-disc' },
            { value: 'book', text: 'Book' },
            { value: 'furniture', text: 'Furniture' }
          ]"
        />
        <div v-for="attribute in attributesData || []" :key="attribute.attribute_id">
          <BaseInput
            v-model="attributes[attribute.attribute_key]"
            type="number"
            :id="attribute.attribute_key"
            :label="attribute.attribute_label"
            :errors="vAttributes[attribute.attribute_key].$errors"
          />
        </div>
      </div>
    </form>
  </div>
</template>
