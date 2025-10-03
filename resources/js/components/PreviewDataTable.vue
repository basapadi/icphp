<template>
    <div class="z-1" ref="tableContainer">
        <table class="table-auto border-collapse border border-dashed border-orange-100 z-1">
            <thead class="bg-orange-50 sticky top-0" style="z-index:11">
                <tr>
                    <template v-for="column in columns" :key="column.value">
                        <template v-if="column.show">
                            <th :class="`px-4 py-2 text-left font-bold text-xs text-gray-500 uppercase border border-1 border-dashed border-gray-300 text-${column.align}`"
                                :style="`${column.styles}`">
                                {{ column.label }}
                            </th>
                        </template>
                    </template>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(data,index) in rows" :key="data.id" :class="['transition-colors duration-50 border border-1 border-dashed text-sm border-gray-300',selectedIndex.includes(index) ? 'bg-orange-200' : 'hover:bg-orange-100 odd:bg-gray-100 even:bg-white']">
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
</template> 

<script>
import * as operator from "./../constants/operator";
import { mapGetters } from "vuex";
import { ref } from "vue"
import { Badge } from "@/components/ui";
import FilterHeader from "@/components/FilterHeader.vue";
import Button from "@/components/ui/button/Button.vue";

export default {
    components: {
        Badge,
        Button
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
            pagination: {
                _limit: 20,
                _page: 0,
            },
            filter: {
                operator: '',
                column: '',
                value: '',
            },
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

            selected: {},
            columnOptions:[],
            selectedContextMenu: null
        };
    },
    watch: {
        'query.query': {
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

    },
    methods: {
        async load(reset) {
            this.loading = true
            this.$emit("loading", true);
            this.selectAll = false;
            this.selectedData = [];
            let params = { ...this.pagination };
            params.path = this.query.path +'/'+this.query.name;
            params.rawQuery = this.query.query
            await this.$store
                .dispatch('report/preview', params)
                .then(({ data }) => {
                    data = data.data;
                    this.rows = data.rows;
                    this.columns = data.columns;
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
                    this.$emit("loading", false);
                })
            
            this.selectedData = []
            this.selectedIndex = []
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
