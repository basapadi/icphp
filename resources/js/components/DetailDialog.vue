<template>
    <!-- Overlay -->
    <div v-if="open" class="h-screen fixed inset-0 flex items-center justify-center bg-black/50 z-50" @click="close()">
        <div class="dialog-scale-animation" :data-state="dialogState" @click.stop>
            <Card class="w-auto max-w-[90%] min-w-[50vw] max-h-[80vh] mx-auto">
                <CardHeader>
                    <CardTitle>
                        <span class="text-foreground uppercase tracking-wider">
                            {{ getTitle() }}
                        </span>
                    </CardTitle>
                </CardHeader>
                <CardContent class="overflow-y-auto">
                    <div v-for="(sub, i) in schema" :key="i">
                        <template v-if="i == 'main' && sub.type == 'object'">
                            <div
                                class="flex gap-4 bg-card shadow-sm rounded-md py-2 px-2 mb-2 shadow-sm rounded-sm border border-dashed border-border">
                                <div class="w-[85%] overflow-x-auto">
                                    <table class="w-full border-dashed border-border rounded-lg overflow-hidden">
                                        <tbody>
                                            <tr class="border border-dashed border-border odd:bg-muted even:bg-card"
                                                v-for="(f, k) in sub.fields" :key="k">
                                                <td class="px-4 py-1 border border-dashed border-border text-muted-foreground font-sans text-sm"
                                                    style="width: 250px">
                                                    {{ f?.label }}
                                                </td>
                                                <td :class="`px-4 py-1 text-foreground text-sm text-${data['color_' + k]
                                                    } ${f?.class}`">
                                                    <div v-if="f.type != undefined && f.type == 'file' && $helpers.getSubObjectValue(data, k) != null">
                                                        <button @click="viewFile($helpers.getSubObjectValue(data, k))"
                                                            class="inline-flex items-center justify-center
                                                                p-1 rounded hover:bg-blue-200
                                                                text-blue-700
                                                                transition">
                                                            <Eye class="w-4 h-4 text-blue-600 mr-1" /> Lihat Dokumen
                                                        </button>
                                                    </div>
                                                    <div v-else-if="f.type != undefined && f.type == 'json'">
                                                        <pre :class="`overflow-auto whitespace-pre-wrap ${f?.class}`">{{ formatJson($helpers.getSubObjectValue(data, k)) }}</pre>
                                                    </div>
                                                    <div v-else v-html="$helpers.getSubObjectValue(data, k)"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="w-[15%] flex items-top justify-center">
                                    <div v-if="data.qrcode" class="flex flex-col items-center">

                                        <div id="qrcode-wrapper">
                                            <qrcode-canvas :value="data.qrcode" :size="200" level="H" />
                                        </div>

                                        <button @click="downloadQR(data.kode)"
                                            class="mt-3 px-3 py-1 bg-blue-600 text-white rounded">
                                            Download QR
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-else-if="sub.type == 'array'">
                            <div
                                class="bg-card shadow-sm rounded-md py-2 px-2 mb-2 border-1 shadow-sm rounded-sm border-border border-dashed">
                                <div class="py-2 px-5">
                                    <span class="text-muted-foreground uppercase tracking-wider">{{ sub.title }}</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table
                                        class="w-full rounded-md overflow-hidden border border-1 border-dashed border-border">
                                        <thead
                                            class="bg-primary/10 text-foreground border border-1 border-dashed border-border">
                                            <tr class="border border-1 border-dashed border-border">
                                                <th v-for="(f, k) in sub.fields"
                                                    class="px-4 py-2 text-left text-sm border-b border-border" :key="k"
                                                    :class="f?.class" :style="f?.style">
                                                    {{ f?.label }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            <template v-if="data[i] != undefined && data[i].length > 0">
                                                <tr v-for="(row, r) in data[i]"
                                                    class="hover:bg-accent border border-1 border-dashed border-border odd:bg-muted/20 even:bg-background/30"
                                                    :key="r">
                                                    <td class="px-4 py-1 text-muted-foreground text-sm border border-1 border-dashed border-border"
                                                        v-for="(col, c) in sub.fields" :key="c" :class="col?.class"
                                                        :style="col?.styles">
                                                        <span v-if="c == 'no'">
                                                            {{ r + 1 }}
                                                        </span>
                                                        <span v-else>
                                                            <div v-if="col.type != undefined && col.type == 'file'">
                                                                <button
                                                                    v-if="$helpers.getSubObjectValue(row, c) != undefined"
                                                                    @click="viewFile($helpers.getSubObjectValue(row, c))"
                                                                    class="inline-flex items-center justify-center
                                                                        p-1 rounded hover:bg-blue-200
                                                                        text-blue-700
                                                                        transition">
                                                                    <Eye class="w-4 h-4 text-blue-600 mr-1" /> Lihat Dokumen
                                                                </button>
                                                            </div>
                                                            <div v-else-if="col.type != undefined && col.type == 'json'">
                                                                <button
                                                                    v-if="$helpers.getSubObjectValue(row, c) != undefined"
                                                                    @click="viewJson($helpers.getSubObjectValue(row, c))"
                                                                    class="inline-flex items-center justify-center rounded hover:bg-blue-200 text-blue-700 transition">
                                                                    <Eye class="w-4 h-4 text-blue-600 mr-1" /> Lihat Data
                                                                </button>
                                                            </div>
                                                            <div v-else class="max-h-36 overflow-y-auto">{{ $helpers.getSubObjectValue(row, c) }}</div>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </template>
                                            <template v-else-if="data[i.split('__')[0]] != undefined && data[i.split('__')[0]][i.split('__')[1]] != undefined && data[i.split('__')[0]][i.split('__')[1]].length > 0">
                                                <tr v-for="(row, r) in data[i.split('__')[0]][i.split('__')[1]]"
                                                    class="hover:bg-accent border border-1 border-dashed border-border odd:bg-muted/20 even:bg-background/30"
                                                    :key="r">
                                                    <td class="px-4 py-1 text-muted-foreground text-sm border border-1 border-dashed border-border"
                                                        v-for="(col, c) in sub.fields" :key="c" :class="col?.class"
                                                        :style="col?.styles">
                                                        <span v-if="c == 'no'">
                                                            {{ r + 1 }}
                                                        </span>
                                                        <span v-else>
                                                            <div v-if="col.type != undefined && col.type == 'file'">
                                                                <button v-if="$helpers.getSubObjectValue(row, c) != undefined"
                                                                    @click="viewFile($helpers.getSubObjectValue(row, c))"
                                                                    class="inline-flex items-center justify-center
                                                                        p-1 rounded hover:bg-blue-200
                                                                        text-blue-700
                                                                        transition">
                                                                    <Eye class="w-4 h-4 text-blue-600 mr-1" /> Lihat Dokumen
                                                                </button>
                                                            </div>
                                                            <div v-else-if="col.type != undefined && col.type == 'json'">
                                                                <button
                                                                    v-if="$helpers.getSubObjectValue(row, c) != undefined"
                                                                    @click="viewJson($helpers.getSubObjectValue(row, c))"
                                                                    class="inline-flex items-center justify-center rounded hover:bg-blue-200 text-blue-700 transition">
                                                                    <Eye class="w-4 h-4 text-blue-600 mr-1" /> Lihat Data
                                                                </button>
                                                            </div>
                                                            <div v-else class="max-h-36 overflow-y-auto">{{ $helpers.getSubObjectValue(row, c) }}
                                                            </div>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </template>
                                            <template v-else>
                                                <tr>
                                                    <td colspan="12" class="text-center">
                                                        Tidak ada data
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div
                                class="bg-card shadow-sm rounded-md py-2 px-2 mb-2 border-1 shadow-sm rounded-sm border-border border-dashed">
                                <div class="py-2 px-5">
                                    <span class="text-muted-foreground uppercase tracking-wider">{{ sub.title }}</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full border border-border rounded-lg overflow-hidden">
                                        <tbody>
                                            <tr class="border border-1 border-dashed border-border odd:bg-muted/20 even:bg-background/30"
                                                v-for="(f, k) in sub.fields" :key="k">
                                                <td class="px-4 py-1 border-r text-muted-foreground text-sm"
                                                    style="width: 250px">
                                                    {{ f?.label }}
                                                </td>
                                                <td :class="`px-4 py-1 text-muted-foreground text-sm text-${data['color_' + k]
                                                    } ${f?.class}`">
                                                    <div
                                                        v-if="f.type != undefined && f.type == 'file' && $helpers.getSubObjectValue(data, k) != ''">
                                                        <button 
                                                            v-if="$helpers.getSubObjectValue(data, k) != undefined && $helpers.getSubObjectValue(data, k) != null"
                                                            @click="viewFile($helpers.getSubObjectValue(data, k))"
                                                            class="inline-flex items-center justify-center
                                                                p-1 rounded hover:bg-blue-200
                                                                text-blue-700
                                                                transition">
                                                            <Eye class="w-4 h-4 text-blue-600 mr-1" /> Lihat Dokumen
                                                        </button>
                                                    </div>
                                                    <div v-else-if="f.type != undefined && f.type == 'tree'">
                                                        <CriteriaTree :items="data[k]" />
                                                    </div>
                                                    <div v-else v-html="$helpers.getSubObjectValue(data, k)"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>
                    </div>
                </CardContent>
                <CardFooter class="flex justify-end px-6">
                    <Button variant="outline" @click="close">Tutup</Button>
                </CardFooter>
            </Card>
        </div>
    </div>
    <div v-if="showDialog" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <!-- Dialog -->
        <div class="bg-white rounded-lg shadow-xl w-full max-w-7xl p-4">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h2 class="text-lg font-semibold">Previu Dokumen</h2>
                <button @click="closeDialog" class="text-gray-600 hover:text-gray-900">
                    ✕
                </button>
            </div>

            <!-- Konten file -->
            <div class="w-full">
                <iframe v-if="fileUrl" :src="fileUrl" class="w-full h-[70vh] border"></iframe>
            </div>
        </div>
    </div>
     <div v-if="showJsonDialog" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <!-- Dialog -->
        <div class="bg-white rounded-lg shadow-xl w-[500px] max-w-[500px] p-4">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h2 class="text-lg font-semibold">Previu Data</h2>
                <button @click="closeJsonDialog" class="text-gray-600 hover:text-gray-900">
                    ✕
                </button>
            </div>

            <!-- Konten file -->
            <div class="w-full" style="max-height: 50vh; overflow-y: auto;">
                <pre :class="`overflow-auto text-xs whitespace-pre-wrap`">{{ formatJson(jsonData) }}</pre>
            </div>
        </div>
    </div>
</template>
<script>
import {
    Card,
    CardTitle,
    CardContent,
    CardHeader,
    CardFooter,
} from "@/components/ui/card";
import { Eye } from 'lucide-vue-next'
import { Button } from "@/components/ui/button";
import { QrcodeCanvas } from 'qrcode.vue'
import DraggableCriteria from "./ui/form/DraggableCriteria.vue";
import CriteriaTree from "./CriteriaTree.vue";

export default {
    name: "DetailDialog",
    props: {
        open: Boolean,
        title: {
            type: String,
            default: () => {
                "";
            },
        },
        data: { type: Object, default: () => ({}) },
        schema: { type: Object, defautl: () => ({}) },
    },
    components: {
        Card,
        CardTitle,
        CardContent,
        CardHeader,
        CardFooter,
        Button,
        Eye,
        QrcodeCanvas,
        DraggableCriteria,
        CriteriaTree,
    },
    data() {
        return {
            form: {},
            isClosing: false,
            showDialog: false,
            showJsonDialog: false,
            jsonData: {}
        };
    },
    methods: {
        load() { },
        viewJson(data) {
            this.jsonData = data;
            this.showJsonDialog = true;
        },
        closeJsonDialog() {
            this.showJsonDialog = false;
        },
        formatJson(value) {
            try {
                if (typeof value === 'object') {
                    return JSON.stringify(value, null, 2)
                }
                return JSON.stringify(JSON.parse(value), null, 2)
            } catch (e) {
                return value
            }
        },
        close() {
            this.isClosing = true;
            setTimeout(() => {
                this.$emit("close");
                this.isClosing = false;
            }, 250);
        },
        submit() {
            this.close();
        },
        getTitle() {
            if (!this.schema) return this.title;

            const firstKey = Object.keys(this.schema)[0];
            if (firstKey && this.schema[firstKey]?.title) {
                return this.schema[firstKey].title;
            }

            return this.title;
        },
        async viewFile(url) {
            try {
                const resp = await axios.get(url, {
                    responseType: 'blob'
                });

                const contentType = resp.headers['content-type'];
                // Bikin blob dari response
                const fileBlob = new Blob([resp.data], { type: contentType });

                // Buat URL sementara
                const fileUrl = URL.createObjectURL(fileBlob);

                if (contentType === 'application/pdf') {
                    this.fileUrl = fileUrl
                    this.showDialog = true
                } else if (contentType.startsWith('image/')) {
                    this.fileUrl = fileUrl
                    this.showDialog = true
                } else {
                    // File lainnya
                    const a = document.createElement('a');
                    a.href = fileUrl;
                    a.download = 'file';
                    a.click();
                }

            } catch (err) {
                if (err.response && err.response.data instanceof Blob) {
                    const blob = err.response.data;
                    const text = await blob.text();
                    let json;
                    try {
                        json = JSON.parse(text);
                    } catch {
                        alert(text)
                        return;
                    }
                    alert(json.message || json.error || json)
                    return;
                }
            }
        },
        openDialog(url) {
            this.fileUrl = url;
            this.showDialog = true;
        },
        closeDialog() {
            this.showDialog = false;
            this.fileUrl = null;
        },
        downloadQR(kode) {
            const wrapper = document.getElementById("qrcode-wrapper");
            if (!wrapper) return;

            const originalCanvas = wrapper.querySelector("canvas");
            if (!originalCanvas) return;

            const w = originalCanvas.width;
            const h = originalCanvas.height;

            const padding = 20;
            const text = kode || "";
            const textSize = 32; // ukuran font px
            const textMarginTop = 20; // jarak QR ke teks

            // tinggi total: QR + padding + teks
            const totalHeight = h + padding * 2 + textMarginTop + textSize;

            const canvas = document.createElement("canvas");
            canvas.width = w + padding * 2;
            canvas.height = totalHeight;

            const ctx = canvas.getContext("2d");

            // background putih
            ctx.fillStyle = "#ffffff";
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // gambar QR
            ctx.drawImage(originalCanvas, padding, padding);

            // gambar teks kode aset di bawahnya
            ctx.fillStyle = "#000000";
            ctx.font = `${textSize}px sans-serif`;
            ctx.textAlign = "center";

            const textX = canvas.width / 2;
            const textY = padding + h + textMarginTop + textSize;

            ctx.fillText(text, textX, textY);

            // export PNG
            const url = canvas.toDataURL("image/png");

            const link = document.createElement("a");
            link.href = url;
            link.download = kode + ".png";
            link.click();
        }
    },
    computed: {
        dialogState() {
            return this.open && !this.isClosing ? "open" : "closed";
        },
    },
};
</script>
