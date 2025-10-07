<template>
  <!-- Overlay -->
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click="close()">
    <Card class="w-full max-w-2xl bg-white shadow-lg rounded-lg overflow-hidden" @click.stop>
      <CardContent class="p-6">
        <form @submit="submit" class="flex flex-col" enctype="multipart/form-data">
          <h2 v-if="contextMenu.title" class="text-xl font-bold mb-2">{{ contextMenu.title }}</h2>
          <span class="text-gray-600 mb-4">{{ contextMenu.message }}</span>

          <!-- Scrollable Form Area -->
          <div class="max-h-[70vh] overflow-y-auto space-y-4">
            <div class="flex flex-1 w-full" v-for="(field,x) in contextMenu.forms" :key="x">
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
                    :modelValue="field.value"
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
            </div>
          </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end gap-2">
                <Button type="button" class="border-orange-200" variant="secondary" @click="close">Tidak</Button>
                <Button class="border-orange-200" variant="secondary" type="submit">
                    <LoaderCircle v-if="loader" class="animate-spin"/>
                    <span v-else>Ya</span>
                </Button>
            </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>

<script>
    import {
      Input, Select, Radio, FileUpload, Textarea, Number,
      Phone, Password, Date, Currency
    } from "@/components/ui/form";
    import { Card, CardContent } from "@/components/ui/card";
    import { Button } from "./ui/button";

    export default {
        name: "ConfirmDialog",
        props: {
            open: { type: Boolean, default: false},
            contextMenu: {type: Object, default: {}},
            data: { type: Object, default: {}},
            isLoading: {type: Boolean, default: false}
        },
        components: {
            Input, Select, Radio, FileUpload, Textarea, Number,
            Phone, Password, Date, Currency, Card, CardContent, Button
        },
        watch: {
            isLoading: {
                handler(newVal) {
                   this.loader = newVal
                }
            }
        },
        data() {
            return {
              form: { },
              loader: false
            };
        },
        methods: {
            close() {
                this.$emit("close");
                this.form = {}
                this.loader = false
            },
            submit(e) {
                e.preventDefault();
                this.loader = true
                this.$emit("onSubmit", this.form);
                this.form = {}
            },
        },
        mounted(){
            this.loader = false
        }
    };
</script>
