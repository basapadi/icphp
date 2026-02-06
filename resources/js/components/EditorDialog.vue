<template>
    <!-- Overlay dengan transition -->
    <div v-if="open" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
        <!-- Container dialog dengan animasi -->
        <div class="dialog-scale-animation" :data-state="dialogState" @click.stop>
            <Card class="w-auto max-w-[95%] min-w-[70vw] mx-4">
                <CardHeader>
                    <CardTitle>
                        <span class="text-muted-foreground font-bold">
                            <SquareChevronRight class="h-5 w-5 mr-2 mb-1 text-primary inline-block align-middle" />
                            Dokumen Editor
                        </span>
                    </CardTitle>
                </CardHeader>
                <CardContent class="max-h-[90vh]">
                    <form @submit="submit" enctype="multipart/form-data">
                        <div class="h-auto max-h-[90%] max-w-[100%]" v-if="sections && Object.keys(sections).length">
                            <template v-for="(section, sectionKey) in sections" :key="sectionKey">
                                <div class="space-y-2 max-h-[90vh] gap-2 border-1 border-dashed rounded-sm mb-4">
                                    <div class="space-y-2 p-4" v-if="section['type'] == 'addtable'">
                                        <template v-if="
                                            form.addtable[sectionKey] !=
                                            undefined &&
                                            form.addtable[sectionKey]
                                                .length > 0
                                        ">
                                            <div v-for="(row, rowIndex) in form
                                                .addtable[sectionKey]" :key="rowIndex"
                                                class="flex border-1 shadow-sm rounded-sm border-border border-dashed py-4 px-2 gap-1">
                                                <span class="pl-2 pr-4 font-bold text-primary">{{ rowIndex + 1 }}</span>
                                                <div :class="`flex flex-1 w-full ${field.class}`" v-for="(
