<template>
    <div class="rounded-xl border bg-card">
        <div class="px-1 py-1 h-auto border-b border-border">
                <div class="flex justify-between">
                    <div class="flex gap-2">
                        <FilterHeader :columns="columns" @load="load" :pagination="pagination" :operators="operators" :filter="filter" :properties="properties"/>
                    </div>
                </div>
            </div>
        <div class="relative w-full overflow-auto">
            <table class="w-full caption-bottom text-sm">
                <thead class="[&_tr]:border-b">
                    <tr class="border-b">
                        <template v-for="col in columns" :key="col.value">
                            <template v-if="col.show">
                                <th :class="`px-4 py-2 text-left font-bold text-xs text-muted-foreground uppercase tracking-wider border border-1 border-dashed border-border text-${col.align}`" :style="`${col.styles}`">
                                    {{ col.label }}
                                </th>
                            </template>
                        </template>
                        <th class="h-10 px-4 text-left font-medium text-muted-foreground" >
                            Pilih
                        </th>
                    </tr>
                </thead>

                <tbody class="[&_tr:last-child]:border-0">
                    <tr v-if="loading">
                        <td
                            :colspan="columns.length + 1"
                            class="h-16 text-center"
                        >
                            Memuat…
                        </td>
                    </tr>

                    <tr v-else-if="!rows.length">
                        <td :colspan="columns.length + 1" class="h-16 text-center">
                            {{ emptyText }}
                        </td>
                    </tr>

                    <tr v-else v-for="(row, idx) in rows" :key="row.id ?? idx" class="border-b hover:bg-muted/50">
                        <template v-for="col in columns" :key="col.value">
                            <td v-if="col.show" class="p-4 align-middle">
                                <!-- 1) SLOT PER KOLOM (paling fleksibel) -->
                                <slot
                                    :name="`cell-${col.name}`"
                                    :value="row[col.name]"
                                    :row="row"
                                    :index="idx"
                                >
                                    <span>
                                        {{ $helpers.getSubObjectValue(row, col.name) }}
                                    </span>
                            </slot> 
                        </td>
                    </template>
                    <td class="p-4 text-right">
                        <input v-model="selected" :value="row[openFilter.value]" type="checkbox"
                            class="rounded border-border text-primary focus:ring-primary" style="transform: scale(1.3); cursor: pointer;" />
                    </td>
                    </tr>
                </tbody>
            </table>
           <div class="px-4 py-3 h-auto border-t border-border z-1">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-muted-foreground">
                        Data
                        <span class="font-medium">
                            {{
                                pagination._page * pagination._limit -
                                (pagination._limit - 1)
                            }}
                        </span>
                        hingga
                        <span class="font-medium">
                            {{
                                (pagination._page - 1) * pagination._limit +
                                rows?.length
                            }}
                        </span>
                        dari <span class="font-medium">{{ total }}</span> hasil
                    </div>
                    <div>
                        <Pagination :total="total" :items-per-page="pagination._limit" :page="pagination._page"
                            @update:page="(val) => (pagination._page = val)">
                            <PaginationContent v-slot="{ items }">
                                <PaginationPrevious />

                                <PaginationFirst />

                                <PaginationEllipsis
                                    v-if="pagination._page > 2"
                                />

                                <template v-for="(item, index) in items" :key="index">
                                    <PaginationItem
                                        v-if="
                                            item.type === 'page' &&
                                            item.value >= pagination._page - 1 &&
                                            item.value <= pagination._page + 1
                                        "
                                        :value="item.value"
                                        :is-active="item.value === pagination._page"
                                        @click="pagination._page = item.value"
                                    >
                                        {{ item.value }}
                                    </PaginationItem>
                                </template>

                                <PaginationEllipsis
                                    v-if="pagination._page < items.length - 1"
                                />

                                <PaginationLast />

                                <PaginationNext />
                            </PaginationContent>

                        </Pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script >
