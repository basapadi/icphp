<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";

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
            <span class="text-gray-500 text-shadow-2xs">{{ label }}</span>
            <span v-if="required" class="text-red-800"> *</span>
        </Label>
        <input
            :id="id"
            v-model="modelValue"
            :type="type"
            :name="name"
            :placeholder="hint"
            :required="required"
            @invalid="e => e.target.setCustomValidity(`Inputan ${label} tidak boleh kosong`)"
            @input="e => e.target.setCustomValidity('')"
            :class="
                cn(
                    'flex h-8 w-full text-sm rounded-md border border-input bg-background px-3 py-2 ring-offset-background file:border-0 file:bg-transparent file:text-foreground file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                    props.class
                )
            "
        />
    </div>
</template>
