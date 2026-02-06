<template>
    <div class="relative rounded-lg shadow-sm border border-border z-1">
        <div :class="!popup ? 'h-screen' : 'h-110'">
            <div class="p-2">
                <div class="px-1">
                    <div class="flex-1 w-full">
                        <form
                            @submit.prevent="save"
                            enctype="multipart/form-data"
                        >
                            <div class="space-y-4 grid grid-cols-3 gap-2">
                                <div
                                    class="flex flex-col md:flex-row md:items-center gap-2"
                                >
                                    <Input
                                        key="name"
                                        label="Nama Laporan"
                                        v-model="form.name"
                                        name="name"
                                        id="name"
                                        hint="Masukkan nama laporan"
                                        :required="true"
                                        @input="sanitizeInput"
                                        :readonly="popup"
                                    />
                                </div>
                            </div>
                            <div class="space-y-2 grid grid-cols-4 gap-2">
                                <div class="pt-2 col-span-3">
                                    <label
                                        class="flex items-center gap-1 text-sm font-medium leading-none"
                                        ><span
                                            class="text-muted-foreground text-shadow-2xs"
                                            >Query</span
                                        >
                                        <span class="text-xs text-destructive"
                                            >filter data berdasarkan '<b
                                                >deleted_at = null</b
                                            >' apabila skema memiliki attribut
                                            tersebut</span
                                        ></label
                                    >
                                    <div
                                        class="flex overflow-x-auto shadow-md rounded-md flex-wrap gap-2 mb-1 border border-dashed p-1 my-2"
                                        style="
                                            height: 300px;
                                            border: 1px solid #ddd;
                                            background-color: #282c34;
                                            color: #ffffff;
                                        "
                                    >
                                        <SqlEditor
                                            v-model="form.query"
                                            :schemas="schemas"
                                        />
                                    </div>
                                </div>
                                <div class="pt-2">
                                    <div class="flex gap-2 items-end">
                                        <Input
                                            key="search"
                                            label="Skema Tabel"
                                            v-model="search"
                                            name="search"
                                            id="search"
                                            hint="Cari dan gunakan skema tabel dibawah ini untuk membangun query"
                                        />
                                        <Button type="button" @click="load(true)" class="text-xs h-8">
                                            <RefreshCcw class="w-4 h-4" />
                                        </Button>
                                    </div>

                                    <div
                                        class="flex overflow-x-auto h-65 flex-wrap gap-2 mb-1 border rounded-md p-1 my-1"
                                    >
                                        <div class="p-4">
                                            <ul class="list-decimal pl-4 space-y-1" v-if="Object.keys(schemas).length > 0">
                                                <li
                                                    v-for="(
                                                        columns, tableName
                                                    ) in filteredSchemas"
                                                    :key="tableName"
                                                >
                                                    <div
                                                        class="font-bold text-foreground text-sm"
                                                    >
                                                        {{ tableName }}
                                                    </div>
                                                    <ul
                                                        class="list-disc pl-2 mt-1 space-y-1 text-xs text-foreground divide-y divide-border ml-4 border-border"
                                                    >
                                                        <li
                                                            v-for="col in columns"
                                                            :key="col.name"
                                                        >
                                                            <span
                                                                class="font-medium pr-2"
                                                                >{{
                                                                    col.name
                                                                }}</span
                                                            >
                                                            <span
                                                                class="text-muted-foreground"
                                                                >({{
                                                                    col.type
                                                                }})</span
                                                            >
                                                            <span
                                                                v-if="
                                                                    col.nullable
                                                                "
                                                                class="ml-1 text-green-600 font-semibold"
                                                                >null</span
                                                            >
                                                            <span
                                                                v-else
                                                                class="ml-1 text-red-600 font-semibold"
                                                                >not null</span
                                                            >
                                                            <span
                                                                v-if="
                                                                    col.default
                                                                "
                                                                class="ml-2 text-blue-600"
                                                                >default:
                                                                {{
                                                                    col.default
                                                                }}</span
                                                            >
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                            <p v-else class="text-muted-foreground text-sm">Skema sedang dimuat, silahkan tunggu...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2 grid grid-cols-1 gap-2">
                                <div class="flex justify-start gap-2">
                                    <div class="flex gap-2">
                                        <button
                                            v-if="!popup"
                                            type="button"
                                            @click="tryQuery()"
                                            class="px-4 py-1 text-sm rounded bg-green-700 hover:bg-green-800 shadow-xs"
                                        >
                                            <LoaderCircle
                                                v-if="exeloading"
                                                class="animate-spin"
                                            />
                                            <span v-else
                                                ><Play
                                                    class="w-4 h-4 text-white"
                                            /></span>
                                        </button>

                                        <button
                                            type="submit"
                                            class="px-4 py-1 text-sm rounded shadow-xs bg-primary text-primary-foreground hover:bg-primary/90"
                                        >
                                            <LoaderCircle
                                                v-if="saveloading"
                                                class="animate-spin"
                                            />
                                            <span v-else
                                                ><Save
                                                    class="w-4 h-4 text-primary-foreground"
                                                />
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div
                            class="space-y-4 grid grid-cols-1 gap-2"
                            v-if="popup == false"
                        >
                            <div class="pt-2">
                                <div
                                    class="flex bg-muted overflow-x-auto gap-2 mb-1 border rounded-md border-dashed p-1 h-90"
                                    style="border: 1px solid #ddd"
                                >
                                    <PreviewDataTable
                                        title="Preview"
                                        :query="query"
                                        @loading="setloading"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters } from "vuex";
