<template>
    <div class="relative rounded-lg shadow-sm border border-border z-1">
        <div class="h-screen relative">
            <!-- Table Header -->
            <div class="px-1 py-1 h-auto border-b border-border">
                <div class="flex justify-between">
                    <div class="flex gap-2">
                        <div v-if="allowCreate && columnOptions.length > 0">
                            <Button v-if="columnOptions.includes('create')" @click="tambahData" size="sm">
                                Tambah
                            </Button>
                        </div>
                        <div>
                            <FilterHeader :columns="columns" @load="load" :pagination="pagination"
                                :operators="operators" :filter="filter" :properties="properties" />
                        </div>
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
                        </div>
                    </div>
                </div>
            </div>
            <!-- Table -->
            <div class="overflow-x-auto h-15/20" ref="tableContainer">
                <table class="min-w-full relative border-collapse border border-dashed border-primary/20">
                    <thead class="bg-accent sticky top-0" style="z-index: 11">
                        <tr>
                            <template v-if="properties.multipleSelect">
                                <th
                                    class="px-4 py-1 text-left text-xs text-muted-foreground uppercase tracking-wider border-1 border-dashed border-border">
                                    <input v-model="selectAll" style="
                                            transform: scale(1.3);
                                            cursor: pointer;
                                        " @change="toggleSelectAll" type="checkbox"
                                        class="rounded border-border text-primary focus:ring-primary" />
                                </th>
                            </template>
                            <template v-for="column in columns" :key="column.value">
                                <template v-if="column.show">
                                    <template v-if="column.name == 'actions'">
                                        <th class="px-4 py-2 text-left font-bold text-xs text-muted-foreground uppercase tracking-wider border border-1 border-dashed border-border"
                                            style="width: 30px !important">
                                            {{ column.label }}
                                        </th>
                                    </template>
                                    <template v-else>
                                        <th :class="`px-4 py-2 text-left font-bold text-xs text-muted-foreground uppercase tracking-wider border border-1 border-dashed border-border text-${column.align}`"
                                            :style="`${column.styles}`">
                                            {{ column.label }}
                                        </th>
                                    </template>
                                </template>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="bg-background/10 divide-y divide-border">
                        <tr v-for="(data, index) in filterData" :key="data.id" :class="[
                            'transition-colors duration-50 border border-1 border-dashed text-sm border-border',
                            selectedIndex.includes(index)
                                ? 'bg-primary/20'
                                : 'hover:bg-primary/10 odd:bg-muted/20 even:bg-background/30',
                        ]" @dblclick="viewData(data)" @click="handleClickRow(data, index, $event)"
                            @contextmenu.prevent="
                                handleRightClick(data, index, $event)
                                ">
                            <template v-if="properties.multipleSelect">
                                <td class="px-4 whitespace-nowrap border border-dashed border-1 border-border"
                                    style="width: 10px">
                                    <input @change.stop="
                                        handleCheckboxChange(index, $event)
                                        " v-model="selectedData" :value="data.id" type="checkbox"
                                        class="rounded border-border text-primary focus:ring-primary" style="
                                            transform: scale(1.3);
                                            cursor: pointer;
                                        " />
                                </td>
                            </template>
                            <template v-for="column in columns" :key="column.value">
                                <td v-if="column.show"
                                    :class="`relative px-4 py-1 whitespace-nowrap border border-1 border-dashed border-border text-${column.align}`">
                                    <template v-if="column.name == 'actions'">
                                        <button @click.stop="
                                            toggleDropdown(
                                                column,
                                                data,
                                                $event,
                                                index
                                            )
                                            " class="px-2 py-1 rounded hover:bg-muted" style="text-align: center">
                                            <EllipsisVertical class="h-4 w-4" />
                                        </button>
                                    </template>
                                    <template v-else-if="column.type === 'badge'">
                                        <div :class="`no-select inline-flex items-center rounded-md bg-${data[`color_${column.name}`]
                                            }/50 px-2 text-xs font-sm text-${data[`color_${column.name}`]
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
                                        <span :class="`text-sm text-foreground ${column.class}`"
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
                                    v-if="pagination._page > 2" />
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
            <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-background/40 z-10">
                <div class="flex items-center space-x-2">
                    <div class="loader"></div>
                </div>
            </div>
        </div>
    </div>
    <FormDialog :open="showDialog" :title="title" :dialog="form.dialog" :sections="form.sections" :formData="form.data"
        @close="this.showDialog = false" @onSubmit="handleSubmit" />
    <EditorDialog :open="showEditorDialog" :title="title" :dialog="form.dialog" :sections="form.sections" :formData="form.data"
        @close="this.showEditorDialog = false" @onSubmit="handleSubmitEditor" />
    <ConfirmDialog :open="showConfirmDialog" :contextMenu="selectedContextMenu" @onSubmit="handleSubmitConfirmDialog"
        @close="this.showConfirmDialog = false" :isLoading="confirmDialogLoading" />
    <ChecklistDialog :open="showChecklistDialog" :contextMenu="selectedContextMenu" :dialog="form.dialog"
        :sections="form.sections" :points="form.points" @onSubmit="handleSubmitChecklistDialog"
        @close="this.showChecklistDialog = false" />
    <StepsDialog :open="showStepsDialog" :contextMenu="selectedContextMenu" :dialog="form.dialog"
        :steps="form.steps" :formData="form.data" :title="form.title" :current="form.currentStep" @onSubmit="handleSubmitStepsDialog"
        @close="this.showStepsDialog = false" />
    <DetailDialog :title="title" :open="showDetail" :data="selected" :schema="detail_schema"
        @close="showDetail = false" />
    <ColumnEditor :open="showColumnEditor" :columns="columns" @close="showColumnEditor = false" @reload="reload"
        :module="apiModule" />
    <PreviewPdfDialog :open="showPdfPreview" :pdfUrl="pdfUrl" @close="closePreview" />

    <div v-if="openDropdown" class="absolute bg-card border rounded shadow-md w-100 z-50" :style="{
        top: dropDownPosition.y + 'px',
        left: dropDownPosition.x + 'px',
    }">
        <div v-if="this.properties.contextMenu.length > 0">
            <div v-for="cm in this.properties.contextMenu.filter((cm) =>
                matchContextMenuConditions(cm.conditions)
            )" :key="cm.name">
                <a class="flex text-sm items-center px-2 py-1 bordered border-t border-dashed hover:bg-accent" href="#"
                    @click.stop="
                        callByFunctionName(cm);
                    openDropdown = false;
                    ">
                    <component :is="cm.icon" :color="cm.color" class="w-8 px-2" />{{ cm.label }}
                </a>
            </div>
        </div>
    </div>
    <div v-if="openContextMenu" class="absolute bg-card border rounded shadow-md w-100 z-50" :style="{
        top: contextMenuPosition.y + 'px',
        left: contextMenuPosition.x + 'px',
    }">
        <div v-if="
            this.properties.contextMenu.length > 0 &&
            selectedData.length <= 1
        ">
            <div v-for="cm in this.properties.contextMenu.filter((cm) =>
                matchContextMenuConditions(cm.conditions)
            )" :key="cm.name">
                <a @click.stop="
                    callByFunctionName(cm);
                openContextMenu = false;
                " href="#"
                    class="flex text-sm items-center px-2 bordered border-t border-dashed r py-1 hover:bg-accent">
                    <component :is="cm.icon" :color="cm.color" class="w-8 px-2" />{{ cm.label }}
                </a>
            </div>
        </div>
        <div v-if="selectedData.length > 1">
            <a @click.stop="hapusDataMultiple()" href="#" class="flex text-sm items-center px-2 py-1 hover:bg-accent">
                <CopyX class="w-8 text-destructive px-2" />Hapus Data
                Terpilih
            </a>
        </div>
    </div>
