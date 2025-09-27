<script lang="ts">
import type { HTMLAttributes } from "vue";
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";

export default {
  name: "Curerncy",
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
    class: { type: [String, Array, Object] as unknown as HTMLAttributes["class"], default: "" },
    min: { type: [String, Number], default: undefined },
    max: { type: [String, Number], default: undefined },
    step: { type: [String, Number], default: 1 },
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
    format: {type: String, default: ''}
  },
  emits: ["update:modelValue"],
  setup(props, { emit }) {
    const modelValue = useVModel(props, "modelValue", emit, {
      passive: true,
      defaultValue: props.defaultValue,
    });

    // Format angka ke string dengan ribuan
    const formatNumber = (value: string | number) => {
      if (value === "" || value === null) return "";
      const number = typeof value === "number" ? value : parseInt(value.toString().replace(/\D/g, ""), 10);
      if (isNaN(number)) return "";
      return number.toLocaleString("id-ID");
    };

    // Tangani input user
    const onInput = (e: Event) => {
      const input = e.target as HTMLInputElement;
      // Hapus semua karakter non-digit
      const numericValue = input.value.replace(/\D/g, "");
      // Update modelValue dengan angka mentah
      modelValue.value = numericValue ? parseInt(numericValue, 10) : "";
      // Format untuk ditampilkan di input
      input.value = formatNumber(numericValue);
    };

    return { props, modelValue, cn, formatNumber, onInput };
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

      <div class="relative group">
        <Info class="h-4 w-4 text-orange-300 cursor-pointer" />
        <div style="z-index:9999" class="absolute left-full ml-2 top-1/2 -translate-y-1/2 w-48 p-2 text-xs 
                bg-orange-500 text-white rounded shadow-lg opacity-0 
                group-hover:opacity-100 transition-opacity duration-200 pointer-events-none"
        >
          {{ hint }}
        </div>
      </div>
    </Label>
    <div class="relative w-full">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-orange-400 pointer-events-none">IDR</span>
        <input
        :id="id"
        :value="formatNumber(modelValue)"
        @input="onInput"
        :type="type"
        :name="name"
        :required="required"
        @invalid="e => e.target.setCustomValidity(`${label} tidak boleh kosong atau tidak sesuai format`)"
        :min="min ?? null"
        :max="max ?? null"
        :step="step ?? 1"
        :pattern="props.format || null"
        :disabled="disabled"
        :readonly="readonly"
        :class="
            cn(
            'flex h-8 w-full font-mono text-sm rounded-md border border-input bg-background px-10 py-2 ring-offset-background file:border-0 file:bg-transparent file:text-foreground file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
            props.class
            )
        "
        />
    </div>
  </div>
</template>
