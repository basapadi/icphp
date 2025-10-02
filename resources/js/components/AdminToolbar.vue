<template>
  <div class="bg-gray-50 border-b border-gray-300 fixed left-0 right-0 pt-1 z-40 h-10">
    <div class="flex items-center justify-between px-2 h-full">
      <!-- Menu Bar -->
      <div class="flex items-center">
        <button class="text-xs text-gray-500 hover:bg-gray-200 px-3 py-1">Panduan</button>
        <button class="text-xs text-gray-500 hover:bg-gray-200 px-3 py-1" @click="showChangeLog = true">Change Log</button>
        <div class="watermark z-5 font-extrabold">
          <span class="demo-2"></span>
        </div>
      </div>

      <!-- Toolbar Actions -->
      <div class="flex items-center space-x-2">
        <div class="w-px h-6 bg-gray-300"></div>
        <button class="h-7 w-7 p-0 relative hover:bg-gray-200 flex items-center justify-center">
          <Bell class="w-3 h-3" />
          <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>

        <!-- Profile dropdown -->
        <div class="relative">
          <button
            @click="showProfileDropdown = !showProfileDropdown"
            class="h-7 w-7 p-0 hover:bg-gray-200 flex items-center justify-center rounded"
          >
            <User class="w-3 h-3" />
          </button>

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

        <!-- ChangeLog Dialog -->
        <div v-if="showChangeLog"  class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click="showChangeLog=false" >
          <Card class="w-full max-w-2xl bg-white shadow-lg rounded-lg overflow-hidden" @click.stop>
            <CardContent class="p-6" >
              <ChangeLog/>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>

    <div
      v-if="showProfileDropdown"
      @click="showProfileDropdown = false"
      class="fixed inset-0 z-40"
    ></div>
  </div>
</template>

<script>
import { Bell, User, LogOut } from 'lucide-vue-next'
import { mapGetters, mapActions } from 'vuex'
import ChangeLog from '@/components/ChangeLog.vue'
import { Card, CardContent } from "@/components/ui/card";

export default {
  name: 'TopBar',
  components: { Bell, User, LogOut,ChangeLog,Card,CardContent },

  data() {
    return {
      showProfileDropdown: false,
      searchQuery: '',
      showChangeLog: false
    }
  },

  computed: {
    ...mapGetters('auth', ['getUser']),
    user() {
      return this.getUser
    }
  },

  methods: {
    ...mapActions('auth', ['logout']),

    handleProfileClick() {
      console.log('[v0] Profile clicked')
      this.showProfileDropdown = false
      // Tambahkan navigasi profil jika diperlukan
    },

    handleLogout() {
      if (confirm('Apakah Anda yakin ingin logout?')) {
        this.showProfileDropdown = false
        this.logout().then(() => this.$router.push('/login'))
      }
    },

    handleClickOutside(event) {
      if (!event.target.closest('.relative')) {
        this.showProfileDropdown = false
      }
    }
  },

  mounted() {
    document.addEventListener('click', this.handleClickOutside)
  },

  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
  }
}
</script>
