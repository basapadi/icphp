<script lang="ts">
import type { HTMLAttributes } from "vue";
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";

export default {
    name: "PlainText",
    components: { Label },
    props: {
        defaultValue: { type: [String, Number], default: "" },
        modelValue: { type: [String, Number], default: "" },
        label: { type: String, required: true },
        type: { type: String, default: "text" },
        name: { type: String, default: "" },
        id: { type: String, default: "" },
        hint: { type: String, default: "" },
        required: { type: Boolean, default: false },
        class: {
            type: [String, Array, Object] as unknown as HTMLAttributes["class"],
            default: "",
        },
        format: { type: String, default: "" },
        disabled: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
    },
    emits: ["update:modelValue"],
    setup(props, { emit }) {
        const modelValue = useVModel(props, "modelValue", emit, {
            passive: true,
            defaultValue: props.defaultValue,
        });

        function handleInput(e: Event) {
            const target = e.target as HTMLInputElement;
            const value = target.value;

            if (props.format) {
                try {
                    const regex = new RegExp(props.format);
                    if (!regex.test(value)) {
                        target.setCustomValidity(
                            "Data tidak boleh kosong atau format tidak sesuai"
                        );
                    } else {
                        target.setCustomValidity("");
                    }
                } catch (err) {
                    target.setCustomValidity("");
                }
            } else {
                target.setCustomValidity("");
            }

            modelValue.value = value;
        }

        const inputClass = cn(
            "flex h-8 w-full text-sm rounded-md border border-input bg-background px-3 py-2 ring-offset-background file:border-0 file:bg-transparent file:text-foreground file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50",
            props.class
        );

        return { props, modelValue, handleInput, inputClass };
    },
};
</script>

<template>
    <!-- Kalau hidden, render sederhana -->
    <template v-if="type === 'hidden'">
        <input :id="id" v-model="modelValue" type="hidden" :name="name" />
    </template>

    <!-- Kalau bukan hidden, render normal -->
    <template v-else>
        <div class="flex flex-col gap-1.5 w-full">
            <input
                :id="id"
                v-model="modelValue"
                :type="type"
                :name="name"
                :required="required"
                :pattern="props.format || undefined"
                @invalid="(e) => {
                    const target = e.target as HTMLInputElement
                    target.setCustomValidity(
                        `${label} tidak boleh kosong atau tidak sesuai format`
                    )
                }"
                @input="handleInput"
                :class="inputClass"
                :disabled="disabled"
                :readonly="readonly"
            />
        </div>
    </template>
</template>
