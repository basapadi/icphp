<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Piutang" description="Daftar Piutang"/>
      <div class="p-2">
         <DataTable title="Piutang" store_grid="receivable/grid" store_form="receivable/form"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue';
</script>