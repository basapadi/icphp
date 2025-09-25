<template>
    <!-- Overlay -->
    <div v-if="open" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
        <Card :class="`w-full max-w-${dialog.width}xl`">
            <CardContent>
                <ul class="list-disc list-outside px-4 pb-2">
                    <li class="text-xs italic text-gray-600">
                        Tanda <span class="text-red-600">*</span> harus diisi
                    </li>
                    <li class="text-xs italic text-gray-600">
                        Pastikan data yang anda masukkan sudah benar
                    </li>
                    <li class="text-xs italic text-gray-600 list-disc items-center gap-1">
                        Lihat info lebih lanjut pada icon
                        <Info class="h-4 w-4 text-orange-300 inline-block align-middle cursor-pointer" />
                    </li>
                </ul>

                <form @submit="submit" enctype="multipart/form-data">
                    <div class="h-150 overflow-y-auto" v-if="sections && Object.keys(sections).length">
                        <template v-for="(section, sectionKey) in sections" :key="sectionKey">
                            <span class="text-gray-600 font-bold"><SquareChevronRight class="h-5 w-5 mr-2 mb-1 text-orange-300 inline-block align-middle" />{{section?.label}}</span>
                            <div class="space-y-2 gap-2 border-1 border-dashed rounded-sm mb-4">
                                <div class="space-y-2 p-4" v-if="section['type'] == 'addtable'">
                                    <div v-for="(row, rowIndex) in form.addtable[sectionKey]" :key="rowIndex" class="flex border-1 shadow-sm rounded-sm border-gray-300 border-dashed py-4 px-2 gap-1 odd:bg-gray-50 even:bg-white">
                                        <span class="pl-2 pr-4 font-bold text-orange-400">{{rowIndex+1}}</span>
                                        <div class="flex flex-1 w-full" v-for="(field,x) in section['forms']" :key="x">
                                            <Input v-if="['text', 'email'].includes(field.type)"
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
                                            <Password v-if="field.type == 'password'"
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
                                            />
                                            <Number v-if="field.type == 'number'"
                                                :key="field.name"
                                                :label="field.label"
                                                :type="field.type"
                                                v-model="row[field.name]"
                                                :name="`${field.name}[${rowIndex}]`"
                                                :id="`${field.name}_${rowIndex}`"
                                                :hint="field.hint"
                                                :required="field.required"
                                                :min="field?.min"
                                                :max="field?.max"
                                                :step="field?.step"
                                                :format="field?.format"
                                                :disabled="field.disabled"
                                                :readonly="field.readonly"
                                            />
                                            <Phone v-if="field.type == 'phone'"
                                                :key="field.name"
                                                :label="field.label"
                                                :type="field.type"
                                                v-model="row[field.name]"
                                                :name="`${field.name}[${rowIndex}]`"
                                                :id="`${field.name}_${rowIndex}`"
                                                :hint="field.hint"
                                                :required="field.required"
                                                :min="field?.min"
                                                :max="field?.max"
                                                :step="field?.step"
                                                :format="field?.format"
                                                :disabled="field.disabled"
                                                :readonly="field.readonly"
                                            />
                                            <Select v-if="field.type == 'select'"
                                                :key="field.name"
                                                :label="field.label"
                                                :type="field.type"
                                                v-model="row[field.name]"
                                                :name="`${field.name}[${rowIndex}]`"
                                                :id="`${field.name}_${rowIndex}`"
                                                :hint="field.hint"
                                                :required="field.required"
                                                :options="field.options"
                                                :disabled="field.disabled"
                                                :readonly="field.readonly"
                                                :multiple="field.multiple"
                                            />
                                            <Radio v-if="field.type == 'radio'"
                                                :key="field.name"
                                                :label="field.label"
                                                :type="field.type"
                                                v-model="row[field.name]"
                                                :name="`${field.name}[${rowIndex}]`"
                                                :id="`${field.name}_${rowIndex}`"
                                                :hint="field.hint"
                                                :required="field.required"
                                                :options="field.options"
                                                :direction="field?.direction"
                                                :disabled="field.disabled"
                                                :readonly="field.readonly"
                                            />  
                                            <FileUpload v-if="field.type == 'file'"
                                                :key="field.name"
                                                :label="field.label"
                                                :type="field.type"
                                                v-model="row[field.name]"
                                                :name="`${field.name}[${rowIndex}]`"
                                                :id="`${field.name}_${rowIndex}`"
                                                :hint="field.hint"
                                                :required="field.required"
                                                :multiple="field.multiple"
                                                :extension="field.extension"
                                                :maxsize="field.maxsize"
                                                :maxfile="field.maxfile"
                                                :disabled="field.disabled"
                                                :readonly="field.readonly"
                                            />
                                            <Textarea v-if="field.type == 'textarea'"
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
                                            />
                                            <Date v-if="field.type == 'date'"
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
                                            <Currency v-if="field.type == 'currency'"
                                                :key="field.name"
                                                :label="field.label"
                                                v-model="row[field.name]"
                                                :name="`${field.name}[${rowIndex}]`"
                                                :id="`${field.name}_${rowIndex}`"
                                                :hint="field.hint"
                                                :required="field.required"
                                                :disabled="field.disabled"
                                                :readonly="field.readonly"
                                                :min="field?.min"
                                                :max="field?.max"
                                                type="text"
                                            />
                                        </div>
                                        <div class="pt-5 flex gap-1">
                                            <!-- Fixed variable name from 'i' to 'sectionKey' -->
                                            <Button type="button" @click="addRow(sectionKey)" class="border-orange-200" variant="secondary" ><Plus class="h-1 w-1 text-orange-300 inline-block align-middle cursor-pointer" /></Button>
                                            <Button type="button" :disabled="rowIndex == 0" @click="removeRow(sectionKey,rowIndex)" class="border-orange-200" variant="secondary" ><SquareX class="h-1 w-1 text-red-300 inline-block align-middle cursor-pointer" /></Button>
                                        </div>
                                    </div>
                                </div>
                                <div v-else :class="`gap-2 p-4 grid grid-cols-1 md:grid-cols-${section.column}`">
                                    <template  v-for="(field,x) in section['forms']" :key="x">
                                        <Input v-if="['text', 'email'].includes(field.type)"
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
                                        <Password v-if="field.type == 'password'"
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
                                        <Number v-if="field.type == 'number'"
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
                                        <Phone v-if="field.type == 'phone'"
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
                                        <Select v-if="field.type == 'select'"
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
                                        <Radio v-if="field.type == 'radio'"
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
                                        <FileUpload v-if="field.type == 'file'"
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
                                        <Textarea v-if="field.type == 'textarea'"
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
                                        <Date v-if="field.type == 'date'"
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
                                        <Currency v-if="field.type == 'currency'"
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
                        <Button type="button" class="border-orange-200" variant="secondary" @click="close">Batal</Button>
                        <Button class="border-orange-200" variant="secondary" type="submit">Simpan</Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
