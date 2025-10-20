<template>
  <AdminLayout>
    <div class="max-w-full">
      <PageHeader title="Dashboard" description="Selamat datang kembali, apa yang terjadi pada bisnis anda hari ini?"/>
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
import PageHeader from '@/components/PageHeader.vue'

export default {
  name: "Dashboard",
  components: {
    AdminLayout,
    DashboardCards,
    DashboardGrid,
    PageHeader
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
