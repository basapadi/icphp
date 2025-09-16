<template>
    <!-- Overlay -->
    <div v-if="open" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
        <Card :class="`w-full max-w-${dialog.width??4}xl`">
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
                    <template v-for="(section,i) in sections" :key="i"> 
                        <span class="text-gray-400 font-bold text-shadow-2xs">{{section?.label}}</span>
                        <div class="space-y-2 gap-2 border-2 rounded-sm mb-4" >
                            <div class="space-y-2 p-4" v-if="section['type'] == 'addtable'">
                                <div class="flex gap-1">
                                    <div class="flex flex-1 w-full" v-for="(field,x) in section['forms']" :key="x">
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
                                            class="w-full"
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
                                        <Number v-else-if="field.type == 'number'"
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
                                        <Phone v-else-if="field.type == 'phone'"
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
                                        <Select v-else-if="field.type == 'select'"
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
                                        <Radio v-else-if="field.type == 'radio'"
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
                                        <FileUpload v-else-if="field.type == 'file'"
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
                                        <Textarea v-else-if="field.type == 'textarea'"
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
                                        <Date v-else-if="field.type == 'date'"
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
                                    </div>
                                    <div class="pt-5 flex gap-1">
                                        <Button type="button" class="border-orange-200" variant="secondary" ><Plus class="h-1 w-1 text-orange-300 inline-block align-middle cursor-pointer" /></Button>
                                        <Button type="button" class="border-orange-200" variant="secondary" ><SquareX class="h-1 w-1 text-red-300 inline-block align-middle cursor-pointer" /></Button>
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
                                    <Number v-else-if="field.type == 'number'"
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
                                    <Phone v-else-if="field.type == 'phone'"
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
                                    <Select v-else-if="field.type == 'select'"
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
                                    <Radio v-else-if="field.type == 'radio'"
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
                                    <FileUpload v-else-if="field.type == 'file'"
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
                                    <Textarea v-else-if="field.type == 'textarea'"
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
                                    <Date v-else-if="field.type == 'date'"
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
                                </template>
                            </div>
                        </div>
                    </template>
                    <!-- Actions -->
                    <div class="mt-6 flex justify-end gap-2">
                        <Button class="border-orange-200" variant="secondary" @click="close">Batal</Button>
                        <Button class="border-orange-200" variant="secondary" type="submit">Simpan</Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
<script>
import { Input,Select,Radio,FileUpload,Textarea,Number,Phone,Password,Date } from "@/components/ui/form";
import { Card, CardTitle, CardContent, CardHeader } from "@/components/ui/card";
import {Info,Plus,SquareX} from "lucide-vue-next";
import { Button } from "./ui/button";

export default {
    name: "FormDialog",
    props: {
        open: Boolean,
        title: { type: String, default: "Form" },
        sections: { type: Object, default: () => {} },
        data: { type: Object, default: () => ({}) },
        dialog: { type: Object, default: () => ({}) }
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
        Info,
        Plus,
        SquareX
    },
    data() {
        return {
            form: {},
        };
    },
    watch: {
        'data'() {
            if(this.data != null || this.data?.id != undefined) this.form = this.data
            else this.form = {}
        }
    },
    methods: {
        load() {},
        close() {
            this.$emit("close");
            this.form = {}
        },
        submit(e) {
            e.preventDefault();
            this.$emit("onSubmit", this.form);
        },
    }
};
</script>