import { ref } from "vue";
import { Badge } from "@/components/ui";
import FilterHeader from "@/components/FilterHeader.vue";
import Button from "@/components/ui/button/Button.vue";
import {RefreshCcw} from "lucide-vue-next";
import {
    Input,
    Select,
    Radio,
    FileUpload,
    Textarea,
    Number,
    Phone,
    Password,
    Date,
} from "@/components/ui/form";
import SqlEditor from "@/components/SqlEditor.vue";
import PreviewDataTable from "@/components/PreviewDataTable.vue";

export default {
    props: {
        popup: { type: Boolean, default: false },
        existQuery: { type: Object, default: {} },
    },
    components: {
        Badge,
        FilterHeader,
        Button,
        Input,
        Select,
        Radio,
        FileUpload,
        Textarea,
        Number,
        Phone,
        Password,
        Date,
        SqlEditor,
        PreviewDataTable,
        RefreshCcw,
    },
    computed: {
        filteredSchemas() {
            if (!this.search) return this.schemas;

            return Object.fromEntries(
                Object.entries(this.schemas).filter(([tableName]) =>
                    tableName.toLowerCase().includes(this.search.toLowerCase())
                )
            );
        },
    },
    data() {
        return {
            search: "",
            loading: false,
            exeloading: false,
            saveloading: false,
            schemas: ref([]),
            form: {
                name: "",
                query: `-- contoh query, lihat skema disamping untuk mendapat referensi kolom
  select * from vendors`,
            },
            query: [],
        };
    },
    methods: {
        async load(forceRefresh = false) {
            this.loading = true;
            this.schemas = [];
            const cacheKey = "report_schema_cache";
            if (!forceRefresh) {
                const cached = localStorage.getItem(cacheKey);
                if (cached) {
                    try {
                        this.schemas = JSON.parse(cached);
                        this.loading = false;
                        return;
                    } catch (e) {
                        // cache rusak, lanjut fetch
                        localStorage.removeItem(cacheKey);
                    }
                }
            }
            try {
               const { data } = await this.$store.dispatch("report/getSchema", {});
                const schemas = data.data;

                this.schemas = schemas;

                // 3. Simpan ke localStorage
                localStorage.setItem(cacheKey, JSON.stringify(schemas));
            } catch (err) {
                if (err.response && err.response.status !== 200) {
                    alert(err.response.data?.message);
                }
            } finally {
                this.loading = false;
            }
        },
        setloading(dt) {
            this.exeloading = dt;
        },
        tryQuery() {
            this.query = {
                name: "",
                query: this.form.query,
            };
        },
        save() {
            this.$confirm({
                message: `Apakah anda yakin menyimpan query laporan ini?`,
                button: {
                    no: "Tidak",
                    yes: "Ya",
                },
                callback: async (confirm) => {
                    if (confirm) {
                        this.saveloading = true;
                        await this.$store
                            .dispatch("report/saveQuery", this.form)
                            .then(({ data }) => {
                                this.form = {};
                                this.$emit("open", false);
                                alert("Query laporan berhasil disimpan");
                            })
                            .catch((resp) => {
                                alert(resp.response?.data?.message);
                            })
                            .finally((f) => {
                                this.saveloading = false;
                            });
                    }
                },
            });
        },
        sanitizeInput(event) {
            event.target.value = event.target.value.replace(
                /[^a-zA-Z0-9_-]/g,
                ""
            );
            this.form.name = event.target.value;
        },
    },
    mounted() {
        if (this.existQuery?.query != "") this.form = this.existQuery;
        this.load();
    },
};
</script>