<script>
import { Input,Select,Radio,FileUpload,Textarea,Number,Phone,Password,Date,Currency } from "@/components/ui/form";
import { Card, CardTitle, CardContent, CardHeader } from "@/components/ui/card";
import { Button } from "./ui/button";
import { reactive } from 'vue';

export default {
    name: "FormDialog",
    props: {
        open: Boolean,
        title: { type: String, default: "Form" },
        sections: { type: Object, default: () => ({}) },
        formData: { type: Object, default: () => ({}) },
        dialog: { type: Object, default: () => ({width: 2}) }
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
        Currency,
        Card,
        CardTitle,
        CardContent,
        CardHeader,
        Button
    },
    data() {
        return {
            form: reactive({
                addtable: {}
            }),
            rowCount: {}
        };
    },
    watch: {
        'formData'(newVal) {
            if (newVal !== undefined) {
                Object.assign(this.form, newVal);
            }else {
                this.form.addtable = {};
                this.initializeAddtableSections();
            }

            if (newVal && Object.keys(newVal).length === 0) {
                this.form = reactive({
                    addtable: {}
                });
            }
        },
        'sections': {
            handler(newVal) {
                if (newVal) {
                    this.initializeAddtableSections();
                }
            },
            deep: true,
            immediate: true
        },
        'open'(newVal) {
            if (newVal && this.sections) {
                this.$nextTick(() => {
                    this.initializeAddtableSections();
                });
            }
        }
    },

    methods: {
        initializeAddtableSections() {
            if (!this.sections) return;
            
            Object.entries(this.sections).forEach(([key, section]) => {
                if (section.type === 'addtable') {

                    if(this.formData[key] != undefined){
                        this.form.addtable[key] = this.formData[key]
                    }

                    if (!this.form.addtable[key] || this.form.addtable[key].length === 0) {
                        const newRow = {};
                        section.forms.forEach(f => newRow[f.name] = '');
                        this.form.addtable[key] = [newRow];
                    }
                }
            });
        },
        close() {
            this.$emit("close");
            this.form = reactive({
                addtable: {}
            })
        },
        submit(e) {
            e.preventDefault();
            this.$emit("onSubmit", this.form);
        },
        addRow(sectionKey) {
            const section = this.sections[sectionKey];
            if (!section || section.type !== "addtable") return;

            const row = {};
            section.forms.forEach(f => row[f.name] = "");

            if (!this.form.addtable[sectionKey]) {
                this.form.addtable[sectionKey] = [];
            }

            this.form.addtable[sectionKey].push(row);
        },
        removeRow(sectionKey, index) {
            if (this.form.addtable[sectionKey] && this.form.addtable[sectionKey].length > 1) {
                this.form.addtable[sectionKey].splice(index, 1);
            }
        }
    },
    beforeMount() {
        this.initializeAddtableSections();
    }
};
</script>
