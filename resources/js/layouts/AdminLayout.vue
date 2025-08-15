<template>
  <div class="min-h-screen bg-gray-50">
    <AdminToolbar />

    <div class="flex">
      <!-- Sidebar -->
      <AdminSidebar />

      <!-- Main Content -->
      <!-- Updated spacing for web-style header and sidebar -->
      <main class="flex-1 ml-side mt-12 bg-white min-h-[calc(100vh-4rem)]">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import AdminToolbar from '@/components/AdminToolbar.vue'
import AdminSidebar from '@/components/AdminSidebar.vue'

const statusMessage = ref('Application loaded successfully')
</script>
