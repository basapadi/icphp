<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Keranjang Sampah" description="Data Dihapus"/>
      <div class="p-2">
         <DataTable title="Keranjang Sampah" module="trash"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue';
</script>