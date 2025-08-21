<template>
  <AdminLayout>
    <div class="max-w-full">
      <div class="bg-gray-50 border-b border-gray-200 p-2">
        <h1 class="text-lg font-bold text-gray-900 mb-0">Penerimaan Barang</h1>
        <p class="text-xs text-gray-600">Daftar Penerimaan Barang Masuk</p>
      </div>

      <div class="p-2">
        <DataTable title="Barang Diterima" store_grid="receive/grid" store_form="receive/form"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
</script>