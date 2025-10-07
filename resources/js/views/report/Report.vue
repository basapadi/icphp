<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Laporan" description="Laporan"/>
      <div class="p-2">
        <div class="px-1">
          <div class="flex flex-wrap gap-2 mb-1">
          <template v-for="q in queries" :key="q.label">
            <span @click.prevent="selectQuery(q)" class="inline-flex items-center gap-1 cursor-pointer bg-gray-100 px-2 py-1 text-xs rounded-sm border border-gray-300 text-gray-500 hover:bg-gray-300">
              <component :is="q.icon" class="w-4 h-4 mr-1 text-orange-500" />
              <span>{{ q.label }}</span>
            </span>
          </template>
        </div>
        <template v-if="selectedQuery != null">
          <ReportDataTable title="Report" :query="selectedQuery" @reloadList="reload"/>
        </template>
        <template>
          <Card>

          </Card>
        </template>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
<script>
  import AdminLayout from '@/layouts/AdminLayout.vue'
  import ReportDataTable from '@/components/ReportDataTable.vue'
  import PageHeader from '@/components/PageHeader.vue';
  import { Card } from "@/components/ui/card";
  export default {
    components: {
      AdminLayout,
      ReportDataTable,
      PageHeader,
      Card
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
              this.queries = data.map((item) => {
                item.icon = 'UserCog'
                if(item.path == '/data/queries/reports/default') item.icon = 'MonitorCog'

                return item
              });
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
      },
      reload(v){
        this.selectedQuery = this.queries[0]
        if(v) this.load();
       
      }
    },
    mounted(){
      this.load()
    }
  }
</script>