<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Hak Akses" description="Pengaturan Hak Akses"/>
      <div class="p-2">
        <RoleGrid title="Hak Akses" module="role"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import RoleGrid from '@/components/RoleGrid.vue'
import PageHeader from '@/components/PageHeader.vue';
</script>