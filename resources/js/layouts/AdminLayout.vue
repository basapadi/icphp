<template>
  <div class="h-screen bg-gray-50">
    <AdminToolbar />
    <div class="flex h-screen">
      <!-- Sidebar -->
      <aside
        :class="[
          'fixed left-0 top-10 bg-white h-[calc(100vh-2.5rem)] z-20 border-r border-gray-200 overflow-y-auto overflow-x-hidden transition-all duration-300 ease-in-out scroll-container',
          isCollapsed ? 'w-16' : 'w-64'
        ]"
      >
        <!-- Tombol Collapse -->
        <button
          @click="toggleSidebar"
          class="fixed top-15 shadow-lg absolute -right-3 left-[15rem] top-1/2 transform -translate-y-1/2 bg-white border border-orange-400 rounded-full w-7 h-7 flex items-center justify-center hover:bg-gray-100 transition-all duration-200 z-30"
          :style="{ left: isCollapsed ? '3rem' : '15rem' }"
        >
          <ChevronLeft v-if="!isCollapsed" class="w-3 h-3 text-orange-600" />
          <ChevronRight v-else class="w-3 h-3 text-orange-600" />
        </button>

        <nav class="p-2">
          <div v-if="!isCollapsed" class="pl-2 mt-8 pr-2 sticky top-0 z-10">
            <div class="relative">
              <Search
                class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4"
              />
              <input
                v-model="searchQuery"
                placeholder="Cari menu..."
                class="pl-7 h-7 text-xs text-gray-500 bg-white border border-gray-300 rounded w-full px-2"
              />
            </div>
          </div>

          <!-- Menu -->
          <ul class="space-y-1 mt-4">
            <MenuItem
              v-for="(item, index) in filteredMenus"
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
            [background-image:linear-gradient(to_right,rgba(242,242,242,0.3)_1px,transparent_1px),linear-gradient(to_bottom,rgba(242,242,242,0.3)_1px,transparent_1px)]
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
import { Search, ChevronLeft, ChevronRight } from 'lucide-vue-next'

const gridOverlay = ref(null)
const isCollapsed = ref(false)

onMounted(() => {
  drawBackground(gridOverlay.value)
})

const toggleSidebar = () => {
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
    }),
    filteredMenus() {
      if (!this.searchQuery) return this.menus

      const q = this.searchQuery.toLowerCase()

      const filterRecursive = (items) => {
        return items
          .map((item) => {
            const matchCurrent = item.label?.toLowerCase().includes(q)

            const filteredChildren = item.sub_items
              ? filterRecursive(item.sub_items)
              : []

            if (matchCurrent || filteredChildren.length > 0) {
              return {
                ...item,
                sub_items: filteredChildren
              }
            }
            return null
          })
          .filter(Boolean)
      }

      return filterRecursive(this.menus)
    },

  },
  watch: {
    $route(to) {
      this.setActiveMenu(to.path)
    }
  },
  data() {
    return {
      loading: true,
      error: null,
      searchQuery: ''
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