import * as operator from "@/constants/operator";
import FilterHeader from "@/components/FilterHeader.vue";
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
    PaginationNext,
    PaginationPrevious,
} from "@/components/ui/pagination";

const DEFAULT_FILTER = {
    operator: "",
    column: "",
    value: ""
};
export default {
    name: "SelectFilter",
    components: {
        FilterHeader,
        Pagination,
        PaginationContent,
        PaginationEllipsis,
        PaginationFirst,
        PaginationItem,
        PaginationLast,
        PaginationNext,
        PaginationPrevious,
    },
    props: {
        openFilter: {
            type: Object,
            default: () => ({})
        }
    },
    computed: {
        totalPage() {
            return Math.ceil(this.allRows.length / this.pagination._limit);
        }
    },
    watch: {
        selected: {
            deep: true,
            handler() {
                this.$emit("selected", this.selected);
            }
        },
        "pagination._page"() {
            this.applyPagination();
        },
        // filter: {
        //     deep: true,
        //     handler() {
        //         this.applyFilter();
        //     }
        // }
    },
    data() {
        return {
            loading: false,
            open: false,
            columns: [],
            allRows: [],
            filteredRows: [],
            rows: [],
            total: 0,
            emptyText: "Data tidak tersedia",
            selected: [],
            operators: operator.Operator,
            filter: { ...DEFAULT_FILTER },
            pagination: {
                _limit: 10,
                _page: 1,
            },
            properties: {
                advanceFilter: true
            }
        };
    },
    mounted() {
        this.load();
    },
    methods: {
        close() {
            this.open = false;
        },
        load(reset = false) {
            this.loading = true;

            // 🔹 RESET FILTER
            if (reset === true) {
                this.filter = { ...DEFAULT_FILTER };
                this.filteredRows = [...this.allRows];
                this.total = this.filteredRows.length;
                this.pagination._page = 1;
                this.applyPagination();
                this.loading = false;
                return;
            }

            // 🔹 LOAD / FILTER DATA
            // Jika allRows sudah ada, pakai client-side filter
            if (this.allRows.length) {
                this.applyFilter();
                this.loading = false;
                return;
            }

            // 🔹 FIRST TIME LOAD DATA DARI SERVER
            axios.get(this.openFilter.url)
                .then(({ data }) => {
                    this.columns = data.data.columns;
                    this.allRows = data.data.rows;

                    // initial state
                    this.filteredRows = [...this.allRows];
                    this.total = this.filteredRows.length;

                    this.applyPagination();
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        applyPagination() {
            const start =
                (this.pagination._page - 1) * this.pagination._limit;
            const end = start + this.pagination._limit;

            this.rows = this.filteredRows.slice(start, end);
        },
        normalize(val) {
            if (val === null || val === undefined) return null;
            return String(val).toLowerCase().trim();
        },
        applyFilter() {
            const { operator, column, value } = this.filter;

            if (!operator || !column) {
                // tanpa filter → tampilkan semua
                this.filteredRows = [...this.allRows];
            } else {
                const filterVal = String(value).toLowerCase().trim();

                this.filteredRows = this.allRows.filter(row => {
                    const cellValRaw = row[column];
                    if (cellValRaw === undefined || cellValRaw === null) return false;

                    const cellVal = String(cellValRaw).toLowerCase().trim();

                    switch (operator) {
                        case "_is": return cellVal === filterVal;
                        case "_ne": return cellVal !== filterVal;
                        case "_contain": return cellVal.includes(filterVal);
                        case "_in": return filterVal.split(",").map(v => v.trim()).includes(cellVal);
                        case "_notin": return !filterVal.split(",").map(v => v.trim()).includes(cellVal);
                        case "_null": return cellValRaw === null || cellValRaw === "";
                        case "_notnull": return cellValRaw !== null && cellValRaw !== "";
                        default: return true;
                    }
                });
            }

            this.total = this.filteredRows.length;
            this.pagination._page = 1;
            this.applyPagination();
        }




    }
}
</script>
