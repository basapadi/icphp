<template>
    <div class="flex flex-col gap-1.5">
        <Label
            :for="id"
            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
        >
            <span class="text-muted-foreground text-shadow-2xs">{{
                label
            }}</span>
            <span v-if="required" class="text-destructive"> *</span>
        </Label>

        <!-- Label custom -->
        <div class="flex items-center gap-2">
            <label
                @click.stop="triggerFileInput"
                class="cursor-pointer px-4 py-1 bg-primary text-sm text-primary-foreground rounded-md hover:bg-primary/90 transition"
            >
                Pilih File
            </label>

            <!-- Teks file terpilih -->
            <span
                class="text-sm text-muted-foreground italic truncate max-w-[200px]"
            >
                {{ fileLabel }}
            </span>
        </div>

        <ul v-if="hint" class="text-xs text-muted-foreground italic">
            <li>- Ekstensi : {{ extension }}</li>
        </ul>

        <!-- Input file disembunyikan -->
        <input
            ref="fileInput"
            type="file"
            :id="id"
            :name="name"
            :required="required"
            :accept="extension"
            :multiple="multiple"
            class="input-hidden"
            :disabled="disabled"
            :readonly="readonly"
            @change="onFileChange"
            @invalid="
                (e) =>
                    e.target.setCustomValidity(
                        `File ${label} tidak boleh kosong`
                    )
            "
            @input="(e) => e.target.setCustomValidity('')"
        />
       <!-- single -->
        <div v-if="typeof preview === 'string'">
            <img
                :src="preview"
                alt="Thumbnail"
                class="rounded-md max-w-20 h-auto object-contain"
                @click="viewFile(modelValue)"
                style="cursor: pointer"
            />
        </div>

        <!-- multiple -->
        <div
            v-else-if="Array.isArray(preview)"
            class="flex gap-2 flex-wrap"
        >
            <div v-for="(file, i) in preview" :key="i">
                <img
                    :src="file"
                    alt="Thumbnail"
                    class="rounded-md max-w-20 h-auto object-contain"
                    @click="viewFile(modelValue[i])"
                    style="cursor: pointer"
                />
            </div>
        </div>
    </div>
    <div v-if="showDialog" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <!-- Dialog -->
        <div class="bg-white rounded-lg shadow-xl w-full max-w-7xl p-4">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h2 class="text-lg font-semibold">Previu</h2>
                <button type="button" @click="closeDialog" class="text-gray-600 hover:text-gray-900">
                    ✕
                </button>
            </div>

            <!-- Konten file -->
            <div class="w-full">
                <iframe v-if="fileUrl" :src="fileUrl" class="w-full h-[70vh] border"></iframe>
            </div>
        </div>
    </div>
</template>
<style>
.input-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}
</style>
<script>
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";
import pdfIcon from "@/assets/pdficon.png";

export default {
    name: "FileUpload",
    components: { Label },
    props: {
        modelValue: { type: [File, Array, String], default: null },
        id: { type: String, default: "" },
        name: { type: String, default: "" },
        label: { type: String, default: "" },
        hint: { type: String, default: "" },
        class: { type: String, default: "" },
        required: { type: Boolean, default: false },
        extension: { type: String, default: "image/*" },
        multiple: { type: Boolean, default: false },
        maxsize: { type: Number, default: 0 },
        maxfile: { type: Number, default: 0 },
        disabled: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
    },
    data() {
        return {
            fileLabel: "Belum ada file",
            preview: null,
            pdfIcon: pdfIcon,
            showDialog: false,
            fileUrl: null
        };
    },
    methods: {
        triggerFileInput() {
            this.$refs.fileInput.click();
        },
        closeDialog() {
            this.showDialog = false;
            this.fileUrl = null;
        },
        onFileChange(e) {
            const files = e.target.files;

            if (this.multiple) {
                const fileArray = Array.from(files).filter(f => f instanceof File)

                if (fileArray.length) {
                    this.$emit("update:modelValue", fileArray)

                    // bikin preview array
                    this.preview = fileArray.map(file =>
                        this.getPreviewSrc(file)
                    )
                } else {
                    this.preview = []
                }

                this.fileLabel = fileArray.length
                    ? fileArray.map(f => f.name).join(", ")
                    : "Belum ada file"

            } else {
                const uploadedFile = files[0]

                if (uploadedFile instanceof File) {
                    this.$emit("update:modelValue", uploadedFile)

                    // preview single
                    this.preview = this.getPreviewSrc(uploadedFile)
                } else {
                    this.preview = null
                }

                this.fileLabel = uploadedFile
                    ? uploadedFile.name
                    : "Belum ada file"
            }

        },
        load() {
            if (!this.modelValue) {
                this.preview = null
                this.fileLabel = "Belum ada file"
                return
            }

            // single string
            if (typeof this.modelValue === "string") {
                this.preview = this.getPreviewSrc(this.modelValue)

                const name = this.modelValue.split("/").pop()
                this.fileLabel = name

                return
            }

            // array string
            if (Array.isArray(this.modelValue)) {
                this.preview = this.modelValue.map(v => this.getPreviewSrc(v))

                this.fileLabel = this.modelValue
            .map(v => v.split("/").pop())
            .join(", ")

                return
            }
        },  

        getPreviewSrc(item) {
            if (item instanceof File) {
                if (item.type.startsWith("image/")) {
                    return URL.createObjectURL(item)
                }
                return this.pdfIcon
            }

            if (typeof item === "string") {
                const lower = item.toLowerCase()

                if (lower.includes(".pdf")) {
                    return this.pdfIcon
                }

                return item
            }

            return null
        },

        async viewFile(fileOrUrl) {
            try {
                // ===== FILE BARU (upload lokal)
                if (fileOrUrl instanceof File) {
                    const fileUrl = URL.createObjectURL(fileOrUrl)

                    this.fileUrl = fileUrl
                    this.showDialog = true
                    return
                }

                // ===== URL BACKEND
                const resp = await axios.get(fileOrUrl, {
                    responseType: 'blob'
                })

                const contentType = resp.headers['content-type']
                const fileBlob = new Blob([resp.data], { type: contentType })
                const fileUrl = URL.createObjectURL(fileBlob)

                this.fileUrl = fileUrl
                this.showDialog = true

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

    },
    watch: {
        modelValue(val) {
            if (val instanceof File) return

            // Array File (multiple upload), jangan load ulang
            if (Array.isArray(val) && val.every(v => v instanceof File)) return

            this.load()
        },
    },
    mounted() {
        this.load()
    },
    beforeUnmount() {
        if (typeof this.preview === "string" && this.preview.startsWith("blob:")) {
            URL.revokeObjectURL(this.preview)
        }
    }
};
</script>
