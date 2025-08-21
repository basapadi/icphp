<template>
  <AdminLayout>
    <div class="max-w-full">
      <div class="bg-gray-50 border-b border-gray-200 p-2">
        <h1 class="text-lg font-bold text-gray-900 mb-0">Penjualan Barang</h1>
        <p class="text-xs text-gray-600">Daftar Penjualan Barang</p>
      </div>

      <div class="p-2">
        <DataTable title="Barang Terjual" store_grid="sale/grid" store_form="sale/form"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
</script>