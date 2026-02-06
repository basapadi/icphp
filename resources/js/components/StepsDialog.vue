<template>
    <!-- Overlay dengan transition -->
    <div v-if="open" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50" @click="close">
        <!-- Container dialog dengan animasi -->
        <div class="dialog-scale-animation" :data-state="dialogState" @click.stop>
            <Card class="w-auto max-w-[95%] min-w-[80vw] max-h-[90vh] overflow-hidden mx-4">
                <CardHeader>
                    <CardTitle>
                        <span class="text-muted-foreground font-bold">
                            <SquareChevronRight class="h-5 w-5 mr-2 mb-1 text-primary inline-block align-middle" />
                            {{ title }}
                        </span>
                    </CardTitle>
                    <CardDescription>
                        <ul class="list-disc list-outside px-4 pb-2">
                            <li class="text-xs italic text-muted-foreground">
                                Tanda
                                <span class="text-destructive">*</span> harus
                                diisi
                            </li>
                            <li class="text-xs italic text-muted-foreground">
                                Pastikan data yang anda masukkan sudah benar
                            </li>
                            <li class="text-xs italic text-muted-foreground list-disc items-center gap-1">
                                Lihat info lebih lanjut pada icon
                                <Info class="h-4 w-4 text-primary inline-block align-middle cursor-pointer" />
                            </li>
                        </ul>
                    </CardDescription>
                </CardHeader>
                <CardContent class="max-h-[90vh]">
                    <form @submit="submit" enctype="multipart/form-data">
                        <div class="h-full max-h-[70vh] max-w-[100%] flex">
                            <div class="w-1/4 pr-6 overflow-y-auto">
                                <div v-for="(step, index) in arraySteps" :key="step.key" class="flex items-start">
                                    <div class="flex flex-col items-center mr-4">
                                        <div class="w-8 h-8 rounded-full border flex items-center justify-center text-sm font-semibold"
                                            :class="circleClass(index)">
                                            {{ index + 1 }}
                                        </div>
                                        <div v-if="index < arraySteps.length - 1" class="w-px h-4 mt-1"
                                            :class="lineClass(index)" />
                                    </div>
                                    <div class="pb-8 w-full cursor-pointer" @click="goToStep(index)">
                                        <h3 class="text-sm font-medium" :class="labelClass(index)">
                                            {{ step.title }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="w-3/4 overflow-y-auto">
                                <div class="border rounded p-4">
                                    <h2 class="font-semibold mb-4">
                                        {{ arraySteps[currentStep]?.title }}
                                    </h2>

                                    <div v-if="arraySteps[currentStep]">
                                        <div :class="`grid gap-4  md:grid-cols-${arraySteps[currentStep]?.column}`">
                                            <div class="flex flex-2"
                                                v-for="(field, i) in arraySteps[currentStep].fields" :key="i">
                                                <Input v-if="[ 'text', 'email', 'hidden', ].includes(field.type)"
                                                    :label="field.label" :type="field.type"
                                                    v-model="form[`${arraySteps[currentStep].key}__${field.name}`]"
                                                    :name="form[`${arraySteps[currentStep].key}__${field.name}`]"
                                                    :id="`${field.name}_${i}`" :hint="field.hint"
                                                    :required="field.required":format="field?.format"
                                                    :disabled="field.disabled" :readonly="field.readonly"/>
                                                <Number v-if="field.type == 'number'" :key="field.name"
                                                    :label="field.label" :type="field.type"
                                                    v-model="form[`${arraySteps[currentStep].key}__${field.name}`]"
                                                    :name="form[`${arraySteps[currentStep].key}__${field.name}`]"
                                                    :id="`${field.name}_${i}`" :hint="field.hint"
                                                    :required="field.required" :min="field?.min" :max="field?.max"
                                                    :step="field?.step" :format="field?.format"
                                                    :disabled="field.disabled" :readonly="field.readonly" />
                                                <Select v-if="field.type == 'select'" :key="field.name"
                                                    :label="field.label" :type="field.type"
                                                    v-model="form[`${arraySteps[currentStep].key}__${field.name}`]"
                                                    :name="form[`${arraySteps[currentStep].key}__${field.name}`]"
                                                    :id="`${field.name}_${i}`" :hint="field.hint"
                                                    :required="field.required" :options="field.options"
                                                    :disabled="field.disabled" :readonly="field.readonly"
                                                    :multiple="field.multiple" />
                                                <Currency v-if="field.type == 'currency'" :key="field.name"
                                                    :label="field.label" :type="field.type"
                                                    v-model="form[`${arraySteps[currentStep].key}__${field.name}`]"
                                                    :name="form[`${arraySteps[currentStep].key}__${field.name}`]"
                                                    :id="`${field.name}_${i}`" :hint="field.hint"
                                                    :required="field.required" :format="field?.format"
                                                    :disabled="field.disabled" :readonly="field.readonly" />
                                                <Date v-if="field.type == 'date'" :key="field.name"  
                                                    :label="field.label" :type="field.type"
                                                    v-model="form[`${arraySteps[currentStep].key}__${field.name}`]"
                                                    :name="form[`${arraySteps[currentStep].key}__${field.name}`]"
                                                    :id="`${field.name}_${i}`" :hint="field.hint"
                                                    :required="field.required" :min="field?.min" :max="field?.max"
                                                    :disabled="field.disabled" :readonly="field.readonly" />
                                                <Textarea v-if="field.type == 'textarea'" :key="field.name"
                                                    :label="field.label" :type="field.type"
                                                    v-model="form[`${arraySteps[currentStep].key}__${field.name}`]"
                                                    :name="form[`${arraySteps[currentStep].key}__${field.name}`]"
                                                    :id="`${field.name}_${i}`" :hint="field.hint"
                                                    :required="field.required" :disabled="field.disabled"
                                                    :readonly="field.readonly" />
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
                                            </div>
                                        </div>
                                        <div class="mt-6">
                                            <template v-if="arraySteps[currentStep].key == 'step_2' && arraySteps[currentStep].type == 'evaluation'">
                                                <Button type="button"
                                                    class="border-secondary bg-blue-500 text-white mr-2"
                                                    variant="primary" @click="previewScoring">Previu Penilaian</Button>
                                            </template>
                                            <template v-else-if="arraySteps[currentStep].key == 'step_3' && arraySteps[currentStep].type == 'evaluation'">
                                                <Button type="button"
                                                    class="border-secondary bg-blue-500 text-white mr-2"
                                                    variant="primary" @click="previewHarga">Previu Penilaian</Button>
                                            </template>
                                            <Button class="border-primary/80 bg-blue-500 text-white"
                                                variant="primary" type="submit">Simpan</Button>
                                        </div>

                                        <div class="mt-6"
                                            v-if="scores.rows != undefined && arraySteps[currentStep].key == 'step_2'">
                                            <Card class="p-4">
                                                <ScoreTree :tree="scores.rows" :final-score="scores.final_score" />
                                            </Card>
                                        </div>
                                        <div class="mt-6"
                                            v-else-if="harga.score > 0 && arraySteps[currentStep].key == 'step_3'">
                                            <div class="bg-white rounded-lg border border-gray-200 p-4 space-y-2">
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">Harga Terendah <span
                                                            class="text-xs text-red-500 italic"> Harga terendah dari
                                                            harga yang sudah disimpan di semua penawaran saat
                                                            ini.</span></span>
                                                    <span class="font-medium text-gray-900">
                                                        {{ harga.harga_terendah }}
                                                    </span>
                                                </div>

                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">Harga Penawaran</span>
                                                    <span class="font-medium text-gray-900">
                                                        {{ harga.harga_penawaran }}
                                                    </span>
                                                </div>

                                                <div class="flex justify-between text-sm border-t pt-2">
                                                    <span class="text-gray-600 font-medium">Skor</span>
                                                    <span class="font-semibold text-indigo-600">
                                                        {{ harga.score }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Actions -->
                                        <!-- <div class="mt-2 flex justify-end gap-2">
                                            <Button type="button" class="border-primary/20" variant="secondary"
                                                @click="close">Batal</Button>
                                            <Button class="border-primary/80 bg-blue-500 text-white"
                                                variant="secondary" type="submit">Simpan</Button>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

<style scoped lang="css">
.hidden {
    width: 0px;
    height: 0px;
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

} from "@/components/ui/form";
import { Card, CardTitle, CardContent, CardHeader } from "@/components/ui/card";
import ScoreTree from "./score/ScoreTree.vue";
import { Button } from "./ui/button";
import { reactive } from "vue";
import { data } from "autoprefixer";

export default {
    name: "StepsDialog",
    inheritAttrs: false,
    props: {
        open: Boolean,
        title: { type: String, default: "Form" },
        steps: { type: Object, default: () => ({}) },
        formData: { type: Object, default: () => ({}) },
        dialog: { type: Object, default: () => ({ width: 2 }) },
        current: { type: Number, default: 1 }
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
        ScoreTree
    },
    data() {
        return {
            form: {},
            rowCount: {},
            isClosing: false,
            currentStep: 0,
            scores: {},
            harga: {},
        };
    },
    computed: {
        dialogState() {
            return this.open && !this.isClosing ? "open" : "closed";
        },
        arraySteps() {
            return Object.keys(this.steps)
                .sort((a, b) => {
                    const na = parseInt(a.replace('step_', ''))
                    const nb = parseInt(b.replace('step_', ''))
                    return na - nb
                })
                .map((key, index) => {
                    const step = this.steps[key]

                    return {
                        index,
                        key,
                        method: step.method,
                        column: step.column??2,
                        type: step.type,
                        title: step.title || step.label || `Step ${index + 1}`,
                        fields: step.fields || [],
                    }
                })
        },
    },
    watch: {
        formData(newVal) {
            if (newVal !== undefined) {
                Object.assign(this.form, newVal);
            }

            if (newVal && Object.keys(newVal).length === 0) {
                this.form = reactive({
                    addtable: {},
                });
            }
        },
        open(newVal) {
            if (newVal && this.sections) {
                this.isClosing = false;
            }
        },
        current: {
            handler(newVal) {
                this.currentStep = newVal;
            },
            immediate: true
        }
    },
    methods: {
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
            this.form.current_step = this.currentStep;
            this.$emit("onSubmit", this.form);
            this.scores = {};
            this.harga = {
                harga_terendah: 0,
                harga_penawaran: 0,
                score: 0
            }
        },
        circleClass(index) {
            if (index < this.currentStep) {
                return 'bg-green-700 border-green-600 text-white'
            }

            if (index === this.currentStep) {
                return 'bg-white border-blue-600 text-blue-600'
            }

            return 'bg-white border-gray-300 text-gray-400'
        },

        labelClass(index) {
            if (index === this.currentStep) {
                return 'text-blue-600 font-medium'
            }

            if (index < this.currentStep) {
                return 'text-green-700'
            }

            return 'text-gray-400'
        },

        lineClass(index) {
            return index < this.currentStep
                ? 'bg-green-700'
                : 'bg-gray-300'
        },

        nextStep() {
            this.currentStep = Math.min(
                this.arraySteps.length - 1,
                this.currentStep + 1
            )
        },

        prevStep() {
            this.currentStep = Math.max(0, this.currentStep - 1)
        },
        stepIndex(step) {
            return Number(step.key.split('_')[1])
        },
        goToStep(index) {
            // optional: batasi lompat ke depan
            if (index <= this.currentStep + 1) {
                this.currentStep = index
            }
            this.previewScoring()
        },
        format(val) {
            if (typeof val !== 'number') return '-'
            return val.toFixed(2)
        },

        async previewScoring() {
            await this.$store
                .dispatch("procurementBid/previewScoring", this.form)
                .then(({ data }) => {
                    this.scores = data.data
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        async previewHarga() {
            await this.$store
                .dispatch("procurementBid/previewHarga", this.form)
                .then(({ data }) => {
                    this.harga = data.data
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    },
    mounted() {
        this.scores = {
            rows: undefined
        }
        this.harga = {
            harga_terendah: 0,
            harga_penawaran: 0,
            score: 0
        }
    }
};
</script>
