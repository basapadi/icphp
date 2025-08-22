<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";
import Label from "./Label.vue";

const props = defineProps<{
    defaultValue?: string | number;
    modelValue?: string | number;
    label: string;
    type?: string;
    name?: string;
    id?: string;
    hint?: any;
    required?: boolean;
    class?: HTMLAttributes["class"];
}>();

const emits = defineEmits<{
    (e: "update:modelValue", payload: string | number): void;
}>();

const modelValue = useVModel(props, "modelValue", emits, {
    passive: true,
    defaultValue: props.defaultValue,
});
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <Label :for="id">
            {{ label }}
            <span v-if="required" class="text-red-800"> *</span>
        </Label>
        <input
            :id="id"
            v-model="modelValue"
            :type="type"
            :name="name"
            :hint="hint"
            :required="required"
            :class="
                cn(
                    'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-foreground file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                    props.class
                )
            "
        />
    </div>
</template>
