<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Pemesanan Penjualan" description="Daftar Pemesanan Penjualan"/>

      <div class="p-2">
        <DataTable title="Pemesanan Penjualan" module="saleOrder"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue'
</script>