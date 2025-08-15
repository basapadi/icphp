<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div 
      v-for="(stat, index) in stats" 
      :key="index" 
      class="bg-white border border-gray-200 rounded-lg shadow-sm"
    >
      <div class="flex flex-row items-center justify-between space-y-0 pb-2 p-4">
        <h3 class="text-sm font-medium text-gray-600">{{ stat.title }}</h3>
        <component :is="stat.icon" class="h-4 w-4 text-gray-400" />
      </div>
      <div class="px-4 pb-4">
        <div class="text-2xl font-bold text-gray-900">{{ stat.value }}</div>
        <div class="flex items-center mt-1">
          <TrendingUp v-if="stat.trend === 'up'" class="h-4 w-4 text-green-500 mr-1" />
          <TrendingDown v-else class="h-4 w-4 text-red-500 mr-1" />
          <span :class="`text-xs font-medium ${stat.trend === 'up' ? 'text-green-600' : 'text-red-600'}`">
            {{ stat.change }}
          </span>
          <span class="text-xs text-gray-500 ml-1">from last month</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { TrendingUp, TrendingDown, Users, ShoppingCart, DollarSign, Package } from 'lucide-vue-next'

const stats = ref([
  {
    title: "Total Revenue",
    value: "$45,231.89",
    change: "+20.1%",
    trend: "up",
    icon: DollarSign,
  },
  {
    title: "Active Users",
    value: "2,350",
    change: "+180.1%",
    trend: "up",
    icon: Users,
  },
  {
    title: "Orders",
    value: "12,234",
    change: "+19%",
    trend: "up",
    icon: ShoppingCart,
  },
  {
    title: "Products",
    value: "573",
    change: "-4.3%",
    trend: "down",
    icon: Package,
  },
])
</script>
