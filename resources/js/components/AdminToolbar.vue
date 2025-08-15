<template>
  <div class="bg-gray-50 border-b border-gray-300 fixed left-0 right-0 z-40 h-12">
    <div class="flex items-center justify-between px-4 h-full">
      <!-- Menu Bar -->
      <div class="flex items-center space-x-6">
        <button class="text-sm text-gray-700 hover:bg-gray-200 px-2 py-1">File</button>
        <button class="text-sm text-gray-700 hover:bg-gray-200 px-2 py-1">Edit</button>
        <button class="text-sm text-gray-700 hover:bg-gray-200 px-2 py-1">View</button>
        <button class="text-sm text-gray-700 hover:bg-gray-200 px-2 py-1">Tools</button>
        <button class="text-sm text-gray-700 hover:bg-gray-200 px-2 py-1">Help</button>
      </div>

      <!-- Toolbar Actions -->
      <div class="flex items-center space-x-2">
        <div class="relative">
          <Search class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 w-3 h-3" />
          <input 
            v-model="searchQuery"
            placeholder="Search..." 
            class="pl-7 h-7 text-xs bg-white border border-gray-300 rounded w-48 px-2"
          />
        </div>
        <div class="w-px h-6 bg-gray-300"></div>
        <button class="h-7 w-7 p-0 relative hover:bg-gray-200 flex items-center justify-center">
          <Bell class="w-3 h-3" />
          <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>
        <button class="h-7 w-7 p-0 hover:bg-gray-200 flex items-center justify-center">
          <Settings class="w-3 h-3" />
        </button>
        <button class="h-7 w-7 p-0 hover:bg-gray-200 flex items-center justify-center">
          <User class="w-3 h-3" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Bell, Search, Settings, User } from 'lucide-vue-next'

const searchQuery = ref('')
</script>
