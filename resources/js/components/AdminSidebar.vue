<template>
  <aside :class="[
    'fixed left-0 top-10 h-[calc(100vh-4rem)] bg-white border-r border-gray-200 overflow-y-auto overflow-x-hidden transition-all duration-300 ease-in-out w-64'
  ]">
    <nav class="p-1">
      <div class="pl-2 pt-2 sticky top-0 bg-white z-10">
        <Search class="absolute left-3 top-5/8 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
        <input
          v-model="searchQuery"
          placeholder="Cari..."
          class="pl-7 h-7 text-xs bg-white border border-gray-300 rounded w-58 px-2"
        />
      </div>
      <ul class="space-y-1">
        <MenuItem 
          v-for="(item, index) in menus" 
          :key="index" 
          :item="item" 
          :level="0"
        />
      </ul>
    </nav>
    <div class="sticky bottom-0 border-t bg-white border-gray-200 p-2 text-center">
      <label class="text-xs italic antialiased text-gray-500  px-3 py-1">© {{app.copyright}} v{{app.version}}</label>
    </div>
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
  DollarSign,
  ScrollText,
  PackageOpen,
  Trash,
  Search
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
          settings: Settings,
          shoppingcart: ShoppingCart,
          package: Package,
          dollarsign: DollarSign,
          scrolltext: ScrollText,
          packageopen: PackageOpen,
          trash: Trash
        },
        menus: [],
        loading: true,
        error: null,
        app: {},
        searchQuery: ''
      };
    },
  methods: {
      ...mapActions({
        getMenu : 'menu/getMenu'
      }),
      async getMenus() {
        try {
          const resp = await this.getMenu({
              route: this.$route.path
              // role: this.user.role
            });
          this.menus = this.mapIcons(resp.data.menus);
          this.app = resp.data.app
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
