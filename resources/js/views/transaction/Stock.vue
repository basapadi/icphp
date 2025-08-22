<template>
  <AdminLayout>
    <div class="max-w-full">
      <PageHeader title="Stok Barang" description="Daftar Stok Barang"/>

      <div class="p-2">
        <DataTable title="Stok Barang" store_grid="stock/grid" store_form="stock/form"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue'
</script>