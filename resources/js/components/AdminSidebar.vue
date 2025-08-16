<template>
  <aside :class="[
    'fixed left-0 top-10 h-[calc(100vh-4rem)] bg-white border-r border-gray-200 overflow-y-auto transition-all duration-300 ease-in-out w-64'
  ]">

    <nav class="p-1">
      <ul class="space-y-1">
        <MenuItem 
          v-for="(item, index) in menus" 
          :key="index" 
          :item="item" 
          :level="0"
        />
      </ul>
    </nav>

    <!-- Modern status indicator at bottom -->
    <!-- <div class="absolute bottom-0 left-0 right-0 bg-gray-50 border-t border-gray-200 p-4">
      <div class="flex items-center space-x-3">
        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
        <span v-show="!isCollapsed" class="text-sm text-gray-600">System Online</span>
      </div>
    </div> -->
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import { useStore, mapGetters, mapActions } from 'vuex'
import MenuItem from './MenuItem.vue'
import {
  BarChart3,
  Users,
  ShoppingCart,
  Package,
  Settings,
  FileText,
  Home,
  TrendingUp,
  Calendar,
  CreditCard,
} from 'lucide-vue-next'

</script>
<script>
  export default {
    name: 'AdminSidebar',
    computed: {
      ...mapGetters({
        user: 'auth/getUser'
      })
    },
    data() {
      return {
        ICONS: {
          home: Home,
          creditcard: CreditCard,
          settings: Settings
        },
        menus: [],
        loading: true,
        error: null
      };
    },
  methods: {
      ...mapActions({
        getMenu : 'menu/getMenu'
      }),
      async getMenus() {
        try {
          const resp = await this.getMenu({
              route: this.$route.path,
              role: this.user.role
            });
          this.menus = this.mapIcons(resp.data.menus);
        } catch (err) {
          this.error = err.response?.data?.message || 'Gagal mengambil data';
        } finally {
          this.loading = false;
        }
      },
      mapIcons(menuList) {
        return menuList.map(menu => ({
          ...menu,
          icon: this.ICONS[menu.icon?.toLowerCase()] || null,
          childs: Array.isArray(menu.childs) ? this.mapIcons(menu.childs) : []
        }));
      }
  },
  beforeMount() {
    this.getMenus();
  },
  }
</script>
