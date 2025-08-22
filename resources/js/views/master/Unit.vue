<template>
  <AdminLayout>
    <div class="max-w-full">
      <PageHeader title="Satuan Barang" description="Daftar Satuan Barang"/>
      <div class="p-2">
         <DataTable title="Satuan Barang" store_grid="unit/grid" store_form="unit/form"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue';
</script>