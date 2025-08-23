<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Pengguna" description="Daftar Pengguna Aplikasi"/>
      <div class="p-2">
        <DataTable title="Pengguna" store_grid="user/grid" store_form="user/form"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue';

</script>