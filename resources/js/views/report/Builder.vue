<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Builder" description="Report Builder"/>
      <div class="p-2">
        <QueryBuilder/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import PageHeader from '@/components/PageHeader.vue'
import QueryBuilder from '@/components/QueryBuilder.vue'
</script>