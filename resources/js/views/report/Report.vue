<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Laporan" description="Laporan"/>
      <div class="p-2">
        <div class="px-1">
          <div class="flex flex-wrap gap-2 mb-1">
            <span
              v-for="q in queries"
              :key="q.label"
              @click="selectQuery(q)"
              class="inline-block cursor-pointer bg-gray-100 px-2 py-1 text-xs rounded-sm border border-dashed border-gray-400 text-gray-500 hover:bg-gray-300"
            >
              {{ q.label }}
            </span>
          </div>
          <ReportDataTable title="Report" :query="selectedQuery"/>
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