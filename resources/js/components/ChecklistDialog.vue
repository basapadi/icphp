<template>
    <!-- Overlay -->
    <div
        v-if="open"
        class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
    >
        <Card :class="`w-300 mx-4`">
            <CardContent>
                <ul class="list-disc list-outside px-4 pb-2">
                    <li class="text-xs italic text-muted-foreground">
                        Tanda <span class="text-destructive">*</span> harus
                        diisi
                    </li>
                    <li class="text-xs italic text-muted-foreground">
                        Pastikan data yang anda masukkan sudah benar
                    </li>
                    <li
                        class="text-xs italic text-muted-foreground list-disc items-center gap-1"
                    >
                        Lihat info lebih lanjut pada icon
                        <Info
                            class="h-4 w-4 text-primary inline-block align-middle cursor-pointer"
                        />
                    </li>
                </ul>
                <form @submit="submit" enctype="multipart/form-data">
                    <div
                        class="h-150 overflow-y-auto"
                        v-if="sections && Object.keys(sections).length"
                    >
                        <template v-for="(section, sectionKey) in sections" :key="sectionKey">
                            <span class="text-muted-foreground font-bold"
                                ><SquareChevronRight
                                    class="h-5 w-5 mr-2 mb-1 text-primary inline-block align-middle"
                                />{{ section?.label }}</span
                            >
                            <span class="text-xs px-2 italic text-primary">{{
                                section?.description
                            }}</span>
                            <div class="space-y-2 gap-2 border-1 border-dashed rounded-sm mb-4" >
                                <div class="p-2" v-if="section['type'] == 'checklist'" >
                                    <template v-if="form.addtable != undefined " >
                                        <div class="flex px-2 py-1 border-1 shadow-sm border-border border-dashed mx-2">
                                            <template
                                                v-for="(header, x) in section['headers']"
                                                :key="x"
                                            >
                                                <div v-if="header.trim().toLowerCase() == 'uraian'" class="flex-[7]">{{ header }}</div>
                                                <div v-else-if="header.trim().toLowerCase() == 'no'" class="flex-[1]">{{ header }}</div>
                                                <div v-else class="flex-[2]">{{ header }}</div>
                                                
                                            </template>
                                        </div>
                                        
                                        <div v-for="(row, rowIndex) in form.addtable" :key="rowIndex" class="flex px-2 py-1 border-1 shadow-sm border-border border-dashed mx-2 odd:bg-muted even:bg-background">
                                            <div class="shrink-0 text-sm text-gray-600" style="width:80px">
                                                {{ row.no }}
                                            </div>
                                            <div class="flex flex-[7] text-sm text-gray-600">
                                                <div
                                                    class="flex flex-[7] text-sm text-gray-600"
                                                    :style="{ paddingLeft: `${row.level * 20}px` }"
                                                    :class="row.no.replace(/_/g, '').length === 1 ? 'font-bold' : ''"
                                                >
                                                    {{ row.uraian }}
                                                </div>
                                            </div>
                                            <template v-if="row.fillable">
                                                <div class="flex flex-2 w-full" v-for="(field, x) in section['forms']" :key="x">
                                                    <PlainInput
                                                        v-if=" ['text', 'email'].includes(field.type) "
                                                        :key="field.name"
                                                        :label="field.label"
                                                        :type="field.type"
                                                        v-model="row[field.name]"
                                                        :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`"
                                                        :hint="field.hint"
                                                        :required="field.required"
                                                        :format="field?.format"
                                                        :disabled="field.disabled"
                                                        :readonly="field.readonly"
                                                        :modelValue="field.value"
                                                    />

                                                    <PlainDate
                                                        v-if="field.type == 'date'"
                                                        :key="field.name"
                                                        :label="field.label"
                                                        :type="field.type"
                                                        v-model="row[field.name]"
                                                        :name="`${field.name}[${rowIndex}]`"
                                                        :id="`${field.name}_${rowIndex}`"
                                                        :hint="field.hint"
                                                        :required="field.required"
                                                        :disabled="field.disabled"
                                                        :readonly="field.readonly"
                                                        :min="field?.min"
                                                        :max="field?.max"
                                                    />
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                                <div v-else :class="`gap-2 p-4 grid grid-cols-1 md:grid-cols-${section.column}`" >
                                    <template
                                        v-for="(field, x) in section['forms']"
                                        :key="x"
                                    >
                                        <Input
                                            v-if="
                                                [
                                                    'text',
                                                    'email',
                                                ].includes(field.type)
                                            "
                                            :key="field.name"
                                            :label="field.label"
                                            :type="field.type"
                                            v-model="form[field.name]"
                                            :name="field.name"
                                            :id="field.name"
                                            :hint="field.hint"
                                            :required="field.required"
                                            :format="field?.format"
                                            :disabled="field.disabled"
                                            :readonly="field.readonly"
                                        />
                                        <Password
                                            v-if="field.type == 'password'"
                                            :key="field.name"
                                            :label="field.label"
                                            :type="field.type"
                                            v-model="form[field.name]"
                                            :name="field.name"
                                            :id="field.name"
                                            :hint="field.hint"
                                            :required="field.required"
                                            :format="field?.format"
                                            :disabled="field.disabled"
                                            :readonly="field.readonly"
                                        />
                                        <Number
                                            v-if="field.type == 'number'"
                                            :key="field.name"
                                            :label="field.label"
                                            :type="field.type"
                                            v-model="form[field.name]"
                                            :name="field.name"
                                            :id="field.name"
                                            :hint="field.hint"
                                            :required="field.required"
                                            :min="field?.min"
                                            :max="field?.max"
                                            :step="field?.step"
                                            :format="field?.format"
                                            :disabled="field.disabled"
                                            :readonly="field.readonly"
                                        />
                                        <Phone
                                            v-if="field.type == 'phone'"
                                            :key="field.name"
                                            :label="field.label"
                                            :type="field.type"
                                            v-model="form[field.name]"
                                            :name="field.name"
                                            :id="field.name"
                                            :hint="field.hint"
                                            :required="field.required"
                                            :min="field?.min"
                                            :max="field?.max"
                                            :step="field?.step"
                                            :format="field?.format"
                                            :disabled="field.disabled"
                                            :readonly="field.readonly"
                                        />
                                        <Select
                                            v-if="field.type == 'select'"
                                            :key="field.name"
                                            :label="field.label"
                                            :type="field.type"
                                            v-model="form[field.name]"
                                            :name="field.name"
                                            :id="field.name"
                                            :hint="field.hint"
                                            :required="field.required"
                                            :options="field.options"
                                            :disabled="field.disabled"
                                            :readonly="field.readonly"
                                            :multiple="field.multiple"
                                        />
                                        <Radio
                                            v-if="field.type == 'radio'"
                                            :key="field.name"
                                            :label="field.label"
                                            :type="field.type"
                                            v-model="form[field.name]"
                                            :name="field.name"
                                            :id="field.name"
                                            :hint="field.hint"
                                            :required="field.required"
                                            :options="field.options"
                                            :direction="field?.direction"
                                            :disabled="field.disabled"
                                            :readonly="field.readonly"
                                        />
                                        <FileUpload
                                            v-if="field.type == 'file'"
                                            :key="field.name"
                                            :label="field.label"
                                            :type="field.type"
                                            v-model="form[field.name]"
                                            :name="field.name"
                                            :id="field.name"
                                            :hint="field.hint"
                                            :required="field.required"
                                            :multiple="field.multiple"
                                            :extension="field.extension"
                                            :maxsize="field.maxsize"
                                            :maxfile="field.maxfile"
                                            :disabled="field.disabled"
                                            :readonly="field.readonly"
                                        />
                                        <Textarea
                                            v-if="field.type == 'textarea'"
                                            :key="field.name"
                                            :label="field.label"
                                            :type="field.type"
                                            v-model="form[field.name]"
                                            :name="field.name"
                                            :id="field.name"
                                            :hint="field.hint"
                                            :required="field.required"
                                            :disabled="field.disabled"
                                            :readonly="field.readonly"
                                        />
                                        <Date
                                            v-if="field.type == 'date'"
                                            :key="field.name"
                                            :label="field.label"
                                            :type="field.type"
                                            v-model="form[field.name]"
                                            :name="field.name"
                                            :id="field.name"
                                            :hint="field.hint"
                                            :required="field.required"
                                            :disabled="field.disabled"
                                            :readonly="field.readonly"
                                            :min="field?.min"
                                            :max="field?.max"
                                        />
                                        <Currency
                                            v-if="field.type == 'currency'"
                                            :key="field.name"
                                            :label="field.label"
                                            v-model="form[field.name]"
                                            :name="field.name"
                                            :id="field.name"
                                            :hint="field.hint"
                                            :required="field.required"
                                            :disabled="field.disabled"
                                            :readonly="field.readonly"
                                            :min="field?.min"
                                            :max="field?.max"
                                            type="text"
                                        />
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                    <!-- Actions -->
                    <div class="mt-6 flex justify-end gap-2">
                        <Button
                            type="button"
                            class="border-primary/20"
                            variant="secondary"
                            @click="close"
                            >Batal</Button
                        >
                        <Button
                            class="border-primary/20"
                            variant="secondary"
                            type="submit"
                            >Simpan</Button
                        >
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
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
    PlainInput,
    PlainDate,
    Currency
} from "@/components/ui/form";
import { Card, CardTitle, CardContent, CardHeader } from "@/components/ui/card";
import { Button } from "./ui/button";
import { reactive } from "vue";

export default {
    name: "ChecklistDialog",
    inheritAttrs: false,
    props: {
        open: Boolean,
        title: { type: String, default: "Form" },
        sections: { type: Object, default: () => ({}) },
        formData: { type: Object, default: () => ({}) },
        points: { type: Object, default: () => ({}) },
        dialog: { type: Object, default: () => ({ width: 7 }) },
    },
    components: {
        Input,
        Select,
        Radio,
        FileUpload,
        Textarea,
        Number,
        Phone,
        Password,
        Date,
        Card,
        CardTitle,
        CardContent,
        CardHeader,
        Button,
        PlainInput,
        PlainDate,
        Currency
    },
    data() {
        return {
            form: reactive({
                addtable: {},
            }),
            rowCount: {},
        };
    },
    watch: {
        formData(newVal) {
            if (newVal !== undefined) {
                Object.assign(this.form, newVal);
            } else {
                this.form.addtable = {};
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
        }
    },

    methods: {
        initializeAddtableSections() {
            if (!this.sections) return;
            Object.entries(this.sections).forEach(([key, section]) => {
                if (section.type === "checklist") {
                    if (this.formData[key] != undefined) {
                        this.form.addtable[key] = this.formData[key];
                    }

                    Object.entries(this.points).forEach(([k,point]) => {
                        if (section.editable != undefined && section.editable == true ) {
                            let newRow = {};
                            newRow.level = point.no.split('.').length - 1
                            section.forms.forEach((f) => (newRow[f.name] = ""));
                            this.form.addtable[point.no] = {...point,...newRow};
                        }
                    })

                   
                }
            });
        },
        close() {
            this.$emit("close");
            this.form = reactive({
                addtable: {},
            });
        },
        submit(e) {
            e.preventDefault();
            this.$emit("onSubmit", this.form);
        },
    }
}

</script>