<template>
  <AdminLayout>
    <div class="max-w-full">
      <PageHeader title="Barang" description="Daftar Barang"/>

      <div class="p-2">
        <DataTable title="Barang" store_grid="item/grid" store_form="item/form"/>
       
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue';
</script>