field, x
                                                    ) in section['forms']" :key="x">
                                                    <Input v-if="
                                                        [
                                                            'text',
                                                            'email',
                                                            'hidden'
                                                        ].includes(
                                                            field.type
                                                        )
                                                    " :key="field.name" :label="field.label" :type="field.type"
                                                        v-model="row[field.name]
                                                            " :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`" :hint="field.hint" :required="field.required
                                                            " :format="field?.format" :disabled="field.disabled
                                                                " :readonly="field.readonly
                                                                    " :modelValue="field.value
                                                                        " />
                                                    <Password v-if="
                                                        field.type ==
                                                        'password'
                                                    " :key="field.name" :label="field.label" :type="field.type"
                                                        v-model="row[field.name]
                                                            " :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`" :hint="field.hint" :required="field.required
                                                            " :format="field?.format" :disabled="field.disabled
                                                                " :readonly="field.readonly
                                                                    " />
                                                    <Number v-if="
                                                        field.type ==
                                                        'number'
                                                    " :key="field.name" :label="field.label" :type="field.type"
                                                        v-model="row[field.name]
                                                            " :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`" :hint="field.hint" :required="field.required
                                                            " :min="field?.min" :max="field?.max" :step="field?.step"
                                                        :format="field?.format" :disabled="field.disabled
                                                            " :readonly="field.readonly
                                                                " />
                                                    <Phone v-if="
                                                        field.type ==
                                                        'phone'
                                                    " :key="field.name" :label="field.label" :type="field.type"
                                                        v-model="row[field.name]
                                                            " :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`" :hint="field.hint" :required="field.required
                                                            " :min="field?.min" :max="field?.max" :step="field?.step"
                                                        :format="field?.format" :disabled="field.disabled
                                                            " :readonly="field.readonly
                                                                " />
                                                    <Select v-if="
                                                        field.type ==
                                                        'select'
                                                    " :key="field.name" :label="field.label" :type="field.type"
                                                        v-model="row[field.name]
                                                            " :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`" :hint="field.hint" :required="field.required
                                                            " :options="field.options" :disabled="field.disabled
                                                                " :readonly="field.readonly
                                                                    " :multiple="field.multiple
                                                                        " />
                                                    <Radio v-if="
                                                        field.type ==
                                                        'radio'
                                                    " :key="field.name" :label="field.label" :type="field.type"
                                                        v-model="row[field.name]
                                                            " :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`" :hint="field.hint" :required="field.required
                                                            " :options="field.options" :direction="field?.direction
                                                                " :disabled="field.disabled
                                                                    " :readonly="field.readonly
                                                                        " />
                                                    <FileUploadRow v-if="
                                                        field.type == 'file'
                                                    " :key="field.name" :label="field.label" :type="field.type"
                                                        v-model="row[field.name]
                                                            " :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`" :hint="field.hint" :required="field.required
                                                            " :multiple="field.multiple
                                                                " :extension="field.extension
                                                                    " :maxsize="field.maxsize" :maxfile="field.maxfile"
                                                        :disabled="field.disabled
                                                            " :readonly="field.readonly
                                                                " />
                                                    <Textarea v-if="
                                                        field.type ==
                                                        'textarea'
                                                    " :key="field.name" :label="field.label" :type="field.type"
                                                        v-model="row[field.name]
                                                            " :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`" :hint="field.hint" :required="field.required
                                                            " :disabled="field.disabled
                                                                " :readonly="field.readonly
                                                                    " />
                                                    <Date v-if="
                                                        field.type == 'date'
                                                    " :key="field.name" :label="field.label" :type="field.type"
                                                        v-model="row[field.name]
                                                            " :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`" :hint="field.hint" :required="field.required
                                                            " :disabled="field.disabled
                                                                " :readonly="field.readonly
                                                                    " :min="field?.min" :max="field?.max" />
                                                    <Datetime v-if="
                                                        field.type ==
                                                        'datetime'
                                                    " :key="field.name" :label="field.label" v-model="row[field.name]
                                                        " :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`" :hint="field.hint" :required="field.required
                                                            " :disabled="field.disabled
                                                                " :readonly="field.readonly
                                                                    " :min="field?.min" :max="field?.max" />
                                                    <Currency v-if="
                                                        field.type ==
                                                        'currency'
                                                    " :key="field.name" :label="field.label" v-model="row[field.name]
                                                        " :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`" :hint="field.hint" :required="field.required
                                                            " :disabled="field.disabled
                                                                " :readonly="field.readonly
                                                                    " :min="field?.min" :max="field?.max"
                                                        type="text" />
                                                    <Time v-if="
                                                        field.type ==
                                                        'time'
                                                    " :key="field.name" :label="field.label" v-model="row[field.name]
                                                        " :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`" :hint="field.hint" :required="field.required
                                                            " :disabled="field.disabled
                                                                " :readonly="field.readonly
                                                                    " :min="field?.min" :max="field?.max"
                                                        type="text" />
                                                    <!-- <Currency
                                                        v-if="
                                                            field.type ==
                                                            'currency'
                                                        "
                                                        :key="field.name"
                                                        :label="field.label"
                                                        v-model="
                                                            row[field.name]
                                                        "
                                                        :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`"
                                                        :hint="field.hint"
                                                        :required="
                                                            field.required
                                                        "
                                                        :disabled="
                                                            field.disabled
                                                        "
                                                        :readonly="
                                                            field.readonly
                                                        "
                                                        :min="field?.min"
                                                        :max="field?.max"
                                                        type="text"
                                                    /> -->
                                                </div>
                                                <div class="pt-5 flex gap-1" v-if="
                                                    section['editable'] !=
                                                    false
                                                ">
                                                    <Button type="button" @click="
                                                        addRow(sectionKey)
                                                        " class="border-1 border-primary/30" variant="secondary">
                                                        <Plus
                                                            class="h-1 w-1 text-primary inline-block align-middle cursor-pointer" />
                                                    </Button>
                                                    <Button type="button" :disabled="rowIndex == 0
                                                        " @click="
                                                            removeRow(
                                                                sectionKey,
                                                                rowIndex
                                                            )
                                                            " class="border-1 border-red-500/30" variant="secondary">
                                                        <SquareX
                                                            class="h-1 w-1 text-destructive inline-block align-middle cursor-pointer" />
                                                    </Button>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <span class="text-sm text-muted-foreground italic">{{ section?.label }} akan
                                                muncul disini.</span>
                                        </template>
                                    </div>
                                    <div v-else :class="`gap-2 p-4 grid grid-cols-1 md:grid-cols-${section.column}`">
                                        <div v-for="(field, x) in section['forms']" :key="x"
                                            :class="`${field.class ?? ''}`">
                                            <Input v-if="
                                                [
                                                    'text',
                                                    'email',
                                                    'hidden',
                                                ].includes(field.type)
                                            " :key="field.name" :label="field.label" :type="field.type"
                                                v-model="form[field.name]" :name="field.name" :id="field.name"
                                                :hint="field.hint" :required="field.required" :format="field?.format"
                                                :disabled="field.disabled" :readonly="field.readonly" />
                                            <Password v-if="field.type == 'password'" :key="field.name"
                                                :label="field.label" :type="field.type" v-model="form[field.name]"
                                                :name="field.name" :id="field.name" :hint="field.hint"
                                                :required="field.required" :format="field?.format"
                                                :disabled="field.disabled" :readonly="field.readonly" />
                                            <Number v-if="field.type == 'number'" :key="field.name" :label="field.label"
                                                :type="field.type" v-model="form[field.name]" :name="field.name"
                                                :id="field.name" :hint="field.hint" :required="field.required"
                                                :min="field?.min" :max="field?.max" :step="field?.step"
                                                :format="field?.format" :disabled="field.disabled"
                                                :readonly="field.readonly" />
                                            <Phone v-if="field.type == 'phone'" :key="field.name" :label="field.label"
                                                :type="field.type" v-model="form[field.name]" :name="field.name"
                                                :id="field.name" :hint="field.hint" :required="field.required"
                                                :min="field?.min" :max="field?.max" :step="field?.step"
                                                :format="field?.format" :disabled="field.disabled"
                                                :readonly="field.readonly" />
                                            <Select v-if="field.type == 'select'" :key="field.name" :label="field.label"
                                                :type="field.type" v-model="form[field.name]" :name="field.name"
                                                :id="field.name" :hint="field.hint" :required="field.required"
                                                :options="field.options" :disabled="field.disabled"
                                                :readonly="field.readonly" :multiple="field.multiple"
                                                @change="(value) => handleSelectChange(section.forms, field, value)" />
                                            <Radio v-if="field.type == 'radio'" :key="field.name" :label="field.label"
                                                :type="field.type" v-model="form[field.name]" :name="field.name"
                                                :id="field.name" :hint="field.hint" :required="field.required"
                                                :options="field.options" :direction="field?.direction"
                                                :disabled="field.disabled" :readonly="field.readonly" />
                                            <FileUpload v-if="field.type == 'file'" :key="field.name"
                                                :label="field.label" :type="field.type" v-model="form[field.name]"
                                                :name="field.name" :id="field.name" :hint="field.hint"
                                                :required="field.required" :multiple="field.multiple"
                                                :extension="field.extension" :maxsize="field.maxsize"
                                                :maxfile="field.maxfile" :disabled="field.disabled"
                                                :readonly="field.readonly" />
                                            <FileUploadRow v-if="field.type == 'filerow'" :key="field.name"
                                                :label="field.label" :type="field.type" v-model="form[field.name]"
                                                :name="field.name" :id="field.name" :hint="field.hint"
                                                :required="field.required" :multiple="field.multiple"
                                                :extension="field.extension" :maxsize="field.maxsize"
                                                :maxfile="field.maxfile" :disabled="field.disabled"
                                                :readonly="field.readonly" />
                                            <Textarea v-if="field.type == 'textarea'" :key="field.name"
                                                :label="field.label" :type="field.type" v-model="form[field.name]"
                                                :name="field.name" :id="field.name" :hint="field.hint"
                                                :required="field.required" :disabled="field.disabled"
                                                :readonly="field.readonly" />
                                            <Date v-if="field.type == 'date'" :key="field.name" :label="field.label"
                                                :type="field.type" v-model="form[field.name]" :name="field.name"
                                                :id="field.name" :hint="field.hint" :required="field.required"
                                                :disabled="field.disabled" :readonly="field.readonly" :min="field?.min"
                                                :max="field?.max" />
                                            <Currency v-if="field.type == 'currency'" :key="field.name"
                                                :label="field.label" v-model="form[field.name]" :name="field.name"
                                                :id="field.name" :hint="field.hint" :required="field.required"
                                                :disabled="field.disabled" :readonly="field.readonly" :min="field?.min"
                                                :max="field?.max" type="text" />
                                            <Datetime v-if="field.type == 'datetime'" :key="field.name"
                                                :label="field.label" v-model="form[field.name]" :name="field.name"
                                                :id="field.name" :hint="field.hint" :required="field.required"
                                                :disabled="field.disabled" :readonly="field.readonly" :min="field?.min"
                                                :max="field?.max" />
                                            <Time v-if="field.type == 'time'" :key="field.name" :label="field.label"
                                                v-model="form[field.name]" :name="field.name" :id="field.name"
                                                :hint="field.hint" :required="field.required" :disabled="field.disabled"
                                                :readonly="field.readonly" :min="field?.min" :max="field?.max" />
                                            <Draggable v-if="field.type == 'draggable'" v-model="form[field.name]"
                                                :name="field.name" :maxLevel="field.maxLevel ?? 3" />
                                            <Editor v-if="field.type == 'editor'" :key="field.name" :label="field.label"
                                                v-model="form[field.name]" :name="field.name" :id="field.name"
                                                :hint="field.hint" :required="field.required" :disabled="field.disabled"
                                                :readonly="field.readonly" @print="editorPrint()" />
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <!-- Actions -->
                        <div class="mt-6 flex justify-end gap-2">
                            <Button type="button" class="border-primary/20" variant="secondary"
                                @click="close">Batal</Button>
                            <Button class="border-primary/20" variant="secondary" type="submit">Simpan</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
        <div v-if="showPreview" class="modal-backdrop" @click.self="closePreview">
            <div class="modal-content">
                <iframe
                    :src="pdfUrl"
                    width="100%"
                    height="100%"
                ></iframe>
            </div>
        </div>
    </div>
</template>

<style scoped lang="css">
.hidden {
    width: 0px;
    height: 0px;
}
.modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.6);
    z-index: 9999;
}

.modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 9999;

    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    width: 85%;
    height: 85%;
    background: #fff;
    padding: 8px;
}
</style>

<script>
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
    Currency,
    FileUploadRow,
    Datetime,
    Time,
    Draggable,
    Editor
} from "@/components/ui/form";
import { Card, CardTitle, CardContent, CardHeader } from "@/components/ui/card";
import { Button } from "./ui/button";
import { reactive } from "vue";

export default {
    name: "EditorDialog",
    inheritAttrs: false,
    props: {
        open: Boolean,
        title: { type: String, default: "Form" },
        sections: { type: Object, default: () => ({}) },
        formData: { type: Object, default: () => ({}) },
        dialog: { type: Object, default: () => ({ width: 2 }) },
    },
    components: {
        Input,
        Select,
        Radio,
        FileUpload,
        FileUploadRow,
        Textarea,
        Number,
        Phone,
        Password,
        Date,
        Currency,
        Card,
        CardTitle,
        CardContent,
        CardHeader,
        Button,
        Time,
        Datetime,
        Draggable,
        Editor
    },
    data() {
        return {
            form: reactive({
                addtable: {},
            }),
            rowCount: {},
            isClosing: false,
            pdfUrl: null,
            pdfFilename: 'dokumen.pdf',
            showPreview: false,
        };
    },
    computed: {
        dialogState() {
            return this.open && !this.isClosing ? "open" : "closed";
        },
    },
    watch: {
        formData(newVal) {
            if (newVal !== undefined) {
                Object.assign(this.form, newVal);
            } else {
                this.form.addtable = {};
                this.initializeAddtableSections();
                this.initEditForm();
            }

            if (newVal && Object.keys(newVal).length === 0) {
                this.form = reactive({
                    addtable: {},
                });
            }
        },
        sections: {
            handler(newVal) {
                if (newVal) {
                    this.initializeAddtableSections();
                }
            },
            deep: true,
            immediate: true,
        },
        open(newVal) {
            if (newVal && this.sections) {
                this.isClosing = false;
                this.$nextTick(() => {
                    this.initializeAddtableSections();
                    this.initEditForm();
                });
            }
        },
    },

    methods: {
        initializeAddtableSections() {
            if (!this.sections) return;

            Object.entries(this.sections).forEach(([key, section]) => {
                if (section.type === "addtable") {
                    if (this.formData[key] != undefined) {
                        this.form.addtable[key] = this.formData[key];
                    }

                    if (
                        !this.form.addtable[key] ||
                        this.form.addtable[key].length === 0
                    ) {
                        if (
                            section.editable != undefined &&
                            section.editable == true
                        ) {
                            const newRow = {};
                            section.forms.forEach((f) => (newRow[f.name] = ""));
                            this.form.addtable[key] = [newRow];
                        }
                    }
                }
            });
        },
        getTitle() {
            if (!this.sections) return this.title;

            const firstKey = Object.keys(this.sections)[0];
            if (firstKey && this.sections[firstKey]?.title) {
                return this.sections[firstKey].title;
            }

            return this.title;
        },
        getDescription() {
            if (!this.sections) return this?.description;

            const firstKey = Object.keys(this.sections)[0];
            if (firstKey && this.sections[firstKey]?.description) {
                return this.sections[firstKey].description;
            }

            return this.description;
        },
        close() {
            this.isClosing = true;
            // Tunggu animasi selesai sebelum emit close
            setTimeout(() => {
                this.$emit("close");
                this.form = reactive({
                    addtable: {},
                });
                this.isClosing = false;
            }, 250); // Sesuaikan dengan durasi animasi keluar
        },
        submit(e) {
            e.preventDefault();
            this.$emit("onSubmit", this.form);
        },
        addRow(sectionKey) {
            const section = this.sections[sectionKey];
            if (!section || section.type !== "addtable") return;

            const row = {};
            section.forms.forEach((f) => (row[f.name] = ""));

            if (!this.form.addtable[sectionKey]) {
                this.form.addtable[sectionKey] = [];
            }

            this.form.addtable[sectionKey].push(row);
        },
        removeRow(sectionKey, index, value) {
            if (
                this.form.addtable[sectionKey] &&
                this.form.addtable[sectionKey].length > 1
            ) {
                this.form.addtable[sectionKey].splice(index, 1);
            }
        },
        async editorPrint() {
            this.$store.dispatch("documentEditor/downloadPdf", this.form)
                .then((response) => {
                   // ambil filename dari header
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
                    this.showPreview = true;
                })
                .catch((resp) => {
                    alert(resp.response.data.message);
                })
                .finally((f) => {
                    
                })
        },
        async handleSelectChange(section, field, value) {
            if (!field.onchange || !field.onchange.api || !field.onchange.destination) return;
            this.form[field.onchange.destination] = field.multiple ? [] : "";

            await this.runFieldOnChange(section, field, value);

        },
        async runFieldOnChange(section, field, value) {
            if (!field.onchange || !field.onchange.api) return;
            if (!value) return;

            const { api, destination, parameter } = field.onchange;

            const targetField = section.find(
                f => f.name === destination
            );

            try {
                const params = new URLSearchParams()
                const merged = {...this.form, ...this.formData}
                parameter.split(',').forEach(v => {
                    params.append(v, merged[v])
                })
                const response = await fetch(`${api}?${params.toString()}`)
                const result = await response.json();
                let dest = destination.split(',');
                if(dest.length > 1) {
                    dest.forEach(v => {
                        this.form[v] = result.data != null?result.data[v]:"";
                    })
                } else {
                    if (!targetField) return;

                    if (targetField.type === 'select') {
                        targetField.options = result.data;
                    } else if (targetField.type === 'draggable') {
                        if (this.form[destination] == "" || this.form[destination] == null) {
                            this.form[destination] = result.data;
                        }
                    }
                }

            } catch (err) {
                console.error("Gagal load dependent select:", err);
                targetField.options = {};
                this.form[destination] = "";
                let dest = destination.split(',');
                if(dest.length > 1) {
                    dest.forEach(v => {
                        this.form[v] = "";
                    })
                } else {
                    this.form[destination] = "";
                }
            }
        },
        async initEditForm() {
            if (!this.form?.id) return;

            for (const forms of Object.values(this.sections)) {

                for (const form of forms.forms) {
                    if (!form.onchange) continue;

                    const value = this.form[form.name];

                    await this.runFieldOnChange(forms.forms, form, value);
                }
            }
        },
        closePreview() {
            if (this.pdfUrl) {
                URL.revokeObjectURL(this.pdfUrl);
            }
            this.pdfUrl = null;
            this.showPreview = false;
        }
    },
    beforeMount() {
        this.initializeAddtableSections();
    },
};
</script>
