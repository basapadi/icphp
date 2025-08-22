<template>
  <AdminLayout>
    <div class="max-w-full">
      <PageHeader title="Penjualan Barang" description="Daftar Penjualan Barang"/>
      <div class="p-2">
        <DataTable title="Barang Terjual" store_grid="sale/grid" store_form="sale/form"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHEader.vue'
</script>