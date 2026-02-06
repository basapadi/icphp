<script lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from "vue";
import type { HTMLAttributes } from "vue";
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";

import flatpickr from "flatpickr";
import { Indonesian } from "flatpickr/dist/l10n/id.js";

export default {
    name: "DateTimeInput",
    components: { Label },
    props: {
        defaultValue: { type: String, default: "" }, // format: DD-MM-YYYY
        modelValue: { type: String, default: "" }, // format: DD-MM-YYYY
        label: { type: String, required: true },
        name: { type: String, default: "" },
        id: { type: String, default: "" },
        hint: { type: String, default: "" },
        required: { type: Boolean, default: false },
        class: {
            type: [String, Array, Object] as unknown as HTMLAttributes["class"],
            default: "",
        },
        disabled: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
        minDate: { type: String, default: "" },
        maxDate: { type: String, default: "" },
    },
    emits: ["update:modelValue"],
    setup(props, { emit }) {
        const modelValue = useVModel(props, "modelValue", emit, {
            passive: true,
            defaultValue: props.defaultValue,
        });

        const inputRef = ref<HTMLInputElement | null>(null);
        let picker: flatpickr.Instance | null = null;

        onMounted(() => {
            picker = flatpickr(inputRef.value as HTMLInputElement, {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                locale: Indonesian,
                defaultDate: modelValue.value || undefined,
                minDate: props.minDate || undefined,
                maxDate: props.maxDate || undefined,
                allowInput: true,
                clickOpens: !props.readonly && !props.disabled,
                onChange: (_, dateStr) => {
                    modelValue.value = dateStr;
                },
            });

            if (props.readonly)
                picker._input.setAttribute("readonly", "readonly");
            if (props.disabled)
                picker._input.setAttribute("disabled", "disabled");
        });

        watch(
            () => props.disabled,
            (val) => {
                if (picker) {
                    picker._input.toggleAttribute("disabled", val);
                    picker.set("clickOpens", !val && !props.readonly);
                }
            }
        );

        watch(
            () => props.readonly,
            (val) => {
                if (picker) {
                    picker._input.toggleAttribute("readonly", val);
                    picker.set("clickOpens", !val && !props.disabled);
                }
            }
        );

        onBeforeUnmount(() => {
            if (picker) picker.destroy();
        });

        const inputClass = cn(
            "flex h-8 w-full text-sm rounded-md border border-input bg-background px-3 py-2 ring-offset-background " +
                "file:border-0 file:bg-transparent file:text-foreground file:text-sm file:font-medium " +
                "placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-2 " +
                "disabled:cursor-not-allowed disabled:opacity-50",
            props.class
        );

        return { props, modelValue, inputRef, inputClass };
    },
};
</script>

<template>
    <div class="flex flex-col gap-1.5 w-full">
        <Label
            :for="id"
            class="flex items-center gap-1 text-sm font-medium leading-none"
        >
            <span class="text-muted-foreground">{{ label }}</span>
            <span v-if="required" class="text-destructive">*</span>

            <div class="relative group">
                <Info class="h-4 w-4 text-primary cursor-pointer" />
                <div
                    style="z-index: 9999"
                    class="absolute left-full ml-2 top-1/2 -translate-y-1/2 w-48 p-2 text-xs bg-primary text-primary-foreground border rounded-md shadow-md opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none"
                >
                    {{ hint }}
                </div>
            </div>
        </Label>

        <input
            ref="inputRef"
            :id="id"
            type="text"
            :name="name"
            :required="required"
            v-model="modelValue"
            :class="inputClass"
            :disabled="disabled"
            :readonly="readonly"
            autocomplete="false"
            placeholder="yyyy-mm-dd H:i:s"
        />
    </div>
</template>
