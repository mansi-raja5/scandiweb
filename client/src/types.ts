export type Id = string

export type TDocument = {
  created_at: string
  updated_at: string
  deleted_at: string
}

export type TProductType = 'dvd' | 'book' | 'furniture'

export type TAttribute = {
  attribute_id: Id
  attribute_key: string
  attribute_label: string
  product_type_key: TProductType
}

export type TProductAttribute = {
  attribute_id: Id
  attribute_value: string
  attribute_label?: string
  attribute: TAttribute
}

export type TProduct = TDocument & {
  product_id: Id
  user_id: Id
  product_name: string
  product_sku: string
  product_price: string
  product_type_key: TProductType
  attributes: TProductAttribute[]
}
