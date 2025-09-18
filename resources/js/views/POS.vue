<template>
  <div class="min-h-screen bg-gray-200">
    <!-- Removed AdminHeader component -->
    <AdminToolbar v-if="!isFullscreen" />

    <div class="flex">
      <!-- Sidebar -->
      <AdminSidebar v-if="!isFullscreen" />

      <!-- Main Content -->
      <!-- Changed mt-20 to mt-12 to account for only toolbar height -->
       <main :class="`flex-1 bg-white ${!isFullscreen ? 'ml-side mt-10' : 'ml-0 mt-0'}`">
        <div class="max-w-full">
          <div class="mb-2 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <PageHeader title="POS" description="Proses transaksi pelanggan"/>
            <button
              @click="toggleFullscreen"
              class="flex items-center gap-1 px-2 py-1 text-xs bg-gray-200 hover:bg-gray-300 border border-gray-400 rounded-lg shadow-sm text-gray-700"
              :title="isFullscreen ? 'Exit Fullscreen' : 'Enter Fullscreen'"
            >
              <Minimize v-if="isFullscreen" :size="12" />
              <Maximize v-else :size="12" />
              {{ isFullscreen ? 'Exit' : 'Fullscreen' }}
            </button>
          </div>

          <!-- POS Interface -->
          <div class="p-2">
            <POSInterface />
          </div>
        </div>
      </main>
    </div>

    <div class="fixed bottom-0 left-0 right-0 bg-gray-100 border-t border-gray-300 px-2 py-1 text-xs text-gray-600 flex justify-between">
      <span>POS Ready</span>
      <span>Terminal ID: T001 | Cashier: Admin</span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import AdminToolbar from '@/components/AdminToolbar.vue'
import AdminSidebar from '@/components/AdminSidebar.vue'
import POSInterface from '@/components/POSInterface.vue'
import { Maximize, Minimize } from 'lucide-vue-next'
import PageHeader from '@/components/PageHeader.vue'

const isFullscreen = ref(false)
const collapsed = localStorage.getItem('sidebarCollapsed')

const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen()
    isFullscreen.value = true
  } else {
    document.exitFullscreen()
    isFullscreen.value = false
  }
}
</script>
