<template>
  <AdminLayout>
  <div class="max-w-full relative">
    <PageHeader title="POS" description="Proses transaksi pelanggan"/>
  </div>
  <div class="p-2">
    <POSInterface />
  </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import POSInterface from '@/components/POSInterface.vue'
import PageHeader from '@/components/PageHeader.vue'
</script>
