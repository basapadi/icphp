<template>
    <div class="bg-white rounded-lg shadow-md border border-gray-200 pb-18">
        <!-- Table Header -->
        <div class="px-1 py-1 border-b border-gray-200">
            <div class="flex justify-between">
                <div>
                    <FilterHeader :columns="columns" @load="load" :pagination="pagination" :operators="operators" :filter="filter" :properties="properties"/>
                </div>
                <div class="">
                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                        <div class="relative">
                            <input v-model="searchQuery" type="text" :placeholder="`Cari Data ${title}`"
                                class="pl-8 pr-3 py-1.5 text-sm border-1 border-gray-300 rounded-md focus:border-transparent" />
                            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                <Search class="h-4 w-4 text-gray-400" />
                            </div>
                        </div>
                        <div class="relative mr-2">
                            <Button @click="tambahData" v-if="allowCreate" size="sm">
                                Tambah
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-screen relative">
            <!-- Table -->
            <div class="overflow-x-auto max-h-3/4">
                <table class="min-w-full border-collapse border border-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <template v-if="properties.multipleSelect">
                                <th
                                    class="px-4 py-1 text-left text-xs text-gray-500 uppercase tracking-wider border-2 border-gray-200">
                                    <input v-model="selectAll" @change="toggleSelectAll" type="checkbox"
                                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500" />
                                </th>
                            </template>
                            <template v-for="column in columns" :key="column.value">
                                <template v-if="column.show">
                                    <template v-if="column.name == 'actions'">
                                        <th class="px-4 py-2 text-left font-bold text-xs text-gray-500 uppercase tracking-wider border-2 border-gray-200"
                                            style="width: 50px !important">
                                            {{ column.label }}
                                        </th>
                                    </template>
                                    <template v-else>
                                        <th :class="`px-4 py-2 text-left font-bold text-xs text-gray-500 uppercase tracking-wider border-2 border-gray-200 text-${column.align}`"
                                            :style="`${column.styles}`">
                                            {{ column.label }}
                                        </th>
                                    </template>
                                </template>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(data,index) in filterData" :key="data.id" class="hover:bg-gray-50 transition-colors">
                            <template v-if="properties.multipleSelect">
                                <td class="px-4 whitespace-nowrap border-2 border-gray-200" style="width: 10px">
                                    <input v-model="selectedData" :value="data.encode_id" type="checkbox"
                                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500" />
                                </td>
                            </template>
                            <template v-for="column in columns" :key="column.value">
                                <td v-if="column.show" :class="`px-4 py-1 whitespace-nowrap border-2 border-gray-200 relative text-${column.align}`">
                                    <template v-if="column.name == 'actions'">
                                        <button @click.stop="toggleDropdown(index)" class="px-2 py-1 rounded hover:bg-gray-300" style="text-align:center;"><EllipsisVertical class="h-4 w-4"/></button>
                                        <div v-if="openDropdown === index" class="absolute right--2 mt-2 w-40 bg-white border rounded shadow-md z-10">
                                            <a v-if="column.options.includes('detail')" href="#" @click.stop="viewData(data)" class="flex items-center px-4 py-1 hover:bg-gray-100"><EyeIcon class="h-4 text-green-700 px-2" />Detail</a>
                                            <a v-if="column.options.includes('edit')" href="#" @click.stop="editData(data)" class="flex items-center px-4 py-1 hover:bg-gray-100"><PencilSquareIcon class="h-4 text-orange-500 px-2" />Ubah</a>
                                            <div class="border-t border-gray-200 my-1"></div>
                                            <a v-if="column.options.includes('delete')" href="#" @click.stop="hapusData(data.id)" class="flex items-center px-4 py-1 hover:bg-gray-100"><TrashIcon class="h-4 text-red-500 px-2" />Hapus</a>
                                        </div>
                                    </template>
                                    <template v-else-if="column.type === 'badge'">
                                        <div :class="`inline-flex items-center rounded-md bg-${data[`color_${column.name}`]
                                            }/50 px-2 py-1 text-xs font-medium text-${data[`color_${column.name}`]
                                            } inset-ring inset-ring-${data[`color_${column.name}`]
                                            }/50`">
                                            {{
                                                $helpers.getSubObjectValue(
                                                    data,
                                                    column.name
                                                )
                                            }}
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span :class="`text-sm text-gray-600 ${column.class}`"
                                            :style="`${column.styles}`">{{
                                                $helpers.getSubObjectValue(
                                                    data,
                                                    column.name
                                                )
                                            }}</span>
                                    </template>
                                </td>
                            </template>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Data
                        <span class="font-medium">
                            {{ pagination._page * pagination._limit - (pagination._limit - 1) }}
                        </span>
                        hingga
                        <span class="font-medium">
                            {{ (pagination._page - 1) * pagination._limit + rows?.length }}
                        </span>
                        dari <span class="font-medium">{{ total }}</span> hasil
                    </div>
                    <div>
                        <Pagination v-slot="{ page }" :total="total" :items-per-page="pagination._limit"
                            :page="pagination._page" @update:page="(val) => (pagination._page = val)">
                            <PaginationContent v-slot="items">
                                <PaginationPrevious />
                                <PaginationFirst />
                                <PaginationEllipsis v-if="pagination._page > 2"/>
                                <template v-for="(item, index) in items.items" :key="index">
                                    <PaginationItem v-if="item.type === 'page' && item.value >= pagination._page-1 && item.value <= pagination._page + 1 "
                                        :value="item.value" :is-active="item.value === pagination._page"
                                        @click="pagination._page = item.value">
                                        {{ item.value }}
                                    </PaginationItem>
                                </template>
                                <PaginationEllipsis v-if="pagination._page < items.items.length -1"/>
                                <PaginationLast />
                                <PaginationNext />
                            </PaginationContent>
                        </Pagination>
                    </div>
                </div>
            </div>
            <div v-if="loading" class="absolute inset-0 flex pt-80 justify-center bg-white/70 z-10" >
                <LoaderCircle class="w-14 h-14 animate-spin text-orange-500" />
            </div>
        </div>
    </div>
    <FormDialog :open="showDialog" :title="title" :fields="form.fields" :data="form.data" @close="showDialog = false"
        @submit="handleSubmit" />
</template>

<script>
import * as operator from "./../constants/operator";
import { Search,LoaderCircle, EllipsisVertical } from "lucide-vue-next"
import { mapGetters } from "vuex";
import {
    TrashIcon,
    PencilSquareIcon,
    EyeIcon
} from "@heroicons/vue/24/outline";
import { Badge } from "@/components/ui";
import FormDialog from "@/components/FormDialog.vue";
import FilterHeader from "@/components/FilterHeader.vue";
import Button from "@/components/ui/button/Button.vue";
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
    components: {
        Search,
        TrashIcon,
        PencilSquareIcon,
        Badge,
        FormDialog,
        EyeIcon,
        FilterHeader,
        Button,
        Pagination,
        PaginationContent,
        PaginationEllipsis,
        PaginationFirst,
        PaginationItem,
        PaginationLast,
        PaginationNext,
        PaginationPrevious,
        LoaderCircle,
        EllipsisVertical
    },
    props: {
        title: {
            type: String,
            default: "title",
        },
        store_grid: {
            type: String,
            default: "",
        },
        store_form: {
            type: String,
            default: "",
        },
    },
    data() {
        return {
            searchQuery: '',
            selectAll: false,
            selectedData: [],
            total: 0,
            rows: [],
            form: [],
            columns: [],
            properties: {},
            allowCreate: false,
            showDialog: false,
            pagination: {
                _limit: 25,
                _page: 1,
            },
            filter: {
                operator: '',
                column: '',
                value: '',
            },
            operators: operator.Operator,
            loading: false,
            openDropdown: false
        };
    },
    watch: {
        searchQuery: {
            handler() {
                this.load();
            },
            //immediate: true // langsung load pertama kali juga
        },
        currentPage() {
            this.load();
        },
        "pagination._page"() {
            this.load();
        },
    },
    computed: {
        ...mapGetters({
            menuRoles: "menu/getMenuRoles",
        }),
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
            this.loading = true
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

                if (this.filter.operator == '_between') {
                    filter_value = `${this.filter.value_from},${this.filter.value_to}`
                }

                if (this.filter.column != undefined) {
                    params[`${this.filter.column}${this.filter.operator}`] =
                        filter_value;
                }
                params.q = this.searchQuery;
            }

            await this.$store
                .dispatch(this.store_grid, params)
                .then(({ data }) => {
                    data = data.data;
                    this.rows = data.rows;
                    this.columns = data.columns;
                    this.total = data.total;
                    this.properties = data.properties;
                }).finally((f) => {
                    this.loading = false
                })
            this.allowCreate = this.menuRoles.find(
                (role) => role.route === this.$route.path
            )?.create;
        },
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        },
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedData = this.filterData.map((dt) => dt.encode_id);
            } else {
                this.selectedData = [];
            }
        },
        async tambahData() {
            await this.$store.dispatch(this.store_form).then(({ data }) => {
                this.form = data.data;
            });
            this.showDialog = true;
        },
        editData(user) {
            alert("Action Edit disini:", user);
        },
        hapusData(userId) {
            alert("Action Hapus disini:", userId);
        },
        viewData(data) {
            alert("Action view detail data:");
        },
        handleSubmit() { },
        toggleDropdown(index) {
            this.openDropdown = this.openDropdown === index ? null : index
        },
        handleClickOutside(e) {
            // cek apakah klik berada di dalam dropdown
            if (!this.$el.contains(e.target)) this.openDropdown = null
        }
    },
    mounted() {
        this.load();
        document.addEventListener("click", this.handleClickOutside)
    },
    beforeUnmount() {
        document.removeEventListener("click", this.handleClickOutside)
    }
};
</script>
