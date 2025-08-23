<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Penerimaan Barang" description="Daftar Penerimaan Barang"/>
      <div class="p-2">
        <DataTable title="Barang Diterima" store_grid="receive/grid" store_form="receive/form"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue'
</script>