<template>
  <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="flex flex-row items-center justify-between p-2">
        <h3 class="text-lg font-semibold text-gray-500">Jatuh Tempo Penerimaan</h3>
        <button class="p-2 hover:bg-gray-100 rounded">
          <MoreHorizontal class="h-4 w-4" />
        </button>
      </div>
      <div class="p-4 pt-2">
        <div class="space-y-2">
          <div 
            v-for="(order, index) in recentOrders" 
            :key="index" 
            class="flex items-center justify-between"
          >
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                <span class="text-xs font-medium text-gray-600">
                  {{ getInitials(order.customer) }}
                </span>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900">{{ order.customer }}</p>
                <p class="text-xs text-gray-500">{{ order.id }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-sm font-medium text-gray-900">{{ order.amount }}</p>
              <span :class="getStatusClass(order.status)">
                {{ order.status }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="flex flex-row items-center justify-between p-2">
        <h3 class="text-lg font-semibold text-gray-500">Jatuh Tempo Penjualan</h3>
        <button class="p-2 hover:bg-gray-100 rounded">
          <MoreHorizontal class="h-4 w-4" />
        </button>
      </div>
      <div class="p-4 pt-2">
        <div class="space-y-2">
          <div 
            v-for="(order, index) in recentOrders" 
            :key="index" 
            class="flex items-center justify-between"
          >
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                <span class="text-xs font-medium text-gray-600">
                  {{ getInitials(order.customer) }}
                </span>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900">{{ order.customer }}</p>
                <p class="text-xs text-gray-500">{{ order.id }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-sm font-medium text-gray-900">{{ order.amount }}</p>
              <span :class="getStatusClass(order.status)">
                {{ order.status }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="flex flex-row items-center justify-between p-2">
        <h3 class="text-lg font-semibold text-gray-500">Barang Paling Laku</h3>
        <button class="p-2 hover:bg-gray-100 rounded">
          <MoreHorizontal class="h-4 w-4" />
        </button>
      </div>
      <div class="p-4 pt-2">
        <div class="space-y-4">
          <div 
            v-for="(product, index) in topProducts" 
            :key="index" 
            class="flex items-center justify-between"
          >
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <span class="text-xs font-bold text-blue-600">#{{ index + 1 }}</span>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
                <p class="text-xs text-gray-500">{{ product.sales }} sales</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-sm font-medium text-gray-900">{{ product.revenue }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { MoreHorizontal, ArrowUpRight } from 'lucide-vue-next'

const recentOrders = ref([
  { id: "#3210", customer: "Olivia Martin", amount: "$42.25", status: "Completed" },
  { id: "#3209", customer: "Jackson Lee", amount: "$74.99", status: "Processing" },
  { id: "#3208", customer: "Isabella Nguyen", amount: "$99.99", status: "Completed" },
  { id: "#3207", customer: "William Kim", amount: "$39.95", status: "Pending" },
  { id: "#3206", customer: "Sofia Davis", amount: "$19.99", status: "Completed" },
])

const topProducts = ref([
  { name: "Wireless Headphones", sales: 1234, revenue: "$12,340" },
  { name: "Smart Watch", sales: 987, revenue: "$19,740" },
  { name: "Laptop Stand", sales: 756, revenue: "$7,560" },
  { name: "USB-C Cable", sales: 543, revenue: "$2,715" },
  { name: "Phone Case", sales: 432, revenue: "$4,320" },
])

const getInitials = (name) => {
  return name.split(" ").map(n => n[0]).join("")
}

const getStatusClass = (status) => {
  const baseClass = "inline-flex items-center px-2 py-1 rounded-full text-xs font-medium "
  switch (status) {
    case "Completed":
      return baseClass + "bg-green-100 text-green-800"
    case "Processing":
      return baseClass + "bg-blue-100 text-blue-800"
    default:
      return baseClass + "bg-yellow-100 text-yellow-800"
  }
}
</script>
