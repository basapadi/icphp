<script lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue";
import type { HTMLAttributes } from "vue";
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";
import { Info } from "lucide-vue-next";

import flatpickr from "flatpickr";
import { Indonesian } from "flatpickr/dist/l10n/id.js";

export default {
  name: "DateInput",
  components: { Label, Info },
  props: {
    defaultValue: { type: String, default: "" }, // format: DD-MM-YYYY
    modelValue: { type: String, default: "" },   // format: DD-MM-YYYY
    label: { type: String, required: true },
    name: { type: String, default: "" },
    id: { type: String, default: "" },
    hint: { type: String, default: "" },
    required: { type: Boolean, default: false },
    class: { type: [String, Array, Object] as unknown as HTMLAttributes["class"], default: "" },
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
    minDate: { type: String, default: "" }, // optional: batas min (DD-MM-YYYY)
    maxDate: { type: String, default: "" }, // optional: batas max (DD-MM-YYYY)
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
        dateFormat: "d-m-Y",  // format DMY
        locale: Indonesian,
        defaultDate: modelValue.value || undefined,
        minDate: props.minDate || undefined,
        maxDate: props.maxDate || undefined,
        allowInput: true,
        onChange: (_, dateStr) => {
          modelValue.value = dateStr;
        },
      });
    });

    onBeforeUnmount(() => {
      if (picker) picker.destroy();
    });

    const inputClass = cn(
      "flex h-8 w-full text-sm rounded-md border border-input bg-background px-3 py-2 ring-offset-background focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50",
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
      <span class="text-gray-500 text-shadow-2xs">{{ label }}</span>
      <span v-if="required" class="text-red-700">*</span>

      <!-- wrapper untuk icon + tooltip -->
      <div class="relative group">
        <Info class="h-4 w-4 text-orange-300 cursor-pointer" />
        <div
          style="z-index:9999"
          class="absolute left-full ml-2 top-1/2 -translate-y-1/2 w-48 p-2 text-xs 
                 bg-orange-500 text-white rounded shadow-lg opacity-0 
                 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none"
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
      placeholder="DD-MM-YYYY"
    />
  </div>
</template>
