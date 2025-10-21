<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 mb-2">
    <div 
      v-for="(stat, index) in stats" 
      :key="index" 
      class="border border-gray-200 rounded-lg shadow-sm z-2"
    >
      <div class="flex flex-row items-center justify-between space-y-0 pb-2 p-4">
        <h3 class="text-sm font-semibold text-gray-500">{{ stat.title }}</h3>
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
          <span class="text-xs text-gray-500 ml-1">dari bulan terakhir</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { TrendingUp, TrendingDown, Users, ShoppingCart, DollarSign, Package } from 'lucide-vue-next'

export default {
  name: "DashboardCards",
  components: {
    TrendingUp,
    TrendingDown,
    Users,
    ShoppingCart,
    DollarSign,
    Package
  },
  props: {
    stats: {
      type: Array,
      default: []
    }
  },
  data() {
    return {
      data: {},
    };
  },
}
</script>
