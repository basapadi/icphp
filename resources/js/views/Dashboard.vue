<template>
  <AdminLayout>
    <div class="max-w-full">
      <div class="mb-2 bg-gray-50 border-b border-gray-200 p-2">
        <h1 class="text-lg font-bold text-gray-900 mb-0">Dashboard</h1>
        <p class="text-xs text-gray-600">Welcome back! Here's what's happening with your business today.</p>
      </div>

      <!-- Dashboard Content -->
      <div class="p-2">
        <DashboardCards />
        <DashboardGrid />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DashboardCards from '@/components/DashboardCards.vue'
import DashboardGrid from '@/components/DashboardGrid.vue'
</script>
