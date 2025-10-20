<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Barang" description="Daftar Barang"/>
      <div class="p-2 relative h-screen">
        <DataTable title="Barang" module="item"/>
       
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue';
</script>