<template>
  <div class="h-screen bg-gray-50">
    <AdminToolbar @toggle-sidebar="toggleSidebar" />

    <div class="flex h-screen">
      <!-- Sidebar -->
      <aside
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
        :class="[
          'fixed left-0 top-10 bg-white h-[calc(100vh-2.5rem)] z-20 border-r border-gray-200 overflow-y-auto overflow-x-hidden transition-all duration-300 ease-in-out',
          isCollapsed ? 'w-16' : 'w-64'
        ]"
      >
        <nav class="p-2">
          <!-- Search hanya tampil kalau tidak collapse -->
          <div v-if="!isCollapsed" class="pl-2 pt-2 sticky top-0 z-10">
            <div class="relative">
              <Search class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
              <input
                v-model="searchQuery"
                placeholder="Cari..."
                class="pl-7 h-7 text-xs bg-white border border-gray-300 rounded w-full px-2"
              />
            </div>
          </div>

          <!-- Menu -->
          <ul class="space-y-1 mt-4">
            <MenuItem
              v-for="(item, index) in menus"
              :key="index"
              :item="item"
              :level="0"
              :collapsed="isCollapsed"
            />
          </ul>
        </nav>
      </aside>

      <!-- Main -->
      <main
        :class="[
          'flex-1 mt-10 bg-white min-h-[calc(100vh-4rem)] overflow-hidden transition-all duration-300 ease-in-out',
          isCollapsed ? 'ml-16' : 'ml-64'
        ]"
      >
        <div class="h-screen relative">
          <div
            class="absolute inset-0 z-1 bg-[length:15px_15px]
            [background-image:linear-gradient(to_right,rgba(107,114,128,0.04)_1px,transparent_1px),
            linear-gradient(to_bottom,rgba(107,114,128,0.04)_1px,transparent_1px)]
            pointer-events-none"
          ></div>
          <div ref="gridOverlay" class="absolute inset-0 z-1 pointer-events-none"></div>
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminToolbar from '@/components/AdminToolbar.vue'
import { drawBackground } from '@/helpers/datautils.js'
import { Search } from 'lucide-vue-next'

const gridOverlay = ref(null)
const isCollapsed = ref(true)
const searchQuery = ref('')
const manualExpand = ref(false)

onMounted(() => {
  drawBackground(gridOverlay.value)
})

const handleMouseEnter = () => {
  if (isCollapsed.value) {
    isCollapsed.value = false
  }
}

const handleMouseLeave = () => {
  // Hanya collapse lagi kalau sebelumnya memang collapse mode
  if (!manualExpand.value) {
    isCollapsed.value = true
  }
}

const toggleSidebar = () => {
  manualExpand.value = !manualExpand.value
  isCollapsed.value = !isCollapsed.value
}
</script>

<script>
import { mapGetters, mapActions } from 'vuex'
import MenuItem from '@/components/MenuItem.vue'

export default {
  name: 'AdminLayout',
  components: { MenuItem },
  computed: {
    ...mapGetters({
      user: 'auth/getUser',
      menus: 'menu/getMenus',
      app: 'menu/getApp'
    })
  },
  watch: {
    $route(to) {
      this.setActiveMenu(to.path)
    }
  },
  data() {
    return {
      loading: true,
      error: null
    }
  },
  methods: {
    ...mapActions({
      getMenu: 'menu/getMenu',
      setActiveMenu: 'menu/setActiveMenu'
    }),
    async fetchMenus() {
      try {
        await this.getMenu({ route: this.$route.path })
        this.setActiveMenu(this.$route.path)
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal mengambil data'
      } finally {
        this.loading = false
      }
    }
  },
  async created() {
    await this.fetchMenus()
  }
}
</script>
