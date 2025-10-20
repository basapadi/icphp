<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Kontak" description="Daftar Kontak Pelanggan, Pemasok dan lainnya"/>
      <div class="p-2">
        <DataTable title="Kontak" module="contact"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue'
</script>