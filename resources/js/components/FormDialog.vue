<template>
    <!-- Overlay -->
    <div v-if="open" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
        <Card class="w-full max-w-4xl">
            <CardHeader>
                <CardTitle>Form {{ title }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <template v-for="(section,i) in sections" :key="i"> 
                        <span class="text-gray-400 font-bold text-shadow-2xs">{{section?.label}}</span>
                        <div class="space-y-2 p-4 grid grid-cols-1 md:grid-cols-2 gap-2 border-2 rounded-sm ">
                            <template v-for="(field,x) in section['forms']" :key="x">
                                <Input v-if="['text', 'email', 'phone'].includes(field.type)"
                                    :key="field.name"
                                    :label="field.label"
                                    :type="field.type"
                                    v-model="form[field.name]"
                                    :name="field.name"
                                    :id="field.id"
                                    :hint="field.hint"
                                    :required="field.required"
                                    class=""
                                />
                                <Select v-if="field.type == 'select'"
                                    :key="field.name"
                                    :label="field.label"
                                    :type="field.type"
                                    v-model="form[field.name]"
                                    :name="field.name"
                                    :id="field.id"
                                    :hint="field.hint"
                                    :required="field.required"
                                    :options="field.options"
                                />
                                <Radio v-if="field.type == 'radio'"
                                    :key="field.name"
                                    :label="field.label"
                                    :type="field.type"
                                    v-model="form[field.name]"
                                    :name="field.name"
                                    :id="field.id"
                                    :hint="field.hint"
                                    :required="field.required"
                                    :options="field.options"
                                />  
                                <FileUpload v-if="field.type == 'file'"
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
                                />
                            </template>
                        </div>
                    </template>
                    <!-- Actions -->
                    <div class="mt-6 flex justify-end gap-2">
                        <Button variant="outline" @click="close">Batal</Button>
                        <Button variant="secondary" type="submit">Simpan</Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
<script>
import { Input,Select,Radio,FileUpload } from "@/components/ui/form";
import { Card, CardTitle, CardContent, CardHeader } from "@/components/ui/card";
import { Button } from "./ui/button";

export default {
    name: "FormDialog",
    props: {
        open: Boolean,
        title: { type: String, default: "Form" },
        sections: { type: Array, default: () => [] },
        data: { type: Object, default: () => ({}) },
    },
    components: {
        Input,
        Select,
        Radio,
        FileUpload,
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
    methods: {
        load() {},
        close() {
            this.$emit("close");
            this.form = {}
        },
        submit() {
            this.$emit("submit", this.form);
            this.close();
        },
    },
    beforeMount() {
        // this.sections.forEach((f) => {
        //     this.form[f.name] = this.data[f.name] ?? null;
        // });
    },
};
</script>
