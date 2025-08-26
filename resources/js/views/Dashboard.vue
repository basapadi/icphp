<template>
  <AdminLayout>
    <div class="max-w-full">
      <div class="bg-gray-50 border-b border-gray-200 p-2">
        <h1 class="text-lg font-bold text-gray-900 mb-0">Dashboard</h1>
        <p class="text-xs text-gray-600">Selamat datang kembali, apa yang terjadi pada bisnis anda hari ini?</p>
      </div>

      <!-- Dashboard Content -->
      <div class="p-2">
        <DashboardCards :stats="data.cards"/>
        <DashboardGrid :datas="data.grids"/>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/layouts/AdminLayout.vue'
import DashboardCards from '@/components/DashboardCards.vue'
import DashboardGrid from '@/components/DashboardGrid.vue'

export default {
  name: "Dashboard",
  components: {
    AdminLayout,
    DashboardCards,
    DashboardGrid
  },
  data() {
    return {
      data: {},
    };
  },
  methods: {
    async load() {
      await this.$store.dispatch('dashboard/data').then(({ data }) => {
        this.data = data.data;
      })
    }
  },
  beforeMount() {
    this.load()
  }
}
</script>
