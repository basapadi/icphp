<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Approval SO" description="Daftar Approval Pesanan Penjualan"/>
      <div class="p-2">
         <DataTable title="Approval SO" module="approvalSaleOrder" :defaultFilter="{
                operator: '_is',
                column: 'approval_status',
                value: 'pending'
            }"/>
      </div>
    </div>
  </AdminLayout>
</template>
<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue';
</script>