</template>

<script>
const audioAlertError = new Audio("/audio/soundAlertError.mp3");
const audioWarning = new Audio("/audio/soundWarning.mp3");
const audioAlertInfo = new Audio("/audio/soundAlertInfo.mp3");
const audioPopup = new Audio("/audio/soundPopup.mp3");
import * as operator from "./../constants/operator";
import { ref } from "vue";
import { Badge } from "@/components/ui";
import FormDialog from "@/components/FormDialog.vue";
import StepsDialog from "@/components/StepsDialog.vue";
import FilterHeader from "@/components/FilterHeader.vue";
import Button from "@/components/ui/button/Button.vue";
import PreviewPdfDialog from "@/components/PreviewPdfDialog.vue";
import DetailDialog from "@/components/DetailDialog.vue";
import ConfirmDialog from "@/components/ConfirmDialog.vue";
import ColumnEditor from "@/components/ColumnEditor.vue";
import ChecklistDialog from "@/components/ChecklistDialog.vue";
import EditorDialog from "@/components/EditorDialog.vue";
import * as helpers from "@/helpers/datautils";
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
import { Card, CardTitle, CardContent, CardHeader } from "@/components/ui/card";
export default {
    components: {
        Badge,
        FormDialog,
        StepsDialog,
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
        PaginationPrevious,
        ColumnEditor,
        ChecklistDialog,
        EditorDialog,
        Card,
        PreviewPdfDialog,
    },
    props: {
        title: {
            type: String,
            default: "title",
        },
        module: {
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
            selectAll: false,
            selectedData: [],
            selectedIndex: [],
            total: 0,
            rows: [],
            form: [],
            columns: [],
            properties: {},
            apiModule: "",
            detail_schema: [],
            allowCreate: false,
            allowDelete: false,
            allowEdit: false,
            allowDelete: false,
            showDialog: false,
            showEditorDialog: false,
            showConfirmDialog: false,
            showColumnEditor: false,
            showChecklistDialog: false,
            showStepsDialog: false,
            showPdfPreview: false,
            pdfUrl: "",
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
            contextMenuPosition: {
                x: 0,
                y: 0,
            },
            dropDownPosition: {
                x: 0,
                y: 0,
            },
            showDetail: false,
            selected: {},
            columnOptions: [],
            selectedContextMenu: null,
            confirmDialogLoading: false,
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
            this.loading = true;
            this.selectAll = false;
            this.selectedData = [];
            let params = { ...this.pagination };
            Object.entries(this.$route.query).forEach(([key, value]) => {
                params[key] = value;
            })
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
                params.q = this.searchQuery;
            }

            try {
                await this.$store
                    .dispatch(this.module + "/grid", params)
                    .then(({ data }) => {
                        data = data.data;
                        this.rows = data.rows;
                        this.columns = data.columns;
                        this.total = data.total;
                        this.properties = data.properties;
                        this.detail_schema = data.detail_schemes;
                        this.apiModule = data.module;
                    })
                    .catch((err) => {
                        if (err.response) {
                            if (err.response.status != 200) {
                                audioAlertError.play();
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
                this.columnOptions = action.options;
                this.allowCreate = this.properties.contextMenu.some(
                    (item) => item.name === "create"
                );
            } catch (err) {
                // alert(error)
                console.log(err)
            }
        },
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        },
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedData = this.filterData.map((dt) => dt.id);
                this.selectedIndex = this.filterData.map((dt, index) => index);
            } else {
                this.selectedData = [];
                this.selectedIndex = [];
            }
        },
        async tambahData() {
            this.loading = true;
            await this.$store
                .dispatch(this.module + "/form")
                .then(({ data }) => {
                    this.form = data.data;
                    this.selected = {};
                })
                .finally(() => {
                    audioPopup.play();
                    this.showDialog = true;
                    this.loading = false;
                    this.openDropdown = false;
                });
        },
        async editData() {
            this.loading = true;
            await this.$store
                .dispatch(this.module + "/edit", this.selected.encode_id)
                .then(({ data }) => {
                    this.form = data.data;
                    audioPopup.play();
                    this.showDialog = true;
                    this.loading = false;
                    this.openDropdown = false;
                    return data;
                })
                .catch((err) => {
                    // console.error("Edit request error:", err);
                    alert(err.response?.data?.message)
                    // kalau kamu ingin tampilkan pesan error:
                    // this.errorMessage = err.response?.data?.message || "Gagal mengambil data.";

                    // supaya error tetap bisa ditangkap oleh caller (kalau perlu)
                    throw err;
                })
                .finally(() => {
                    this.loading = false;
                    this.openDropdown = false;
                    this.selectedContextMenu = null;
                });
        },
        async hapusData() {
            audioWarning.play();
            this.openDropdown = false;
            this.$confirm({
                message: `Apakah anda yakin menghapus data ini?`,
                button: {
                    no: "Tidak",
                    yes: "Ya",
                },
                callback: async (confirm) => {
                    if (confirm) {
                        this.loading = true;
                        await this.$store
                            .dispatch(
                                this.module + "/delete",
                                this.selected.encode_id
                            )
                            .then(({ data }) => {
                                audioAlertInfo.play();
                                alert(data?.message);
                                this.load(); // Refresh the data table after deletion
                            })
                            .catch((resp) => {
                                audioAlertError.play();
                                alert(resp.response.data.message);
                            })
                            .finally((f) => {
                                this.openDropdown = null;
                                this.loading = false;
                            });
                    }
                },
            });
        },
        viewData() {
            if (this.selected.schema != undefined) {
                this.detail_schema = JSON.parse(this.selected.schema);
                this.selected = JSON.parse(this.selected.data);
            }
            audioPopup.play();
            this.showDetail = true;
            this.openDropdown = false;
        },
        returData() {
            alert("Action retur data:");
        },
        undoData() {
            alert("Action undo data:");
        },
        truncateData() {
            this.$confirm({
                message: `Apakah anda yakin menghapus semua data di modul ini?`,
                button: {
                    no: "Tidak",
                    yes: "Ya",
                },
                callback: async (confirm) => {
                    if (confirm) {
                        this.loading = true;
                        await this.$store
                            .dispatch(this.module + "/truncate")
                            .then(({ data }) => {
                                this.load();
                                alert(data.message);
                            })
                            .catch((resp) => {
                                alert(resp.response.data.message);
                            })
                            .finally((f) => {
                                this.openDropdown = null;
                                this.loading = false;
                            });
                    }
                },
            });
        },
        async handleSubmit(form) {
            audioWarning.play();
            // console.log('ctx: ', this.selectedContextMenu)
            this.$confirm({
                message: `Apakah anda yakin menyimpan data ini?`,
                button: {
                    no: "Tidak",
                    yes: "Ya",
                },
                callback: async (confirm) => {
                    if (confirm) {
                        this.loading = true;
                        let payload = form
                        if (
                            this.selectedContextMenu != null &&
                            this.selectedContextMenu.apiUrl != undefined
                        ) {
                            if(this.selectedContextMenu.asFormData){
                                payload = new FormData()
                                Object.entries(form).forEach(([key, value]) => {
                                    if (value !== undefined && value !== null) {
                                        payload.append(key, value)
                                    }
                                })

                                if(form.addtable.details != undefined && form.addtable.details.length > 0){
                                    const detailsWithoutFile = form.addtable.details.map((d, idx) => {
                                        const result = {}
                                        if (d.tipe) result.tipe = d.tipe
                                        if (d.show_on_mitra) result.show_on_mitra = d.show_on_mitra
                                        result.index = idx
                                        return result
                                    })

                                    payload.append('details', JSON.stringify(detailsWithoutFile))

                                    form.addtable.details.forEach((d, idx) => {
                                        if (d.file) payload.append(`file_${idx}`, d.file)
                                    })
                                }
                            }
                            
                            await axios
                                .post(this.selectedContextMenu.apiUrl, payload)
                                .then(({ data }) => {
                                    this.load();
                                    if (
                                        data.message != undefined &&
                                        data.status == true
                                    ) {
                                        audioAlertInfo.play();
                                        alert(data.message);
                                        this.showDialog = false;
                                        this.form = {};
                                        this.selected = {};
                                        this.selectedContextMenu = null;
                                    }
                                })
                                .catch((resp) => {
                                    let msgError = "";
                                    if (resp.response.data?.data != undefined) {
                                        const errors = Object.values(
                                            resp.response.data.data
                                        );
                                        msgError = errors[0];
                                    }
                                    audioAlertError.play();
                                    alert(
                                        resp.response.data.message +
                                        " " +
                                        msgError
                                    );
                                })
                                .finally(() => {
                                    this.loading = false;
                                    this.openDropdown = false;
                                });
                        } else {
                            // console.log('ctx not exists')
                            await this.$store
                                .dispatch(this.module + "/create", form)
                                .then(({ data }) => {
                                    this.load();
                                    if (
                                        data.message != undefined &&
                                        data.status == true
                                    ) {
                                        audioAlertInfo.play();
                                        alert(data.message);
                                        this.showDialog = false;
                                        this.form = {};
                                        this.selected = {};
                                    }
                                    this.selectedContextMenu = null;
                                })
                                .catch((resp) => {
                                    let msgError = "";
                                    if (resp.response.data?.data != undefined) {
                                        const errors = Object.values(
                                            resp.response.data.data
                                        );
                                        msgError = errors[0];
                                    }
                                    audioAlertError.play();
                                    alert(
                                        resp.response.data.message +
                                        " " +
                                        msgError
                                    );
                                })
                                .finally((f) => {
                                    this.openDropdown = null;
                                    this.loading = false;
                                });
                        }
                    }
                    // this.selectedContextMenu = null;
                },
            });
        },
        async handleSubmitEditor(payload){
            audioWarning.play();
            this.$confirm({
                message: `Apakah anda yakin menyimpan data ini?`,
                button: {
                    no: "Tidak",
                    yes: "Ya",
                },
                callback: async (confirm) => {
                    if (confirm) {
                        this.loading = true;

                        if (
                            this.selectedContextMenu != null &&
                            this.selectedContextMenu.apiUrl != undefined
                        ) {
                            const form = new FormData()
                            if (payload.id != undefined) form.append('id', payload.id)
                            Object.entries(payload).forEach(([key, value]) => {
                                if (value !== undefined && value !== null) {
                                    form.append(key, value)
                                }
                            })

                            if(payload.files != undefined){
                                Array.from(payload.files).forEach(file => {
                                    form.append('files[]', file)
                                })
                                delete payload.files
                            }

                            if(payload.addtable.details != undefined){
                                payload.addtable.details.forEach((d, idx) => {
                                    if (d.file) {
                                        form.append(`file_${idx}`, d.file)
                                    }
                                })
                            }
                            
                            await axios
                                .post(this.selectedContextMenu.apiUrl, form)
                                .then(({ data }) => {
                                    this.load();
                                    if (
                                        data.message != undefined &&
                                        data.status == true
                                    ) {
                                        audioAlertInfo.play();
                                        alert(data.message);
                                        // this.showEditorDialog = false
                                        // this.form = {};
                                        // this.selected = {};
                                        // this.selectedContextMenu = null;
                                    }
                                })
                                .catch((resp) => {
                                    let msgError = "";
                                    if (resp.response.data?.data != undefined) {
                                        const errors = Object.values(
                                            resp.response.data.data
                                        );
                                        msgError = errors[0];
                                    }
                                    audioAlertError.play();
                                    alert(
                                        resp.response.data.message +
                                        " " +
                                        msgError
                                    );
                                })
                                .finally(() => {
                                    this.loading = false;
                                    this.openDropdown = false;
                                });
                        }
                    }
                    // this.selectedContextMenu = null;
                },
            });
        },
        hapusDataMultiple() {
            audioWarning.play();
            // console.log(this.selectedData);
            alert("hapus data terpilih");
        },
        toggleDropdown(column, data, e, index) {
            this.columnOptions = column.options;
            if (this.selectAll == false) {
                this.selected = data;
                this.selectedIndex = [index];
                if (this.properties.multipleSelect) {
                    this.selectedData = [data.id];
                }
            }
            const sidebar = document.querySelector("aside");
            const toolbar = document.querySelector(".h-10"); // sesuai class toolbar kamu

            const sidebarWidth = sidebar ? sidebar.offsetWidth : 0;
            const toolbarHeight = toolbar ? toolbar.offsetHeight : 0;
            // Hitung posisi relatif ke area konten
            this.dropDownPosition.x = e.clientX - sidebarWidth + 10;
            this.dropDownPosition.y = e.clientY - toolbarHeight;

            this.openDropdown = true;
            this.openContextMenu = false;
        },
        closeContextMenu() {
            this.openContextMenu = false;
        },
        handleClickOutside(e) {
            if (!this.$el.contains(e.target)) this.openDropdown = null;
        },
        handleScroll(e) {
            let position = e.target.scrollTop;
            if (position != this.scrollPosition) this.openDropdown = null;
            this.scrollPosition = position;
        },
        handleRightClick(data, index, e) {
            if (this.selectAll == false && this.selectedIndex.length <= 1) {
                this.selected = data;
                this.selectedIndex = [index];
                if (this.properties.multipleSelect) {
                    this.selectedData = [data.id];
                }
            }
            // Dapatkan referensi sidebar & toolbar untuk perhitungan real
            const sidebar = document.querySelector("aside");
            const toolbar = document.querySelector(".h-10"); // sesuai class toolbar kamu

            const sidebarWidth = sidebar ? sidebar.offsetWidth : 0;
            const toolbarHeight = toolbar ? toolbar.offsetHeight : 0;
            // Hitung posisi relatif ke area konten
            this.contextMenuPosition.x = e.clientX - sidebarWidth;
            this.contextMenuPosition.y = e.clientY - toolbarHeight;

            this.openContextMenu = true;
            this.openDropdown = false;
        },
        handleClickRow(data, index, e) {
            if (e.target.type !== "checkbox") {
                this.selected = data;
                this.selectedIndex = [index];
                if (this.properties.multipleSelect) {
                    this.selectedData = [data.id];
                    this.selectAll = false;
                }
            }
        },
        handleCheckboxChange(index, e) {
            if (e.target.checked) {
                this.selectedIndex.push(index);
                this.selected = null;
            } else {
                this.selectedIndex = this.selectedIndex.filter(
                    (i) => i !== index
                );
            }
        },
        matchContextMenuConditions(conditions) {
            if (!conditions || Object.keys(conditions).length === 0)
                return true;
            return Object.entries(conditions).every(([key, value]) => {
                const selectedValue = this.selected[key];
                if (Array.isArray(value)) {
                    return value.includes(selectedValue);
                }
                return selectedValue === value;
            });
        },
        callByFunctionName(cm) {
            this[cm.onClick](cm);
        },
        test(cm) {
            alert(`Test, buat function sebenarnya di datatable component`);
        },
        async getFormDialog(cm) {
            // console.log({cm})
            if (cm.type == "form_dialog"){
                this.selectedContextMenu = cm;
                this.loading = true;
                let res = null

                let params = { id: this.selected.encode_id }
                cm.params.forEach((param) => {
                    params[param] = helpers.getSubObjectValue(this.selected,param) || this.selected[param]
                })
                try {
                    res = await axios.get(cm.formUrl, {
                        params,
                    })

                    audioPopup.play()
                    this.showDialog = true
                    this.form = res.data.data;
                } catch (err) {
                    audioAlertError.play();
                    alert(err?.response?.data?.message)
                } finally {
                    this.loading = false
                    this.openDropdown = false
                }
            }

        },
        async getEditorDialog(cm) {
            // console.log({cm})
            if (cm.type == "form_editor"){
                this.selectedContextMenu = cm;
                this.loading = true;
                let res = null

                let params = { id: this.selected.encode_id }
                cm.params.forEach((param) => {
                    params[param] = helpers.getSubObjectValue(this.selected,param) || this.selected[param]
                })
                try {
                    res = await axios.get(cm.formUrl, {
                        params,
                    })

                    audioPopup.play()
                    this.showEditorDialog = true
                    this.form = res.data.data;
                } catch (err) {
                    audioAlertError.play();
                    alert(err?.response?.data?.message)
                } finally {
                    this.loading = false
                    this.openDropdown = false
                }
            }

        },
        async getStepsDialog(cm) {
            if (cm.type !== "steps") return;
            this.selectedContextMenu = cm;
            this.loading = true;
            let res = null
            try {
                res = await axios.get(cm.formUrl, {
                    params: { id: this.selected.encode_id },
                })

                audioPopup.play()
                this.showStepsDialog = true
                this.form = res.data.data;
            } catch (err) {
                audioAlertError.play();
                alert(err?.response?.data?.message)
            } finally {
                this.loading = false
                this.openDropdown = false
            }

        },
        async confirmPopup(cm) {
            this.selectedContextMenu = cm

            if (cm.type === 'confirm') {
                this.showConfirmDialog = true
                audioPopup.play()
                return
            }

            if (cm.type === 'download_pdf') {
                try {
                    const response = await axios.post(
                        cm.apiUrl,
                        { id: this.selected.encode_id },
                        { responseType: 'blob' }
                    )

                    // ambil filename dari header
                    const disposition = response.headers['content-disposition']
                    let filename = 'download.pdf'

                    if (disposition) {
                        const utf8 = /filename\*=UTF-8''([^;]+)/i
                        const ascii = /filename="([^"]+)"/i

                        if (utf8.test(disposition)) {
                            filename = decodeURIComponent(utf8.exec(disposition)[1])
                        } else if (ascii.test(disposition)) {
                            filename = ascii.exec(disposition)[1]
                        }
                    }

                    // buat blob
                    const blob = new Blob([response.data], { type: 'application/pdf' })
                    const url = window.URL.createObjectURL(blob)

                    // trigger download
                    const link = document.createElement('a')
                    link.href = url
                    link.setAttribute('download', filename)
                    document.body.appendChild(link)
                    link.click()

                    // bersihin sampahnya
                    link.remove()
                    window.URL.revokeObjectURL(url)

                } catch (err) {
                    audioAlertError.play()
                    alert(err?.response?.data?.message || 'Gagal download PDF')
                }

                audioPopup.play()
            }
        },
        async redirect(cm) {
            let params = {}
            cm.params.forEach((param) => {
                params[param+"_is"] = helpers.getSubObjectValue(this.selected,param) || this.selected[param]
            })
            this.$router.push({
                path: cm.url,
                query: params,
            })
            
        },
        async downloadPdf(cm) {
            let params = {}
            cm.params.forEach((param) => {
                params[param] = helpers.getSubObjectValue(this.selected,param) || this.selected[param]
            })
            
            try {
                await axios.post(cm.apiUrl, params, {
                    responseType: 'blob'
                }).then((response) => {
                   const disposition = response.headers['content-disposition'];
                   if (disposition && disposition.includes('filename=')) {
                       this.pdfFilename = disposition
                            .split('filename=')[1]
                            .replace(/"/g, '');
                    }

                    const blob = new Blob([response.data], {
                        type: 'application/pdf'
                    });

                    this.pdfUrl = URL.createObjectURL(blob);
                    this.showPdfPreview = true;
                }).catch((resp) => {
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
                }).finally((f) => {
                    
                })
            } catch (err) {
                audioAlertError.play()
                alert(err?.response?.data?.message || 'Terjadi kesalahan')
            }
            
        },
        async handleSubmitConfirmDialog(form) {
            this.loading = true

            form.id = this.selected.encode_id 

            const formData = new FormData()

            Object.entries(form).forEach(([key, value]) => {
                if (value !== undefined && value !== null) {
                    formData.append(key, value)
                }
            })

            try {
                const { data } = await axios.post(this.selectedContextMenu.apiUrl, formData)

                if (data?.message && data.status === true) {
                    audioAlertInfo.play()
                    alert(data.message)
                }

            } catch (err) {
                audioAlertError.play()
                alert(err?.response?.data?.message || 'Terjadi kesalahan')

            } finally {
                this.loading = false
                this.openDropdown = false
                this.showConfirmDialog = false
                this.selectedContextMenu = null
                this.confirmDialogLoading = false
                this.load()
            }
        },
        async handleSubmitStepsDialog(form) {
            this.loading = true

            form.id = this.selected.encode_id

            const formData = new FormData()

            Object.entries(form).forEach(([key, value]) => {
                if (value !== undefined && value !== null) {
                    formData.append(key, value)
                }
            })

            try {
                const { data } = await axios.post(this.selectedContextMenu.apiUrl, formData)

                if (data?.message && data.status === true) {
                    audioAlertInfo.play()
                    alert(data.message)
                }

            } catch (err) {
                audioAlertError.play()
                alert(err?.response?.data?.message || 'Terjadi kesalahan')

            } finally {
                this.loading = false
                this.openDropdown = false
                this.showStepsDialog = false
                this.selectedContextMenu = null
                this.stepsDialogLoading = false
                this.load()
            }
        },
        async getChecklistDialog(cm) {
            if (cm.type !== "checklist_dialog") return;
            this.selectedContextMenu = cm;
            this.loading = true;
            const res = await axios
                .get(cm.formUrl, {
                    params: { id: this.selected.encode_id },
                })
                .catch((err) => console.error(err))
                .finally(() => {
                    audioPopup.play();
                    this.showChecklistDialog = true;
                    this.loading = false;
                    this.openDropdown = false;
                    this.selectedContextMenu = null;
                });
            this.form = res.data.data;
        },
        async handleSubmitChecklistDialog(form) {
            this.loading = true;
            form.id = this.selected.encode_id;
            await axios
                .post(this.selectedContextMenu.apiUrl, form)
                .then(({ data }) => {
                    if (data.message != undefined && data.status == true) {
                        audioAlertInfo.play();
                        alert(data.message);
                    }
                })
                .catch((resp) => {
                    audioAlertError.play();
                    alert(resp.response.data.message);
                })
                .finally(() => {
                    this.loading = false;
                    this.openDropdown = false;
                    this.showChecklistDialog = false;
                    this.selectedContextMenu = null;
                    this.confirmDialogLoading = false;
                    this.load();
                });
        },
        openColumnEditor(cm) {
            audioPopup.play();
            this.showColumnEditor = true;
        },
        reload(state) {
            if (state) {
                this.showColumnEditor = false;
                this.load();
            }
        },
        closePreview() {
            if (this.pdfUrl) {
                URL.revokeObjectURL(this.pdfUrl);
            }
            this.pdfUrl = null;
            this.showPdfPreview = false;
        }
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
