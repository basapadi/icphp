<template>
    <div class="bg-white rounded-lg shadow-xs border border-gray-200 z-1">
        <div class="h-screen">
            <div class="px-1 py-1 h-auto border-b border-gray-300">
                <div class="flex justify-between">
                    <div>
                        <FilterHeader :columns="columns" @load="load" :pagination="pagination" :operators="operators" :filter="filter" :properties="properties"/>
                    </div>
                </div>
            </div>
            <!-- Table -->
            <div class="overflow-x-auto h-7/10 z-1" ref="tableContainer">
                <table class="min-w-full border-collapse border border-dashed border-orange-100 z-1">
                    <thead class="bg-orange-50 sticky top-0" style="z-index:11">
                        <tr>
                            <template v-if="properties.multipleSelect">
                                <th 
                                    class="px-4 py-1 text-left text-xs text-gray-500 uppercase tracking-wider border-1 border-dashed border-gray-00">
                                    <input v-model="selectAll" style="transform: scale(1.3);cursor: pointer;" @change="toggleSelectAll" type="checkbox"
                                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500" />
                                </th>
                            </template>
                            <template v-for="column in columns" :key="column.value">
                                <template v-if="column.show">
                                    <template v-if="column.name == 'actions'">
                                        <th class="px-4 py-2 text-left font-bold text-xs text-gray-500 uppercase tracking-wider border border-1 border-dashed border-gray-300"
                                            style="width: 30px !important">
                                            {{ column.label }}
                                        </th>
                                    </template>
                                    <template v-else>
                                        <th :class="`px-4 py-2 text-left font-bold text-xs text-gray-500 uppercase tracking-wider border border-1 border-dashed border-gray-300 text-${column.align}`"
                                            :style="`${column.styles}`">
                                            {{ column.label }}
                                        </th>
                                    </template>
                                </template>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(data,index) in filterData" :key="data.id" :class="['transition-colors duration-50 border border-1 border-dashed text-sm border-gray-300',selectedIndex.includes(index) ? 'bg-orange-200' : 'hover:bg-orange-100 odd:bg-gray-100 even:bg-white']" @dblclick="viewData(data)" @click="handleClickRow(data,index,$event)" @contextmenu.prevent="handleRightClick(data,index,$event)">
                            <template v-if="properties.multipleSelect">
                                <td class="px-4 whitespace-nowrap border border-dashed border-1 border-gray-300" style="width: 10px">
                                    <input @change.stop="handleCheckboxChange(index, $event)" v-model="selectedData" :value="data.id" type="checkbox"
                                        class="rounded border-gray-100 text-orange-600 focus:ring-orange-500" style="transform: scale(1.3);cursor: pointer;" />
                                </td>
                            </template>
                            <template v-for="column in columns" :key="column.value">
                                <td v-if="column.show" :class="`relative px-4 py-1 whitespace-nowrap border border-1 border-dashed border-gray-300 text-${column.align}`">
                                    <template v-if="column.name == 'actions'">
                                        <button @click.stop="toggleDropdown(column,data,$event)" class="px-2 py-1 rounded hover:bg-gray-300" style="text-align:center;"><EllipsisVertical class="h-4 w-4"/></button>
                                    </template>
                                    <template v-else-if="column.type === 'badge'">
                                        <div :class="`no-select inline-flex items-center rounded-md bg-${data[`color_${column.name}`]
                                            }/50 px-2 text-xs font-sm text-${data[`color_${column.name}`]
                                            } inset-ring inset-ring-${data[`color_${column.name}`]
                                            }/50`">
                                            {{$helpers.getSubObjectValue(data,column.name)}}
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span :class="`text-sm text-gray-600 ${column.class}`"
                                            :style="`${column.styles}`">{{$helpers.getSubObjectValue(data,column.name)}}</span>
                                    </template>
                                </td>
                            </template>
                        </tr>
                    </tbody>
                </table>
            </div>
                <!-- Table Footer -->
            <div class="px-4 py-3 h-auto border-t border-gray-200 bg-white z-1">
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
            <div v-if="loading" class="absolute inset-0 flex pt-80 justify-center bg-white/40 z-10" >
                <div>
                    <div class="loader"></div>
                </div>
                <span class="justify-center text-orange-500 ml-2">Sedang memuat ...</span>
            </div>
        </div>
    </div>
    <FormDialog :open="showDialog" :title="title" :dialog="form.dialog" :sections="form.sections" :formData="form.data" @close="this.showDialog = false" @onSubmit="handleSubmit" />
    <ConfirmDialog :open="showConfirmDialog" :contextMenu="selectedContextMenu" @onSubmit="handleSubmitConfirmDialog" @close="this.showConfirmDialog = false"/>
    <DetailDialog :title="title" :open="showDetail" :data="selected" :schema="detail_schema" @close="showDetail=false"/>
    <div v-if="openDropdown" class="absolute bg-white border rounded shadow-md w-50 z-50" :style="{ top: dropDownPosition.y + 'px', left: dropDownPosition.x + 'px' }">
        <div v-if="this.properties.contextMenu.length > 0">
            <div v-for="cm in this.properties.contextMenu.filter(cm => matchContextMenuConditions(cm.conditions))" :key="cm.name">
                <a class="flex text-sm items-center px-2 py-1 bordered border-t border-dashed hover:bg-gray-100" href="#" @click.stop="callByFunctionName(cm);openDropdown=false"><component :is="cm.icon" :color="cm.color" class="w-8 px-2" />{{cm.label}}</a>
            </div>
        </div>

    </div>
    <div v-if="openContextMenu" class="absolute bg-white border rounded shadow-md w-50 z-50" :style="{ top: contextMenuPosition.y + 'px', left: contextMenuPosition.x + 'px' }">
        <div v-if="this.properties.contextMenu.length > 0 && selectedData.length <= 1">
            <div v-for="cm in this.properties.contextMenu.filter(cm => matchContextMenuConditions(cm.conditions))" :key="cm.name">
                <a @click.stop="callByFunctionName(cm);openContextMenu=false" href="#" class="flex text-sm items-center px-2 bordered border-t border-dashed r py-1 hover:bg-gray-100"><component :is="cm.icon" :color="cm.color" class="w-8 px-2" />{{cm.label}}</a>
            </div>
        </div>
    </div>
</template> 

<script>
import * as operator from "./../constants/operator";
import { mapGetters } from "vuex";
import { ref } from "vue"
import { Badge } from "@/components/ui";
import FormDialog from "@/components/FormDialog.vue";
import FilterHeader from "@/components/FilterHeader.vue";
import Button from "@/components/ui/button/Button.vue";
import DetailDialog from "@/components/DetailDialog.vue";
import ConfirmDialog from "@/components/ConfirmDialog.vue";
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
        Badge,
        FormDialog,
        ConfirmDialog,
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
        PaginationPrevious
    },
    props: {
        title: {
            type: String,
            default: "title",
        },
        query: {
            type: Object,
            default: {}
        },
        defaultFilter: {
            type: Object,
            default: {}
        }
    },
    data() {
        return {
            searchQuery: '',
            selectedIndex: [],
            total: 0,
            rows: [],
            form: [],
            columns: [],
            properties: {},
            showDialog: false,
            showConfirmDialog: false,
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
            columnOptions:[],
            selectedContextMenu: null
        };
    },
    watch: {
        query: {
            handler() {
                this.filter = {
                    operator: '',
                    column: '',
                    value: '',
                }
                if(this.searchQuery == '' && this.defaultFilter.column != undefined) this.filter = this.defaultFilter
                this.load();
            },
            immediate: false // langsung load pertama kali juga
        },
        currentPage() {
            this.load();
        },
        "pagination._page"() {
            this.load();
        }
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
            this.loading = true
            this.selectAll = false;
            this.selectedData = [];
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
                
            }
            params.path = this.query.path +'/'+this.query.name;

            await this.$store
                .dispatch('report/grid', params)
                .then(({ data }) => {
                    data = data.data;
                    this.rows = data.rows;
                    this.columns = data.columns;
                    this.total = data.total;
                    this.properties = data.properties;
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
            
            const action = this.columns.find(x => x.name == 'actions');
            this.selectedData = []
            this.selectedIndex = []
            if(action?.options != undefined) this.columnOptions = action.options
        },
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        },
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedData = this.filterData.map((dt) => dt.id);
                this.selectedIndex = this.filterData.map((dt,index) => index);
            } else {
                this.selectedData = [];
                this.selectedIndex = [];
            }
        },
        async tambahData() {
            this.loading = true
            await this.$store.dispatch('report/form').then(({ data }) => {
                this.form = data.data;
                this.selected = {}  
            }).finally(() => {
                this.showDialog = true
                this.loading = false
                this.openDropdown = false
            });
        },
        toggleDropdown(column,data,e) {
            this.columnOptions = column.options
            this.openDropdown = true
            this.openContextMenu = false
            this.selected = data
            this.dropDownPosition.x = e.clientX
            this.dropDownPosition.y = e.clientY
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
        handleRightClick(data,index, e) {
            this.selected = data
            this.contextMenuPosition.x = e.clientX
            this.contextMenuPosition.y = e.clientY
            this.openContextMenu = true
            this.openDropdown = false
        },
        handleClickRow(data, index,e) {
            if (e.target.type !== 'checkbox') {
                this.selected = data
                this.selectedIndex = [index]
                if(this.properties.multipleSelect){
                    this.selectedData = [data.id]
                    this.selectAll = false
                }
            }
        },
        handleCheckboxChange(index, e) {
            if (e.target.checked) {
                this.selectedIndex.push(index)
                this.selected = null
            } else {
                this.selectedIndex = this.selectedIndex.filter(i => i !== index)
            }
        },
        matchContextMenuConditions(conditions) {
            if (!conditions || Object.keys(conditions).length === 0) return true;
            return Object.entries(conditions).every(([key, value]) => {
                const selectedValue = this.selected[key];
                if (Array.isArray(value)) {
                    return value.includes(selectedValue);
                }
                return selectedValue === value;
            });
        },
        callByFunctionName(cm){
            this[cm.onClick](cm)
        },
        test(cm){
            alert(`Test, buat function sebenarnya di datatable component`)
        },
        async getFormDialog(cm){
            if(cm.type !== "form_dialog")return
            this.selectedContextMenu = cm
            this.loading = true
            const res = await axios.get(cm.formUrl, {
                params: {id: this.selected.encode_id}
            })
            .catch(err => console.error(err))
            .finally(() => {
                this.showDialog = true
                this.loading = false
                this.openDropdown = false
            });
            this.form = res.data.data

        },
        async confirmPopup(cm){
            if(cm.type == "confirm"){
                this.selectedContextMenu = cm
                this.showConfirmDialog = true
            } else if(cm.type == 'download_pdf'){
                this.selectedContextMenu = cm
                await axios.post(cm.apiUrl, {
                    id: this.selected.encode_id
                }, {
                    responseType: 'blob'
                })
                .then((response) => {
                    const disposition = response.headers['content-disposition'];
                    let filename = "download.pdf";

                    if (disposition) {
                        // Prioritaskan filename* (UTF-8)
                        const utf8FilenameRegex = /filename\*\=UTF-8''([^;]+)/i;
                        const asciiFilenameRegex = /filename\=\"([^"]+)\"/i;

                        if (utf8FilenameRegex.test(disposition)) {
                            filename = decodeURIComponent(utf8FilenameRegex.exec(disposition)[1]);
                        } else if (asciiFilenameRegex.test(disposition)) {
                            filename = asciiFilenameRegex.exec(disposition)[1];
                        }
                    }

                    // Buat blob dan URL untuk download
                    const blob = new Blob([response.data], { type: 'application/pdf' });
                    const url = window.URL.createObjectURL(blob);

                    // Trigger download
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', filename);
                    document.body.appendChild(link);
                    link.click();

                    // Bersihkan
                    link.remove();
                    window.URL.revokeObjectURL(url);
                })
                .catch((resp) => {
                    alert(resp.response?.data?.message)
                })
            }
            
        },
        async handleSubmitConfirmDialog(form){
            this.loading = true
            form.id = this.selected.encode_id
            await axios.post(this.selectedContextMenu.apiUrl, form)
            .then(({data}) => {
                if(data.message != undefined && data.status == true) {
                    alert(data.message)
                }
            })
            .catch((resp) => {
                alert(resp.response.data.message)
            })
            .finally(() => {
                this.loading = false
                this.openDropdown = false
                this.showConfirmDialog = false
                this.load()
            });
        }
    },
    mounted() {
        document.addEventListener("click", this.handleClickOutside)
        this.$refs.tableContainer.addEventListener("scroll", this.handleScroll)
        document.addEventListener("click", this.closeContextMenu)
    },
    beforeUnmount() {
        document.removeEventListener("click", this.handleClickOutside)
        this.$refs.tableContainer.removeEventListener("scroll", this.handleScroll)
        document.removeEventListener("click", this.closeContextMenu)
    }   
};
</script>
