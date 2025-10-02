<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Laporan" description="Laporan"/>
      <div class="p-2">
        <div class="flex h-screen">
          <!-- Sidebar (20%) -->
          <aside class="w-1/8 border rounded p-2">
            <ul class="space-y-2">
              <li v-for="q in queries">
                <a href="#" @click="selectQuery(q)" class="block px-2 py-1 border border-dashed rounded hover:bg-gray-300 text-sm text-gray-500">
                  {{q.label}}
                </a>
              </li>
            </ul>
          </aside>

          <!-- Konten utama (80%) -->
          <div class="w-7/8 px-1">
            <ReportDataTable title="Report" :query="selectedQuery"/>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
<script>
  import AdminLayout from '@/layouts/AdminLayout.vue'
  import ReportDataTable from '@/components/ReportDataTable.vue'
  import PageHeader from '@/components/PageHeader.vue';
  export default {
    components: {
      AdminLayout,
      ReportDataTable,
      PageHeader
    },
    data() {
        return {
          loading: false,
          queries: [],
          selectedQuery: null
        }
    },
    methods: {
      async load(){
        this.loading = true
        await this.$store
          .dispatch('report/getQueryList', params)
          .then(({ data }) => {
              data = data.data;
              this.queries = data;
          })
          .catch((err) => {
              if (err.response) {
                  if(err.response.status != 200){
                      alert(err.response.data?.message)
                  }
              }
          })
          .finally((f) => {
              this.loading = false
          })
      },
      selectQuery(query){
        this.selectedQuery = query
      }
    },
    mounted(){
      this.load()
    }
  }
</script>