<template>
  <AdminLayout>
    <div class="max-w-full">
      <div class="bg-gray-50 border-b border-gray-200 p-2">
        <h1 class="text-lg font-bold text-gray-900 mb-0">Hak Akses</h1>
        <p class="text-xs text-gray-600">Daftar Hak Akses</p>
      </div>

      <div class="p-2">
        <RoleGrid title="Hak Akses" store="role/grid" storeSingleUpdate="role/singleUpdate"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import RoleGrid from '@/components/RoleGrid.vue'
</script>