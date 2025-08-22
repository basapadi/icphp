<template>
  <AdminLayout>
    <div class="max-w-full">
      <PageHeader title="Hak Akses" description="Pengaturan Hak Akses"/>
      <div class="p-2">
        <RoleGrid title="Hak Akses" store_grid="role/grid" storeSingleUpdate="role/singleUpdate"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import RoleGrid from '@/components/RoleGrid.vue'
import PageHeader from '@/components/PageHeader.vue';
</script>