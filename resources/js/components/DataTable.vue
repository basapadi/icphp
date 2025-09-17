<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 z-1">
        <!-- Table Header -->
        <div class="px-1 py-1 border-b border-gray-300">
            <div class="flex justify-between">
                <div>
                    <FilterHeader :columns="columns" @load="load" :pagination="pagination" :operators="operators" :filter="filter" :properties="properties"/>
                </div>
                <div class="">
                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                        <div class="relative delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103" v-if="properties.simpleFilter">
                            <input v-model="searchQuery" type="text" :placeholder="`Cari ${title}`"
                                class="pl-8 pr-3 py-1.5 text-sm border-1 border-gray-300 rounded-md focus:border-transparent" />
                            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                <Search class="h-4 w-4 text-gray-400" />
                            </div>
                        </div>
                        <div class="relative mr-2" v-if="this.module == 'trash'">
                            <Button class="bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 transition-colors delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103" @click="truncateData" v-if="allowDelete" size="sm">
                                Hapus Semua
                            </Button>
                        </div>
                        <div class="relative mr-2" v-if="allowCreate && columnOptions.length > 0 ">
                            <Button v-if="columnOptions.includes('create')" class="bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 transition-colors delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103" @click="tambahData" size="sm">
                                Tambah
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-screen relative z-1">
            <!-- Table -->
            <div class="overflow-x-auto max-h-7/10 z-1" ref="tableContainer">
                <table class="min-w-full border-collapse border border-orange-100 z-1">
                    <thead class="bg-orange-50 sticky top-0" style="z-index:11">
                        <tr>
                            <template v-if="properties.multipleSelect">
                                <th 
                                    class="px-4 py-1 text-left text-xs text-shadow-2xs text-gray-500 uppercase tracking-wider border-2 border-gray-100">
                                    <input v-model="selectAll" @change="toggleSelectAll" type="checkbox"
                                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500" />
                                </th>
                            </template>
                            <template v-for="column in columns" :key="column.value">
                                <template v-if="column.show">
                                    <template v-if="column.name == 'actions'">
                                        <th class="px-4 py-2 text-left font-bold text-xs text-gray-500 text-shadow-2xs uppercase tracking-wider border-2 border-gray-100"
                                            style="width: 50px !important">
                                            {{ column.label }}
                                        </th>
                                    </template>
                                    <template v-else>
                                        <th :class="`px-4 py-2 text-left font-bold text-xs text-gray-500 text-shadow-2xs uppercase tracking-wider border-2 border-gray-100 text-${column.align}`"
                                            :style="`${column.styles}`">
                                            {{ column.label }}
                                        </th>
                                    </template>
                                </template>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(data,index) in filterData" :key="data.id" :class="['transition-colors duration-100',selectedIndex === index ? 'bg-orange-200' : 'hover:bg-orange-100']" @dblclick="viewData(data)" @click="handleClickRow(data,index,$event)" @contextmenu.prevent="handleRightClick($event)">
                            <template v-if="properties.multipleSelect">
                                <td class="px-4 whitespace-nowrap border-2 border-gray-200" style="width: 10px">
                                    <input v-model="selectedData" :value="data.encode_id" type="checkbox"
                                        class="rounded border-gray-100 text-orange-600 focus:ring-orange-500" />
                                </td>
                            </template>
                            <template v-for="column in columns" :key="column.value">
                                <td v-if="column.show" :class="`relative px-4 py-1 whitespace-nowrap border-2 border-gray-100 text-${column.align}`">
                                    <template v-if="column.name == 'actions'">
                                        <button @click.stop="toggleDropdown(column,data,$event)" class="px-2 py-1 rounded hover:bg-gray-300" style="text-align:center;"><EllipsisVertical class="h-4 w-4"/></button>
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
            <div class="px-4 py-3 border-t border-gray-200 bg-white z-1">
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
                        <Pagination :total="total" :items-per-page="pagination._limit"
                            :page="pagination._page" @update:page="(val) => (pagination._page = val)">
                            <PaginationContent v-slot="items">
                                <PaginationPrevious class="bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500" />
                                <PaginationFirst class="bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500" />
                                <PaginationEllipsis class="bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500" v-if="pagination._page > 2"/>
                                <template v-for="(item, index) in items.items" :key="index">
                                    <PaginationItem class="bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500" v-if="item.type === 'page' && item.value >= pagination._page-1 && item.value <= pagination._page + 1 "
                                        :value="item.value" :is-active="item.value === pagination._page"
                                        @click="pagination._page = item.value">
                                        {{ item.value }}
                                    </PaginationItem>
                                </template>
                                <PaginationEllipsis class="bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500" v-if="pagination._page < items.items.length -1"/>
                                <PaginationLast class="bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500" />
                                <PaginationNext class="bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500" />
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
    <FormDialog :open="showDialog" :title="title" :dialog="form.dialog" :sections="form.sections" :formData="form.data" @close="closeFormDialog()" @onSubmit="handleSubmit" />
    <DetailDialog :title="title" :open="showDetail" :data="selected" :schema="detail_schema" @close="showDetail=false"/>
    <div v-if="openDropdown" class="absolute bg-white border rounded shadow-md w-50 z-50" :style="{ top: dropDownPosition.y + 'px', left: dropDownPosition.x + 'px' }">
        <a v-if="columnOptions.includes('detail')" href="#" @click.stop="viewData()" class="flex text-sm items-center px-2 py-1 hover:bg-gray-100"><SquareChartGantt class="w-8 text-green-700 px-2" />Detail</a>
        <a v-if="columnOptions.includes('edit')" href="#" @click.stop="editData()" class="flex text-sm items-center px-2 py-1 hover:bg-gray-100"><SquarePen class="w-8 text-orange-500 px-2" />Ubah</a>
        <a v-if="columnOptions.includes('return')" href="#" @click.stop="returData()" class="flex text-sm items-center px-2 py-1 hover:bg-gray-100"><Blocks class="w-8 text-blue-500 px-2" />Retur</a>
        <a v-if="columnOptions.includes('delete')" href="#" @click.stop="hapusData()" class="flex text-sm items-center px-2 py-1 hover:bg-gray-100"><SquareX class="w-8 text-red-500 px-2" />Hapus</a>
        <a v-if="columnOptions.includes('undo')" href="#" @click.stop="undoData()" class="flex text-sm items-center px-2 py-1 hover:bg-gray-100"><Undo2 class="w-8 text-red-500 px-2" />Urungkan</a>
    </div>
    <div v-if="openContextMenu" class="absolute bg-white border rounded shadow-md w-50 z-50" :style="{ top: contextMenuPosition.y + 'px', left: contextMenuPosition.x + 'px' }">
        <a @click.stop="load();openContextMenu=false" href="#" class="flex text-sm items-center px-2 py-1 hover:bg-gray-100"><RefreshCcw class="w-8 text-green-700 px-2" />Muat Ulang</a>
        <div v-if="allowCreate && columnOptions.includes('create')">
            <a @click.stop="tambahData();openContextMenu=false" href="#" class="flex text-sm items-center px-2 py-1 hover:bg-gray-100"><Plus class="w-8 text-orange-700 px-2" />Tambah</a>
        </div>
        <div v-if="columnOptions.includes('detail')">
            <a @click.stop="viewData();openContextMenu=false" href="#" class="flex text-sm items-center px-2 py-1 hover:bg-gray-100"><SquareChartGantt class="w-8 text-green-700 px-2" />Detail</a>
        </div>
        <div v-if="allowEdit && columnOptions.includes('edit')">
            <a @click.stop="editData();openContextMenu=false" href="#" class="flex text-sm items-center px-2 py-1 hover:bg-gray-100"><SquarePen class="w-8 text-orange-700 px-2" />Ubah</a>
        </div>
        <div v-if="columnOptions.includes('undo')">
            <a @click.stop="hapusData();openContextMenu=false" href="#" class="flex text-sm items-center px-2 py-1 hover:bg-gray-100"><Undo2 class="w-8 text-red-700 px-2" />Urungkan</a>
        </div>
        <div v-if="columnOptions.includes('return')">
            <a @click.stop="returData();openContextMenu=false" href="#" class="flex text-sm items-center px-2 py-1 hover:bg-gray-100"><Blocks class="w-8 text-blue-700 px-2" />Retur</a>
        </div>
        <div v-if="allowDelete && columnOptions.includes('delete') && selectedData.length <= 0">
            <a @click.stop="hapusData();openContextMenu=false" href="#" class="flex text-sm items-center px-2 py-1 hover:bg-gray-100"><SquareX class="w-8 text-red-700 px-2" />Hapus</a>
        </div>
        <div v-if="selectedData.length > 0">
            <a @click.stop="hapusDataMultiple()" href="#" class="flex text-sm items-center px-2 py-1 hover:bg-gray-100"><SquareX class="w-8 text-red-500 px-2" />Hapus Data Terpilih</a>
        </div>
    </div>
