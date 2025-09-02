<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Penyesuaian Barang" description="Daftar Penyesuaian Barang"/>

      <div class="p-2">
        <DataTable title="Penyesuaian Barang" module="adjustment"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue'
</script>