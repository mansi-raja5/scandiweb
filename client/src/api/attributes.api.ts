import { api } from '@/axios'
import type { TAttribute } from '@/types'

export const Attributes = {
  async list(product_type_key: string): Promise<TAttribute[]> {
    const config = {
      headers: {
        'Content-Type': 'application/json',
        'Auth_key': '$2y$12$MQJ.uYoQKRROdlhPOHLDzueB5eEW3.saS499SiIgojekLb6KRrNaq'
      }
    };
    const list =
      (
        await api.post(
          '/attribute',
          JSON.stringify({
            product_type_key
          }),
          config
        )
      )?.data || {}

    if (Array.isArray(list)) return list
    return Object.values(list)
  }
}
