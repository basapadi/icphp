<template>
  <header class="bg-gray-100 border-b border-gray-300 fixed top-0 left-0 right-0 z-50 h-8">
    <div class="flex items-center justify-between px-2 h-full bg-gradient-to-b from-gray-200 to-gray-100">
      <!-- Window Title -->
      <div class="flex items-center">
        <div class="w-4 h-4 bg-blue-500 mr-2 flex items-center justify-center">
          <span class="text-white font-bold text-xs">A</span>
        </div>
        <span class="text-sm font-medium text-gray-800">Admin Panel - Dashboard</span>
      </div>

      <!-- Window Controls -->
      <div class="flex items-center space-x-1">
        <button class="w-6 h-6 p-0 hover:bg-gray-300 flex items-center justify-center">
          <Minimize2 class="w-3 h-3" />
        </button>
        <button class="w-6 h-6 p-0 hover:bg-gray-300 flex items-center justify-center">
          <Square class="w-3 h-3" />
        </button>
        <button class="w-6 h-6 p-0 hover:bg-red-500 hover:text-white flex items-center justify-center">
          <X class="w-3 h-3" />
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { Minimize2, Square, X } from 'lucide-vue-next'
</script>
