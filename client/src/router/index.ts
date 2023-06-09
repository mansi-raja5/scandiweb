import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Products',
      component: () => import('../views/ProductsView.vue')
    },
    {
      path: '/products/new',
      name: 'NewProduct',
      component: () => import('../views/NewProductView.vue')
    }
  ]
})

export default router
