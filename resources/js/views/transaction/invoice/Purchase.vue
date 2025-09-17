<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Faktur Pembelian" description="Daftar Faktur Pembelian"/>

      <div class="p-2">
        <DataTable title="Faktur Pembelian" module="transaction.invoice.purchase"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue'
</script>