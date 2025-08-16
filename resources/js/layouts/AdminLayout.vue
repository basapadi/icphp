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
          'flex-1 mt-10 bg-white min-h-[calc(100vh-4rem)] transition-all duration-300 ml-64'
        ]"
      >
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, provide } from 'vue'
import AdminToolbar from '@/components/AdminToolbar.vue'
import AdminSidebar from '@/components/AdminSidebar.vue'

const statusMessage = ref('Application loaded successfully')
</script>
