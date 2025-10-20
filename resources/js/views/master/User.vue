<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Pengguna" description="Daftar Pengguna Aplikasi"/>
      <div class="p-2 relative h-screen">
        <DataTable title="Pengguna" module="user"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue';

</script>