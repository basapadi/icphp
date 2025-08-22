<template>
  <AdminLayout>
    <div class="max-w-full">
      <PageHeader title="Kontak" description="Daftar Kontak Pelanggan, Pemasok dan lainnya"/>
      <div class="p-2">
        <DataTable title="Kontak" store_grid="contact/grid" store_form="contact/form"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue'
</script>