<template>
  <aside class="fixed left-0 top-12 w-64 h-[calc(100vh-4rem)] bg-white border-r border-gray-200 overflow-y-auto">
    <div class="bg-gray-100 border-b border-gray-300 px-3 py-2">
      <span class="text-sm font-medium text-gray-600">Menus</span>
    </div>

    <nav class="p-4">
      <ul class="space-y-1">
        <MenuItem 
          v-for="(item, index) in menuItems" 
          :key="index" 
          :item="item" 
          :level="0"
        />
      </ul>
    </nav>

    <!-- Modern status indicator at bottom -->
    <!-- <div class="absolute bottom-0 left-0 right-0 bg-gray-50 border-t border-gray-200 p-4">
      <div class="flex items-center space-x-3">
        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
        <span class="text-sm text-gray-600">System Online</span>
      </div>
    </div> -->
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import MenuItem from './MenuItem.vue'
import {
  BarChart3,
  Users,
  ShoppingCart,
  Package,
  Settings,
  FileText,
  Home,
  TrendingUp,
  Calendar,
  CreditCard,
} from 'lucide-vue-next'

const menuItems = ref([
  { icon: Home, label: "Dashboard", active: false, route: "/" },
  { icon: CreditCard, label: "POS Cashier", route: "/pos" },
  {
    icon: BarChart3,
    label: "Analytics",
    subItems: [
      { label: "Overview" },
      { label: "Sales Reports" },
      {
        label: "Advanced",
        subItems: [
          { label: "Custom Reports" },
          { label: "Data Export" },
          {
            label: "Integrations",
            subItems: [
              { label: "Google Analytics" },
              { label: "Facebook Pixel" },
              { label: "Custom APIs" }
            ],
          },
        ],
      },
    ],
  },
  {
    icon: Users,
    label: "Users",
    route: "/table",
    subItems: [
      { label: "All Users" },
      { label: "Roles & Permissions" },
      {
        label: "User Management",
        subItems: [
          { label: "Active Users" },
          { label: "Suspended Users" },
          {
            label: "User Groups",
            subItems: [
              { label: "Administrators" },
              { label: "Moderators" },
              { label: "Regular Users" }
            ],
          },
        ],
      },
    ],
  },
  {
    icon: ShoppingCart,
    label: "Orders",
    subItems: [
      { label: "All Orders" },
      { label: "Pending Orders" },
      { label: "Completed Orders" }
    ],
  },
  {
    icon: Package,
    label: "Products",
    subItems: [
      { label: "All Products" },
      { label: "Categories" },
      {
        label: "Inventory",
        subItems: [
          { label: "Stock Levels" },
          { label: "Low Stock Alerts" },
          {
            label: "Suppliers",
            subItems: [
              { label: "Active Suppliers" },
              { label: "Supplier Orders" },
              { label: "Supplier Performance" }
            ],
          },
        ],
      },
    ],
  },
  {
    icon: FileText,
    label: "Reports",
    subItems: [
      { label: "Sales Reports" },
      { label: "Financial Reports" },
      { label: "Custom Reports" }
    ],
  },
  { icon: TrendingUp, label: "Marketing" },
  { icon: Calendar, label: "Calendar" },
  {
    icon: Settings,
    label: "Settings",
    subItems: [
      { label: "General" },
      { label: "Security" },
      {
        label: "Advanced",
        subItems: [
          { label: "API Keys" },
          { label: "Webhooks" },
          {
            label: "System",
            subItems: [
              { label: "Database" },
              { label: "Cache" },
              { label: "Logs" }
            ],
          },
        ],
      },
    ],
  },
])
</script>
