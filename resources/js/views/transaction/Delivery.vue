<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Pengiriman Barang" description="Daftar Pengiriman Barang"/>
      <div class="p-2">
        <DataTable title="Barang Terkirim" module="delivery"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue'
</script>