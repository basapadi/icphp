<template>
    <!-- Overlay -->
    <div
        v-if="open"
        class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
    >
        <Card class="w-full max-w-4xl">
            <CardHeader>
                <CardTitle>Form {{ title }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit">
                    <div
                        class="space-y-2 grid grid-cols-1 md:grid-cols-2 gap-4"
                    >
                        <template v-for="field in fields">
                            <Input
                                v-if="
                                    ['text', 'email', 'phone'].includes(
                                        field.type
                                    )
                                "
                                :key="field.name"
                                :label="field.label"
                                :type="field.type"
                                :modelValue="form[field.name]"
                                :name="field.name"
                                :id="field.id"
                                :hint="field.hint"
                                :required="field.required"
                            />
                        </template>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex justify-end gap-2">
                        <Button variant="outline" @click="close">Batal</Button>
                        <Button type="submit">Simpan</Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
<script>
import { Input } from "@/components/ui";
import { Card, CardTitle, CardContent, CardHeader } from "@/components/ui/card";
import { Button } from "./ui/button";

export default {
    name: "FormDialog",
    props: {
        open: Boolean,
        title: { type: String, default: "Form" },
        fields: { type: Array, default: () => [] },
        data: { type: Object, default: () => ({}) },
    },
    components: {
        Input,
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
        },
        submit() {
            this.$emit("submit", this.form);
            this.close();
        },
    },
    beforeMount() {
        this.fields.forEach((f) => {
            this.form[f.name] = this.data[f.name] ?? "";
        });
    },
};
</script>