</template> 

<script>
import * as operator from "./../constants/operator";
import { Search,LoaderCircle, EllipsisVertical,Blocks,SquareChartGantt,SquarePen,SquareX,Undo2,RefreshCcw,Plus } from "lucide-vue-next"
import { mapGetters } from "vuex";
import { ref } from "vue"
import { Badge } from "@/components/ui";
import FormDialog from "@/components/FormDialog.vue";
import FilterHeader from "@/components/FilterHeader.vue";
import Button from "@/components/ui/button/Button.vue";
import DetailDialog from "@/components/DetailDialog.vue";
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
        Badge,
        FormDialog,
        FilterHeader,
        Button,
        DetailDialog,
        Pagination,
        PaginationContent,
        PaginationEllipsis,
        PaginationFirst,
        PaginationItem,
        PaginationLast,
        PaginationNext,
        PaginationPrevious,
        LoaderCircle,
        EllipsisVertical,
        Blocks,
        SquareChartGantt,
        SquarePen,
        SquareX,
        Undo2,
        RefreshCcw,
        Plus
    },
    props: {
        title: {
            type: String,
            default: "title",
        },
        module: {
            type: String,
            default: ""
        }
    },
    data() {
        return {
            searchQuery: '',
            selectAll: false,
            selectedData: [],
            selectedIndex: null,
            total: 0,
            rows: [],
            form: [],
            columns: [],
            properties: {},
            detail_schema: [],
            allowCreate: false,
            allowDelete: false,
            allowEdit: false,
            allowDelete: false,
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
            openDropdown: false,
            openContextMenu: false,
            tableContainer: ref(null),
            scrollPosition: 0,
            contextMenuPosition: {
                x:0,
                y:0
            },
            dropDownPosition: {
                x:0,
                y:0
            },
            showDetail: false,
            selected: {},
            columnOptions:[]
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
        "rows"(){
            const rm = this.menuRoles.find(
                (role) => role.route === this.$route.path
            )
            this.allowCreate = rm?.create
            this.allowDelete = rm?.delete
            this.allowEdit = rm?.edit
            this.allowDelete = rm?.delete
        }
    },
    computed: {
        ...mapGetters({
            menuRoles: "menu/getMenuRoles"
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
                .dispatch(this.module+'/grid', params)
                .then(({ data }) => {
                    data = data.data;
                    this.rows = data.rows;
                    this.columns = data.columns;
                    this.total = data.total;
                    this.properties = data.properties;
                    this.detail_schema = data.detail_schemes;
                }).finally((f) => {
                    this.loading = false
                })
            
            const action = this.columns.find(x => x.name == 'actions');
            this.columnOptions = action.options
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
            this.loading = true
            await this.$store.dispatch(this.module+'/form').then(({ data }) => {
                this.form = data.data;
                this.selected = {}  
            }).finally(() => {
                this.showDialog = true
                this.loading = false
                this.openDropdown = false
            });
        },
        async editData() {
            this.loading = true
            await this.$store.dispatch(this.module+'/edit', this.selected.encode_id).then(({ data }) => {
                this.form = data.data;
            }).finally(() => {
                this.showDialog = true
                this.loading = false
                this.openDropdown = false
            });
        },
        async hapusData() {
            this.openDropdown = false
            this.$confirm(
                {
                    message: `Apakah anda yakin menghapus data ini?`,
                    button: {
                        no: 'Tidak',
                        yes: 'Ya'
                    },
                    callback: async confirm => {
                        if (confirm) {
                            this.loading = true
                            await this.$store.dispatch(this.module+'/delete', this.selected.encode_id)
                                .then(({ data }) => {
                                    this.load(); // Refresh the data table after deletion
                                })
                                .catch((resp) => {
                                    alert(resp.response.data.message)
                                })
                                .finally((f) => {
                                    this.openDropdown = null
                                    this.loading = false
                                })
                        }
                    }
                }
            )
        },
        viewData() {
            if(this.selected.schema != undefined){
                this.detail_schema = JSON.parse(this.selected.schema)
                this.selected = JSON.parse(this.selected.data)
            }
            this.showDetail = true
            this.openDropdown = false
        },
        returData() {
            alert("Action retur data:");
        },
        undoData(){
            alert("Action undo data:");
        },
        truncateData(){
            this.$confirm(
                {
                    message: `Apakah anda yakin menghapus semua data di modul ini?`,
                    button: {
                        no: 'Tidak',
                        yes: 'Ya'
                    },
                    callback: async confirm => {
                        if (confirm) {
                            this.loading = true
                            await this.$store.dispatch(this.module+'/truncate')
                                .then(({ data }) => {
                                    this.load();
                                    alert(data.message)
                                })
                                .catch((resp) => {
                                    alert(resp.response.data.message)
                                })
                                .finally((f) => {
                                    this.openDropdown = null
                                    this.loading = false
                                })
                        }
                    }
                }
            )
        },
        async handleSubmit(form) {
            this.loading = true
            await this.$store.dispatch(this.module+'/create',form)
            .then(({ data }) => {
                this.load();
                if(data.message != undefined && data.status == true) {
                    alert(data.message)
                    this.showDialog = false
                    this.form = {}
                    this.selected = {}
                }

            }).catch((resp) => {
                let msgError = '';
                if(resp.response.data?.data != undefined){
                    const errors = Object.values(resp.response.data.data);
                    msgError = errors[0]
                }
                alert(resp.response.data.message+ ' : '+msgError)
            })
            .finally((f) => {
                this.openDropdown = null
                this.loading = false
            })
        },
        hapusDataMultiple(){
            alert('hapus data terpilih')
        },
        toggleDropdown(column,data,e) {
            this.columnOptions = column.options
            this.openDropdown = true
            this.openContextMenu = false
            this.selected = data
            this.dropDownPosition.x = e.clientX
            this.dropDownPosition.y = e.clientY
        },
        closeFormDialog() {
            this.showDialog = false;
        },
        closeContextMenu(){
            this.openContextMenu = false
        },
        handleClickOutside(e) {
            if (!this.$el.contains(e.target)) this.openDropdown = null
        },
        handleScroll(e) {
            let position = e.target.scrollTop
            if(position != this.scrollPosition)this.openDropdown = null
            this.scrollPosition = position
        },
        handleRightClick(e) {
            this.contextMenuPosition.x = e.clientX
            this.contextMenuPosition.y = e.clientY
            this.openContextMenu = true
            this.openDropdown = false
        },
        handleClickRow(data, index,e) {
            if (e.target.type !== 'checkbox') {
                this.selected = data
                this.selectedIndex = index
            }
            
        }
    },
    mounted() {
        document.addEventListener("click", this.handleClickOutside)
        this.$refs.tableContainer.addEventListener("scroll", this.handleScroll)
        document.addEventListener("click", this.closeContextMenu)
    },
    beforeMount(){
        this.load();
    },
    beforeUnmount() {
        document.removeEventListener("click", this.handleClickOutside)
        this.$refs.tableContainer.removeEventListener("scroll", this.handleScroll)
        document.removeEventListener("click", this.closeContextMenu)
    }   
};
</script>
