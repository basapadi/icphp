<template>
    <div class="relative rounded-lg shadow-sm border border-border z-1">
        <div class="h-screen relative">
            <!-- Table Header -->
            <div class="px-1 py-1 h-auto border-b border-border">
                <div class="flex justify-between">
                    <div>
                        <FilterHeader :columns="columns" @load="load" :pagination="pagination" :operators="operators"
                            :filter="filter" :properties="properties" valueType="select" />
                    </div>
                    <div class="">
                        <div class="flex flex-col md:flex-row md:items-center gap-2">
                            <div class="relative" v-if="properties.simpleFilter">
                                <input v-model="searchQuery" type="text" :placeholder="`Cari ${title}`"
                                    class="pl-8 pr-3 py-1.5 italic text-muted-foreground text-sm border-1 border-border rounded-md focus:border-transparent" />
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <Search class="h-4 w-4 text-muted-foreground" />
                                </div>
                            </div>
                            <div class="relative mr-2" v-if="this.module == 'trash'">
                                <Button @click="truncateData" v-if="allowDelete" size="sm">
                                    Hapus Semua
                                </Button>
                            </div>
                            <div class="relative mr-2" v-if="allowCreate && columnOptions.length > 0">
                                <Button v-if="columnOptions.includes('create')" @click="tambahData" size="sm">
                                    Tambah
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto h-15/20">
                <table class="min-w-full relative border border-1 border-dashed border-border">
                    <thead class="bg-accent sticky top-0">
                        <tr>
                            <template v-for="column in columns" :key="column.value">
                                <template v-if="column.name == 'actions'">
                                    <th class="px-4 py-2 text-left font-bold text-xs text-muted-foreground uppercase tracking-wider border border-1 border-dashed border-border"
                                        style="width: 50px">
                                        {{ column.label }}
                                    </th>
                                </template>
                                <template v-if="
                                    [
                                        'view',
                                        'create',
                                        'edit',
                                        'delete',
                                        'download',
                                    ].includes(column.name)
                                ">
                                    <th class="px-4 py-2 text-center font-bold text-xs text-muted-foreground uppercase tracking-wider border border-1 border-dashed border-border"
                                        style="width: 100px">
                                        {{ column.label }}
                                    </th>
                                </template>
                                <template v-else>
                                    <th
                                        class="px-4 py-2 text-left font-bold text-xs text-muted-foreground uppercase tracking-wider border border-1 border-dashed border-border">
                                        {{ column.label }}
                                    </th>
                                </template>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="bg-background/10 divide-y divide-border">
                        <tr v-for="(data, i) in filterData" :key="data.id"
                            class="transition-colors duration-50 border border-1 border-dashed text-sm border-border hover:bg-primary/10 odd:bg-muted/20 even:bg-background/30">
                            <template v-for="column in columns" :key="column.value">
                                <td class="px-4 pt-2 whitespace-nowrap border border-1 border-dashed border-border text-center"
                                    v-if="
                                        [
                                            'view',
                                            'create',
                                            'edit',
                                            'update',
                                            'delete',
                                            'download',
                                        ].includes(column.name)
                                    ">
                                    <template v-if="data.show[column.name]">
                                        <input :checked="data[column.name]" type="checkbox"
                                            class="role-cb h-4 w-4 text-primary" style="align-items: center"
                                            @click="onCheck($event, data, column.name)" />
                                    </template>
                                </td>
                                <td class="px-4 whitespace-nowrap border border-1 border-dashed border-border" v-else>
                                    <span :class="`text-sm items-center text-foreground ${column.class}`">{{
                                        $helpers.getSubObjectValue(
                                            data,
                                            column.name
                                        )
                                    }}</span>
                                </td>
                            </template>
                        </tr>
                    </tbody>
                </table>
            </div>
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
                            <PaginationContent v-slot="items">
                                <PaginationPrevious
                                    class="bg-accent border border-border rounded-md hover:bg-accent/80 text-accent-foreground transition-colors" />
                                <PaginationFirst
                                    class="bg-accent border border-border rounded-md hover:bg-accent/80 text-accent-foreground transition-colors" />
                                <PaginationEllipsis
                                    class="bg-accent border border-border rounded-md hover:bg-accent/80 text-accent-foreground transition-colors"
                                    v-if="pagination._page > 3" />
                                <template v-for="(item, index) in items.items" :key="index">
                                    <PaginationItem class="border border-border rounded-md transition-colors" :class="item.value === pagination._page
                                        ? 'bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground'
                                        : 'bg-background hover:bg-accent text-foreground hover:text-accent-foreground'
                                        " v-if="
                                            item.type === 'page' &&
                                            item.value >=
                                            pagination._page - 1 &&
                                            item.value <= pagination._page + 1
                                        " :value="item.value" :is-active="item.value === pagination._page
                                            " @click="pagination._page = item.value">
                                        {{ item.value }}
                                    </PaginationItem>
                                </template>
                                <PaginationEllipsis
                                    class="bg-accent border border-border rounded-md hover:bg-accent/80 text-accent-foreground transition-colors"
                                    v-if="
                                        pagination._page <
                                        items.items.length - 1
                                    " />
                                <PaginationLast
                                    class="bg-accent border border-border rounded-md hover:bg-accent/80 text-accent-foreground transition-colors" />
                                <PaginationNext
                                    class="bg-accent border border-border rounded-md hover:bg-accent/80 text-accent-foreground transition-colors" />
                            </PaginationContent>
                        </Pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { Search } from "lucide-vue-next";
