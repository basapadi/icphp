<template>
  <div class="bg-gray-50 border-b border-gray-300 fixed left-0 right-0 pt-1 z-40 h-10">
    <div class="flex items-center justify-between px-2 h-full">
      <!-- Menu Bar -->
      <div class="flex items-center">
        <button class="text-xs text-gray-500 hover:bg-gray-200 px-3 py-1">Alat</button>
        <button class="text-xs text-gray-500 hover:bg-gray-200 px-3 py-1">Bantuan</button>
        <button class="text-xs text-gray-500 hover:bg-gray-200 px-3 py-1">Panduan</button>
      </div>

      <!-- Toolbar Actions -->
      <div class="flex items-center space-x-2">
        <div class="w-px h-6 bg-gray-300"></div>
        <button class="h-7 w-7 p-0 relative hover:bg-gray-200 flex items-center justify-center">
          <Bell class="w-3 h-3" />
          <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>
        
        <!--  Added profile dropdown with logout button -->
        <div class="relative">
          <button 
            @click="showProfileDropdown = !showProfileDropdown"
            class="h-7 w-7 p-0 hover:bg-gray-200 flex items-center justify-center rounded"
          >
            <User class="w-3 h-3" />
          </button>
          
          <!-- Profile Dropdown -->
          <div 
            v-if="showProfileDropdown"
            class="absolute right-0 mt-1 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50"
          >
            <div class="px-4 py-3 border-b border-gray-100">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                  <span class="text-white text-sm font-medium">IC</span>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ user.username }}</p>
                  <p class="text-xs text-gray-500">{{ user.email }}</p>
                </div>
              </div>
            </div>
            
            <div class="py-1">
              <button 
                @click="handleProfileClick"
                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                <User class="w-4 h-4 mr-3" />
                Profil
              </button>
              <!-- <button 
                @click="handleSettingsClick"
                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                <Settings class="w-4 h-4 mr-3" />
                Settings
              </button> -->
              <div class="border-t border-gray-100 my-1"></div>
              <button 
                @click="handleLogout"
                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50"
              >
                <LogOut class="w-4 h-4 mr-3" />
                Keluar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!--  Added overlay to close dropdown when clicking outside -->
    <div 
      v-if="showProfileDropdown"
      @click="showProfileDropdown = false"
      class="fixed inset-0 z-40"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted,computed } from 'vue'
import { Bell, Search, Settings, User, LogOut } from 'lucide-vue-next'
import { useStore, mapGetters, mapActions } from 'vuex'
import { useRouter } from 'vue-router'
const router = useRouter()
const store = useStore()

const { getUser } = mapGetters('auth', ['getUser'])
const actions = mapActions('auth', ['logout'])
const logout = (credentials) => actions.logout.call({ $store: store })
const user = computed(() => getUser.call({ $store: store }))
const searchQuery = ref('')
const showProfileDropdown = ref(false)

//  Added profile dropdown functions
const handleProfileClick = () => {
  console.log('[v0] Profile clicked')
  showProfileDropdown.value = false
  // Add profile navigation logic here
}

const handleSettingsClick = () => {
  showProfileDropdown.value = false
  // Add settings navigation logic here
}

const handleLogout = () => {
  if (confirm('Apakah Anda yakin ingin logout?')) {
    showProfileDropdown.value = false
    logout()
    router.push('/login');
  }
}

//  Added click outside handler
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showProfileDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
