<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Faktur Penjualan" description="Daftar Faktur Penjualan"/>
      <div class="p-2">
        <DataTable title="Faktur Penjualan" module="transaction.invoice.sale"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue'
</script>