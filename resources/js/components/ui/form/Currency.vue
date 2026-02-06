<script lang="ts">
import type { HTMLAttributes } from "vue";
import { ref, computed, watch } from "vue";
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";

const STORAGE_KEY = "currency-input-history";

export default {
    name: "Currency",
    components: { Label },
    props: {
        defaultValue: { type: [String, Number], default: "" },
        modelValue: { type: [String, Number], default: "" },
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
    },
    emits: ["update:modelValue"],
    setup(props, { emit }) {
        const isFocused = ref(false);

        const modelValue = useVModel(props, "modelValue", emit, {
            passive: true,
            defaultValue: props.defaultValue,
        });

        const displayValue = ref("");

        const history = ref<number[]>(
            JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]")
        );

        const formatNumber = (value: number | string) => {
            if (value === "" || value === null) return "";
            const n = Number(value);
            if (Number.isNaN(n)) return "";
            return n.toLocaleString("id-ID");
        };

        watch(
            () => modelValue.value,
            (val) => {
                displayValue.value = formatNumber(val);
            },
            { immediate: true }
        );

        // const onFocus = () => {
        //     isFocused.value = true;
        // };

        // const onBlur = () => {
        //     // delay supaya click suggestion masih kebaca
        //     setTimeout(() => {
        //         isFocused.value = false;
        //     }, 150);
        // };

        const suggestions = computed(() => {
            if (!isFocused.value) return [];

            const raw = displayValue.value.replace(/\D/g, "");
            if (!raw) return [];

            return history.value
                .filter((v) => v.toString().startsWith(raw))
                .slice(0, 5);
        });

        const onInput = (e: Event) => {
            const input = e.target as HTMLInputElement;

            input.setCustomValidity("");

            const raw = input.value.replace(/\D/g, "");
            const numeric = raw ? Number(raw) : "";

            modelValue.value = numeric;
            displayValue.value = formatNumber(raw);
        };

        const selectSuggestion = (value: number) => {
            modelValue.value = value;
            displayValue.value = formatNumber(value);
        };

        watch(
            () => modelValue.value,
            (val) => {
                if (typeof val === "number" && val > 0) {
                    const set = new Set([val, ...history.value]);
                    history.value = Array.from(set).slice(0, 10);
                    localStorage.setItem(
                        STORAGE_KEY,
                        JSON.stringify(history.value)
                    );
                }
            }
        );

        return {
            cn,
            props,
            displayValue,
            suggestions,
            // onFocus,
            // onBlur,
            onInput,
            selectSuggestion,
        };
    },
};
</script>

<template>
    <div class="flex flex-col gap-1.5 w-full">
        <Label :for="id" class="flex items-center gap-1 text-sm font-medium">
            <span class="text-muted-foreground">{{ label }}</span>
            <span v-if="required" class="text-destructive">*</span>
        </Label>

        <div class="relative">
            <span
                class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"
            >
                IDR
            </span>

            <input
                :id="id"
                :name="name"
                :value="displayValue"
                @input="onInput"
                :required="required"
                :disabled="disabled"
                :readonly="readonly"
                class="flex h-8 w-full font-mono text-sm rounded-md border px-10 py-2"
                @invalid="(e) => {
                    const el = e.target as HTMLInputElement
                    el.setCustomValidity(`${label} wajib diisi`)
                }"
            />

            <!-- <ul
                v-if="suggestions.length"
                class="absolute z-50 mt-1 w-full bg-background border rounded-md shadow"
            >
                <li
                    v-for="item in suggestions"
                    :key="item"
                    @click="selectSuggestion(item)"
                    class="px-3 py-1 cursor-pointer hover:bg-muted"
                >
                    {{ item.toLocaleString("id-ID") }}
                </li>
            </ul> -->
        </div>
    </div>
</template>
