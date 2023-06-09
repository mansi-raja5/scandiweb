import type { Id, TProduct } from '@/types'
import { api } from '../axios'
import router from '@/router'

export const Products = {
  async list(): Promise<TProduct[]> {
    const config = {
      headers: {
        'Auth_key': '$2y$12$MQJ.uYoQKRROdlhPOHLDzueB5eEW3.saS499SiIgojekLb6KRrNaq'
      }
    };

    try {
      const response = await api.get('/product', config);
      const list = response?.data || {};

      if (Array.isArray(list)) return list;
      return Object.values(list);
    } catch (error) {
      console.error('Error fetching product list:', error);
      throw error;
    }
  },

  async add(product: any) {
    const config = {
      headers: {
        'Content-Type': 'application/json',
        'Auth_key': '$2y$12$MQJ.uYoQKRROdlhPOHLDzueB5eEW3.saS499SiIgojekLb6KRrNaq'
      }
    };

    try {
      const response = await api.post('/product', JSON.stringify(product), config);
      return response.data;
    } catch (error) {
      console.error('Error adding product:', error);
      throw error;
    }
  },

  async remove(productIds: Id[]) {
    const config = {
      headers: {
        'Content-Type': 'application/json',
        'Auth_key': '$2y$12$MQJ.uYoQKRROdlhPOHLDzueB5eEW3.saS499SiIgojekLb6KRrNaq'
      },
      data: productIds
    };

    try {
      const response = await api.delete('/product', config);
      return response.data;
    } catch (error) {
      console.error('Error removing product:', error);
      throw error;
    }
  }
};
