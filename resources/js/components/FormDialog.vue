<template>
    <!-- Overlay -->
    <div v-if="open" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
        <Card class="w-full max-w-4xl">
            <CardContent>
                <form @submit="submit" enctype="multipart/form-data">
                    <template v-for="(section,i) in sections" :key="i"> 
                        <span class="text-gray-400 font-bold text-shadow-2xs">{{section?.label}}</span>
                        <div :class="`space-y-2 p-4 grid grid-cols-1 md:grid-cols-${section.column} gap-2 border-2 rounded-sm mb-4`">
                            <template v-for="(field,x) in section['forms']" :key="x">
                                <Input v-if="['text', 'email'].includes(field.type)"
                                    :key="field.name"
                                    :label="field.label"
                                    :type="field.type"
                                    v-model="form[field.name]"
                                    :name="field.name"
                                    :id="field.id"
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
                                    :id="field.id"
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
                                    :id="field.id"
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
                                    :id="field.id"
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
                                    :id="field.id"
                                    :hint="field.hint"
                                    :required="field.required"
                                    :options="field.options"
                                    :disabled="field.disabled"
                                    :readonly="field.readonly"
                                />
                                <Radio v-else-if="field.type == 'radio'"
                                    :key="field.name"
                                    :label="field.label"
                                    :type="field.type"
                                    v-model="form[field.name]"
                                    :name="field.name"
                                    :id="field.id"
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
                                    :id="field.id"
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
                                    :id="field.id"
                                    :hint="field.hint"
                                    :required="field.required"
                                    :disabled="field.disabled"
                                    :readonly="field.readonly"
                                />
                            </template>
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
import { Input,Select,Radio,FileUpload,Textarea,Number,Phone,Password } from "@/components/ui/form";
import { Card, CardTitle, CardContent, CardHeader } from "@/components/ui/card";
import { Button } from "./ui/button";

export default {
    name: "FormDialog",
    props: {
        open: Boolean,
        title: { type: String, default: "Form" },
        sections: { type: Object, default: () => {} },
        data: { type: Object, default: () => ({}) },
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
        Card,
        CardTitle,
        CardContent,
        CardHeader,
        Button
    },
    data() {
        return {
            form: {},
        };
    },
    watch: {
        'data'() {
            if(this.data != null || this.data?.id != undefined) this.form = this.data
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
            // this.form = {}
        },
    }
};
</script>
