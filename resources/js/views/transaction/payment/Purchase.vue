<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Pembayaran Pembelian" description="Daftar Pembayaran Pembelian"/>

      <div class="p-2">
        <DataTable title="Pembayaran Pembelian" module="purchasePayment"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue'
</script>