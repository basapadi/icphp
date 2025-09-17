<script lang="ts">
import type { HTMLAttributes } from "vue";
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";
import {Info} from "lucide-vue-next";

export default {
  name: "Text",
  components: { Label,Info },
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
    format: { type: String, default: "" },
    disabled: {type: Boolean, default: false},
    readonly: { type: Boolean, default: false }
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
            target.setCustomValidity("Data tidak boleh kosong atau format tidak sesuai");
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
      "flex h-8 w-full text-sm rounded-md border border-input bg-background px-3 py-2 ring-offset-background file:border-0 file:bg-transparent file:text-foreground file:text-sm file:font-medium placeholder-gray-400 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50",
      props.class
    );

    return { props, modelValue, handleInput, inputClass };
  }
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

        <!-- Tooltip -->
        <div style="z-index:9999" class="absolute left-full ml-2 top-1/2 -translate-y-1/2 w-48 p-2 text-xs 
                bg-orange-500 text-white rounded shadow-lg opacity-0 
                group-hover:opacity-100 transition-opacity duration-200 pointer-events-none"
        >
          {{hint}}
        </div>
      </div>
    </Label>
    <input
      :id="id"
      v-model="modelValue"
      :type="type"
      :name="name"
      :required="required"
      :pattern="props.format || undefined"
      @invalid="e => e.target.setCustomValidity(`${label} tidak boleh kosong atau tidak sesuai format`)"
      @input="handleInput"
      :class="inputClass"
      :disabled="disabled"
      :readonly="readonly"
    />
  </div>
</template>
