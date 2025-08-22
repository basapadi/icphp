<template>
  <div class="min-h-screen bg-gray-50">
    <AdminToolbar />

    <div class="flex">
      <!-- Sidebar -->
      <AdminSidebar/>

      <!-- Main Content -->
      <!-- Dynamic margin based on sidebar collapsed state -->
      <main 
        :class="[
          'flex-1 mt-10 bg-white min-h-[calc(100vh-4rem)] ml-64 overflow-hidden'
        ]"
      >
        <slot/>
      </main>
    </div>
  </div>
</template>

<script setup>
import AdminToolbar from '@/components/AdminToolbar.vue'
import AdminSidebar from '@/components/AdminSidebar.vue'
</script>
