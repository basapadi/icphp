<template>
  <AdminLayout>
    <div class="max-w-full">
      <div class="mb-2 bg-gray-50 border-b border-gray-200 p-2">
        <h1 class="text-lg font-bold text-gray-900 mb-0">Menu</h1>
        <p class="text-xs text-gray-600">Daftar Menu</p>
      </div>

      <div class="p-2">
        <DataTable title="Daftar Menu" urlGrid="dataMenu/grid"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
</script>