<template>
    <div class="rounded-lg shadow-xs border border-border z-1">
        <div class="h-screen">
            <div class="px-1 py-1 h-auto border-b border-border">
                <div class="flex justify-between">
                    <div>
                        <FilterHeader
                            :columns="columns"
                            @load="load"
                            :pagination="pagination"
                            :operators="operators"
                            :filter="filter"
                            :properties="properties"
                        />
                    </div>
                    <div v-if="query != null">
                        <div
                            class="flex flex-col md:flex-row md:items-center gap-1"
                        >
                            <div class="relative mr-1">
                                <Button
                                    class="bg-blue-50 border-1 border-blue-600 rounded-md hover:bg-blue-200 text-blue-600 transition-colors delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103"
                                    @click="downloadReport"
                                    size="sm"
                                >
                                    <Download class="w-3 h-3" />
                                </Button>
                            </div>
                            <div class="relative mr-1">
                                <Button
                                    class="bg-green-50 border-1 border-green-600 rounded-md hover:bg-green-200 text-green-600 transition-colors delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103"
                                    @click="editReport"
                                    size="sm"
                                >
                                    <SquarePen class="w-3 h-3" />
                                </Button>
                            </div>
                            <div class="relative mr-1">
                                <Button
                                    class="bg-red-50 border-1 border-red-200 rounded-md hover:bg-red-200 text-red-500 transition-colors delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103"
                                    @click="deleteReport"
                                    size="sm"
                                >
                                    <Trash class="w-3 h-3" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Table -->
            <div class="overflow-x-auto h-7/10 z-1" ref="tableContainer">
                <table
                    class="min-w-full border-collapse border border-dashed border-primary/20 z-1"
                >
                    <thead
                        class="bg-primary/10 sticky top-0"
                        style="z-index: 11"
                    >
                        <tr>
                            <template
                                v-for="column in columns"
                                :key="column.value"
                            >
                                <template v-if="column.show">
                                    <template v-if="column.name == 'actions'">
                                        <th
                                            class="px-4 py-2 text-left font-bold text-xs text-muted-foreground uppercase tracking-wider border border-1 border-dashed border-border"
                                            style="width: 30px !important"
                                        >
                                            {{ column.label }}
                                        </th>
                                    </template>
                                    <template v-else>
                                        <th
                                            :class="`px-4 py-2 text-left font-bold text-xs text-muted-foreground uppercase tracking-wider border border-1 border-dashed border-border text-${column.align}`"
                                            :style="`${column.styles}`"
                                        >
                                            {{ column.label }}
                                        </th>
                                    </template>
                                </template>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="(data, index) in filterData"
                            :key="data.id"
                            :class="[
                                'transition-colors duration-50 border border-1 border-dashed text-sm border-border',
                                selectedIndex.includes(index)
                                    ? 'bg-primary/20'
                                    : 'hover:bg-primary/10 odd:bg-muted/20 even:bg-background/30',
                            ]"
                            @dblclick="viewData(data)"
                            @click="handleClickRow(data, index, $event)"
                            @contextmenu.prevent="
                                handleRightClick(data, index, $event)
                            "
                        >
                            <template v-if="properties.multipleSelect">
                                <td
                                    class="px-4 whitespace-nowrap border border-dashed border-1 border-border"
                                    style="width: 10px"
                                >
                                    <input
                                        @change.stop="
                                            handleCheckboxChange(index, $event)
                                        "
                                        v-model="selectedData"
                                        :value="data.id"
                                        type="checkbox"
                                        class="rounded border-border text-primary focus:ring-primary"
                                        style="
                                            transform: scale(1.3);
                                            cursor: pointer;
                                        "
                                    />
                                </td>
                            </template>
                            <template
                                v-for="column in columns"
                                :key="column.value"
                            >
                                <td
                                    v-if="column.show"
                                    :class="`relative px-4 py-1 whitespace-nowrap border border-1 border-dashed border-border text-${column.align}`"
                                >
                                    <template v-if="column.name == 'actions'">
                                        <button
                                            @click.stop="
                                                toggleDropdown(
                                                    column,
                                                    data,
                                                    $event
                                                )
                                            "
                                            class="px-2 py-1 rounded hover:bg-muted"
                                            style="text-align: center"
                                        >
                                            <EllipsisVertical class="h-4 w-4" />
                                        </button>
                                    </template>
                                    <template
                                        v-else-if="column.type === 'badge'"
                                    >
                                        <div
                                            :class="`no-select inline-flex items-center rounded-md bg-${
                                                data[`color_${column.name}`]
                                            }/50 px-2 text-xs font-sm text-${
                                                data[`color_${column.name}`]
                                            } inset-ring inset-ring-${
                                                data[`color_${column.name}`]
                                            }/50`"
                                        >
                                            {{
                                                $helpers.getSubObjectValue(
                                                    data,
                                                    column.name
                                                )
                                            }}
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span
                                            :class="`text-sm text-foreground ${column.class}`"
                                            :style="`${column.styles}`"
                                            >{{
                                                $helpers.getSubObjectValue(
                                                    data,
                                                    column.name
                                                )
                                            }}</span
                                        >
                                    </template>
                                </td>
                            </template>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Table Footer -->
            <div
                class="px-4 py-3 h-auto border-t border-border bg-background z-1"
            >
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
                        <Pagination
                            :total="total"
                            :items-per-page="pagination._limit"
                            :page="pagination._page"
                            @update:page="(val) => (pagination._page = val)"
                        >
                            <PaginationContent v-slot="items">
                                <PaginationPrevious
                                    class="bg-primary/20 border-1 border-primary/20 rounded-md hover:bg-primary/30 text-primary"
                                />
                                <PaginationFirst
                                    class="bg-primary/20 border-1 border-primary/20 rounded-md hover:bg-primary/30 text-primary"
                                />
                                <PaginationEllipsis
                                    class="bg-primary/20 border-1 border-primary/20 rounded-md hover:bg-primary/30 text-primary"
                                    v-if="pagination._page > 2"
                                />
                                <template
                                    v-for="(item, index) in items.items"
                                    :key="index"
                                >
                                    <PaginationItem
                                        class="bg-primary/20 border-1 border-primary/20 rounded-md hover:bg-primary/30 text-primary"
                                        v-if="
                                            item.type === 'page' &&
                                            item.value >=
                                                pagination._page - 1 &&
                                            item.value <= pagination._page + 1
                                        "
                                        :value="item.value"
                                        :is-active="
                                            item.value === pagination._page
                                        "
                                        @click="pagination._page = item.value"
                                    >
                                        {{ item.value }}
                                    </PaginationItem>
                                </template>
                                <PaginationEllipsis
                                    class="bg-primary/20 border-1 border-primary/20 rounded-md hover:bg-primary/30 text-primary"
                                    v-if="
                                        pagination._page <
                                        items.items.length - 1
                                    "
                                />
                                <PaginationLast
                                    class="bg-primary/20 border-1 border-primary/20 rounded-md hover:bg-primary/30 text-primary"
                                />
                                <PaginationNext
                                    class="bg-primary/20 border-1 border-primary/20 rounded-md hover:bg-primary/30 text-primary"
                                />
                            </PaginationContent>
                        </Pagination>
                    </div>
                </div>
            </div>
            <div
                v-if="loading"
                class="absolute inset-0 flex pt-80 justify-center bg-background/40 z-10"
            >
                <div>
                    <div class="loader"></div>
                </div>
                <span class="justify-center text-primary ml-2"
                    >Sedang memuat ...</span
                >
            </div>
        </div>
    </div>
    <div
        v-if="openBuilder"
        class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
        @click="openBuilder = false"
    >
        <Card :class="`w-full max-w-7xl py-0`" @click.stop>
            <QueryBuilder
                :existQuery="existQuery"
                :popup="true"
                @open="closeQueryBuilder"
            />
        </Card>
    </div>
</template>

<script>
import * as operator from "./../constants/operator";
import { mapGetters } from "vuex";
import { ref } from "vue";
import { Badge } from "@/components/ui";
import FilterHeader from "@/components/FilterHeader.vue";
import QueryBuilder from "@/components/QueryBuilder.vue";
import Button from "@/components/ui/button/Button.vue";
import { Card } from "@/components/ui/card";
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
import Trash from "@/views/Trash.vue";

export default {
    emits: ["reloadList"],
    components: {
        Badge,
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
        QueryBuilder,
        Card,
    },
    props: {
        title: {
            type: String,
            default: "title",
        },
        query: {
            type: Object,
            default: {},
        },
        defaultFilter: {
            type: Object,
            default: {},
        },
    },
    data() {
        return {
            searchQuery: "",
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
                operator: "",
                column: "",
                value: "",
            },
            operators: operator.Operator,
            loading: false,
            openDropdown: false,
            openContextMenu: false,
            tableContainer: ref(null),
            scrollPosition: 0,
            showDetail: false,
            selected: {},
            columnOptions: [],
            openBuilder: false,
            existQuery: {},
        };
    },
    watch: {
        query: {
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
            this.loading = true;
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

                if (this.filter.operator == "_between") {
                    filter_value = `${this.filter.value_from},${this.filter.value_to}`;
                }

                if (this.filter.column != undefined && this.filter.column != "") {
                    params[`${this.filter.column}${this.filter.operator}`] = filter_value;
                }
            }
            params.path = this.query.path + "/" + this.query.name;

            await this.$store
                .dispatch("report/grid", params)
                .then(({ data }) => {
                    data = data.data;
                    this.rows = data.rows;
                    this.columns = data.columns;
                    this.total = data.total;
                    this.properties = data.properties;
                    this.existQuery = data.query;
                    this.existQuery.name = this.query.label;
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

            const action = this.columns.find((x) => x.name == "actions");
            this.selectedData = [];
            this.selectedIndex = [];
            if (action?.options != undefined)
                this.columnOptions = action.options;
        },
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        },
        toggleDropdown(column, data, e) {
            this.columnOptions = column.options;
            this.openDropdown = true;
            this.openContextMenu = false;
            this.selected = data;
            this.dropDownPosition.x = e.clientX;
            this.dropDownPosition.y = e.clientY;
        },
        handleClickOutside(e) {
            if (!this.$el.contains(e.target)) this.openDropdown = null;
        },
        handleScroll(e) {
            let position = e.target.scrollTop;
            if (position != this.scrollPosition) this.openDropdown = null;
            this.scrollPosition = position;
        },
        async downloadReport() {
            let params = { ...this.pagination };
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

            if (this.filter.column != undefined && this.filter.column != "") {
                params[`${this.filter.column}${this.filter.operator}`] = filter_value;
            }
            params.path = this.query.path + "/" + this.query.name;
            params.name = this.query.label;
            await this.$store
                .dispatch("report/downloadQuery", params)
                .then((response) => {
                    const blob = new Blob(
                        [response.data],
                        { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }
                    )

                    const disposition = response.headers['content-disposition']
                    let filename = 'report.xlsx'

                    if (disposition) {
                        const match = disposition.match(/filename\s*=\s*([^;]+)/i)
                        if (match && match[1]) {
                            filename = match[1].trim()
                        }
                    }


                    const url = window.URL.createObjectURL(blob)
                    const link = document.createElement('a')

                    link.href = url
                    link.setAttribute('download', filename)
                    document.body.appendChild(link)
                    link.click()

                    link.remove()
                    window.URL.revokeObjectURL(url)
                    this.$emit("reloadList", true);
                })
                .catch((resp) => {
                     if (resp.response && resp.response.data) {
                        const blob = resp.response.data
                        blob.text().then(text => {
                            try {
                                const json = JSON.parse(text)
                                alert(json.message)
                            } catch {
                                alert('Terjadi kesalahan saat memproses data.')
                            }
                        })
                    } else {
                        alert(resp.message)
                    }
                })
                .finally((f) => {});
        },
        editReport() {
            this.openBuilder = true;
        },
        deleteReport() {
            this.$confirm({
                message: `Apakah anda yakin menghapus laporan ini?`,
                button: {
                    no: "Tidak",
                    yes: "Ya",
                },
                callback: async (confirm) => {
                    if (confirm) {
                        await this.$store
                            .dispatch("report/deleteQuery", this.query.name)
                            .then(({ data }) => {
                                this.$emit("reloadList", true);
                                alert("Query laporan berhasil dihapus");
                            })
                            .catch((resp) => {
                                alert(resp.response?.data?.message);
                            })
                            .finally((f) => {});
                    }
                },
            });
        },
        closeQueryBuilder(state) {
            this.openBuilder = state;
        },
    },
    mounted() {
        document.addEventListener("click", this.handleClickOutside);
        this.$refs.tableContainer.addEventListener("scroll", this.handleScroll);
        document.addEventListener("click", this.closeContextMenu);
    },
    beforeUnmount() {
        document.removeEventListener("click", this.handleClickOutside);
        this.$refs.tableContainer.removeEventListener(
            "scroll",
            this.handleScroll
        );
        document.removeEventListener("click", this.closeContextMenu);
    },
};
</script>