import FilterHeader from "@/components/FilterHeader.vue";
import * as operator from "./../constants/operator";
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
export default {
    components: { Search, FilterHeader, Pagination, PaginationContent, PaginationEllipsis, PaginationFirst, PaginationItem, PaginationLast, PaginationNext, PaginationPrevious },
    props: {
        title: {
            type: String,
            default: "title",
        },
        module: {
            type: String,
            default: "",
        },
        storeSingleUpdate: {
            type: String,
            default: "",
        },
        defaultFilter: {
            type: Object,
            default: {},
        },
    },
    data() {
        return {
            searchQuery: "",
            currentPage: 1,
            total: 0,
            rows: [],
            columns: [],
            properties: {},
            itemsPerPage: 30,
            operators: operator.Operator,
            filter: {
                operator: "_is",
                column: "menu__module",
                value: "",
            },
            pagination: {
                _limit: 30,
                _page: 1,
            },
        };
    },
    watch: {
        searchQuery: {
            handler() {
                this.filter = {
                    operator: "",
                    column: "",
                    value: "",
                };
                if (
                    this.searchQuery == "" &&
                    this.defaultFilter.column != undefined
                )
                    this.filter = this.defaultFilter;
                this.load();
            },
            immediate: true, // langsung load pertama kali juga
        },
        currentPage() {
            this.load();
        },
        "pagination._page"() {
            this.load();
        },
    },
    computed: {
        filterData() {
            if (this.searchQuery) {
                this.pagination._page = 1;
            }
            return this.rows;
        },
        totalPages() {
            return Math.ceil(this.total / this.pagination._limit);
        },
        startIndex() {
            return this.pagination._page * this.pagination._limit + 1;
        },
        endIndex() {
            return Math.min(
                this.pagination._page * this.pagination._limit,
                this.filterData.length
            );
        },
    },
    methods: {
        async load(reset) {
            let params = { ...this.pagination };
            if (reset != undefined && reset == true) {
                this.filter = {
                    column: "",
                    operator: "",
                    value: "",
                };
            } else {
                let filter_value = this.filter.value;
                if (this.filter.operator != undefined) {
                    if (
                        this.filter.value == "_notnull" ||
                        this.filter.value == "_null"
                    ) {
                        filter_value = null;
                    }
                }

                if (this.filter.operator == "_between") {
                    filter_value = `${this.filter.value_from},${this.filter.value_to}`;
                }

                if (this.filter.column != undefined) {
                    params[`${this.filter.column}${this.filter.operator}`] =
                        filter_value;
                }
                // params.q = this.searchQuery;
            }
            await this.$store
                .dispatch(this.module + "/grid", params)
                .then(({ data }) => {
                    data = data.data;
                    this.rows = data.rows;
                    this.columns = data.columns;
                    this.total = data.total;
                    this.properties = data.properties;
                });
        },
        async onCheck(event, data, column) {
            await this.$store
                .dispatch(this.module + "/singleUpdate", {
                    value: event.target.checked,
                    id: data.encode_id,
                    column,
                })
                .then(() => {
                    // this.load();
                });
        },
        previousPage() {
            if (this.currentPage > 1) this.currentPage--;
        },
        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },
        initPage() {
            this.currentPage = 1;
        },
    },
};
</script>
