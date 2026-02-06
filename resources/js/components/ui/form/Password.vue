<script lang="ts">
import type { HTMLAttributes } from "vue";
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";
import { ref } from "vue";

export default {
    name: "Password",
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

        const showPassword = ref(false);

        function togglePassword() {
            showPassword.value = !showPassword.value;
        }

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
                } catch {
                    target.setCustomValidity("");
                }
            } else {
                target.setCustomValidity("");
            }

            modelValue.value = value;
        }

        const inputClass = cn(
            "flex h-8 w-full text-sm rounded-md border border-input bg-background pr-10 pl-3 py-2 ring-offset-background file:border-0 file:bg-transparent file:text-foreground file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50",
            props.class
        );

        return {
            props,
            modelValue,
            handleInput,
            inputClass,
            showPassword,
            togglePassword,
        };
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

            <!-- wrapper untuk icon + tooltip -->
            <div class="relative group">
                <Info class="h-4 w-4 text-primary cursor-pointer" />

                <!-- Tooltip -->
                <div
                    style="z-index: 9999"
                    class="absolute left-full ml-2 top-1/2 -translate-y-1/2 w-48 p-2 text-xs bg-primary text-primary-foreground border rounded-md shadow-md opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none"
                >
                    {{ hint }}
                </div>
            </div>
        </Label>

        <div class="relative">
            <input
                :id="id"
                v-model="modelValue"
                :type="showPassword ? 'text' : 'password'"
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

            <button
                type="button"
                @click="togglePassword"
                class="absolute inset-y-0 right-2 flex items-center text-muted-foreground hover:text-foreground"
                tabindex="-1"
            >
                <Eye v-if="!showPassword" class="w-4 h-4" />
                <EyeOff v-else class="w-4 h-4" />
            </button>
        </div>
    </div>
</template>
