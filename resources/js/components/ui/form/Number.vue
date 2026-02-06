<script lang="ts">
import type { HTMLAttributes } from "vue";
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";

export default {
    name: "Number",
    components: { Label },
    props: {
        defaultValue: { type: [String, Number], default: "" },
        modelValue: { type: [String, Number], default: "" },
        label: { type: String, required: true },
        type: { type: String, default: "number" },
        name: { type: String, default: "" },
        id: { type: String, default: "" },
        hint: { type: String, default: "" },
        required: { type: Boolean, default: false },
        class: {
            type: [String, Array, Object] as unknown as HTMLAttributes["class"],
            default: "",
        },
        min: { type: [String, Number], default: 0 },
        max: { type: [String, Number], default: null },
        step: { type: [String, Number], default: 1 },
        disabled: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
        format: { type: String, default: "" },
    },
    emits: ["update:modelValue"],
    setup(props, { emit }) {
        const modelValue = useVModel(props, "modelValue", emit, {
            passive: true,
            defaultValue: props.defaultValue,
        });

        function handleInput(e: Event) {
            const target = e.target as HTMLInputElement
            let value = target.value

            if (value === "") {
                modelValue.value = ""
                return
            }

            let numeric = Number(value)

            if (Number.isNaN(numeric)) return

            if (props.max !== null && numeric > Number(props.max)) {
                numeric = Number(props.max)
            }

            if (numeric < Number(props.min)) {
                numeric = Number(props.min)
            }

            modelValue.value = numeric
        }


        return { props, modelValue, cn, handleInput };
    }  
};
</script>

<template>
    <div class="flex flex-col gap-1.5 w-full">
        <Label
            :for="id"
            class="flex items-center gap-1 text-sm font-medium leading-none"
        >
            <span class="text-muted-foreground" v-html="label"></span>
            <span v-if="required" class="text-destructive">*</span>

            <!-- wrapper untuk icon + tooltip -->
            <div class="relative group">
                <Info class="h-4 w-4 text-primary cursor-pointer" />

                <!-- Tooltip -->
                <div
                    style="z-index: 9999"
                    class="absolute left-full ml-2 top-1/2 -translate-y-1/2 w-48 p-2 text-xs bg-primary text-primary-foreground border rounded-md shadow-md opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none"
                >
                    <span v-html="hint"></span>
                </div>
            </div>
        </Label>
        <input
            :id="id"
            v-model="modelValue"
            :type="type"
            :name="name"
            :required="required"
            @change="handleInput"
            @keydown="(e) => {  
                if (['e', 'E', '+', '-'].includes(e.key)) e.preventDefault()
            }"
            @invalid="(e) => {
                const target = e.target as HTMLInputElement
                target.setCustomValidity(
                    `${label} tidak boleh kosong atau tidak sesuai format`
                )
            }"
            @input="
                (e) => {
                    const target = e.target as HTMLInputElement
                    target.setCustomValidity('');
                }
            "
            :min="min"
            :max="max"
            :step="step ?? 1"
            :pattern="props.format || undefined"
            :disabled="disabled"
            :readonly="readonly"
            :class="
                cn(
                    'flex h-8 w-full text-sm rounded-md border border-input bg-background px-3 py-2 ring-offset-background file:border-0 file:bg-transparent file:text-foreground file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                    props.class
                )
            "
        />
    </div>
</template>
