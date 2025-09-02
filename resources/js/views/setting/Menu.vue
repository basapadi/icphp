<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Menu" description="Daftar Menu Aplikasi"/>
      <div class="p-2">
        <DataTable title="Daftar Menu" module="dataMenu"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue';
</script>