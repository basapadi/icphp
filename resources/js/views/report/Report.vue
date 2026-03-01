<template>
    <AdminLayout>
        <div class="w-full max-h-1">
            <PageHeader title="Laporan" description="Laporan" />
            <div class="relative p-1 z-10">
                <div class="p-1 grid grid-cols-12 gap-4">

                    <!-- SIDEBAR KIRI -->
                    <div class="col-span-12 md:col-span-2 h-[calc(100vh-100px)] flex flex-col">
                        <div class="py-2 border-b border-border">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari Laporan..."
                                class="w-full px-2 py-2 text-xs rounded-md border border-border
                                    bg-background focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                        <div class="flex-1 overflow-y-auto py-2">
                            <template v-for="q in filteredQueries" :key="q.label">
                                <div class="relative group">
                                    <button
                                        @click.prevent="selectQuery(q)"
                                        style="cursor: pointer;"
                                        class="w-full text-left text-xs border mb-1 rounded-md
                                            transition-colors"
                                        :class="selectedQuery?.label === q.label 
                                                ? 'bg-orange-icphp text-gray-500 border-none rounded-sm shadow-sm' 
                                                : 'text-muted-foreground'">
                                        <div class="flex items-center">
                                            <div class="px-2 py-1">
                                                <component :is="q.icon" class="w-4 h-4 text-primary"/>
                                            </div>
                                            <span class="ml-2 text-xs">{{ q.label }}</span>
                                        </div>
                                    </button>

                                    <!-- Tooltip -->
                                    <div
                                        class="fixed
                                            w-40 p-2 text-xs rounded-md border shadow-lg
                                            bg-secondary text-secondary-foreground
                                            opacity-0 invisible
                                            group-hover:opacity-100 group-hover:visible
                                            transition-all duration-150 z-100"
                                        :style="{
                                            top: $event?.target?.getBoundingClientRect().top + 'px',
                                            left: $event?.target?.getBoundingClientRect().right + 10 + 'px'
                                        }"
                                    >
                                        {{ q.description }}
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- CONTENT KANAN -->
                    <div class="col-span-12 md:col-span-10">
                        <template v-if="selectedQuery">
                            <ReportDataTable 
                                title="Report" 
                                :query="selectedQuery" 
                                @reloadList="reload" 
                            />
                        </template>
                        <template v-else>
                            <Card>
                                <CardHeader>
                                    <CardTitle>
                                        <p class="text-center text-muted-foreground">Silakan pilih menu laporan disebelah kiri untuk membuka laporan</p>
                                    </CardTitle>
                                </CardHeader>
                            </Card>
                        </template>
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>
<script>
import AdminLayout from "@/layouts/AdminLayout.vue";
import ReportDataTable from "@/components/ReportDataTable.vue";
import PageHeader from "@/components/PageHeader.vue";
import { Card } from "@/components/ui/card";
export default {
    components: {
        AdminLayout,
        ReportDataTable,
        PageHeader,
        Card,
    },
    data() {
        return {
            loading: false,
            queries: [],
            selectedQuery: null,
            search: ''
        };
    },
    computed: {
        filteredQueries() {
            if (!this.search) return this.queries

            return this.queries.filter(q =>
                q.label.toLowerCase().includes(this.search.toLowerCase())
            )
        }
    },
    methods: {
        async load() {
            this.loading = true;
            await this.$store
                .dispatch("report/getQueryList", params)
                .then(({ data }) => {
                    data = data.data;
                    this.queries = data.map((item) => {
                        item.icon = "UserCog";
                        if (item.path == "/data/queries/reports/default")
                            item.icon = "MonitorCog";

                        return item;
                    });
                })
                .catch((err) => {
                    if (err.response) {
                        if (err.response.status != 200) {
                            alert(err.response.data?.message);
                        }
                    }
                })
                .finally((f) => {
                    this.loading = false;
                });
        },
        selectQuery(query) {
            this.selectedQuery = query;
        },
        reload(v) {
            this.selectedQuery = this.queries[0];
            if (v) this.load();
        },
    },
    mounted() {
        this.load();
    },
};
</script>
