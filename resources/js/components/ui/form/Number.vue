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
    type: { type: String, default: "text" },
    name: { type: String, default: "" },
    id: { type: String, default: "" },
    hint: { type: String, default: "" },
    required: { type: Boolean, default: false },
    class: { type: [String, Array, Object] as unknown as HTMLAttributes["class"], default: "" },
    min: { type: [String, Number], default: undefined },
    max: { type: [String, Number], default: undefined },
    step: { type: [String, Number], default: 1 },
    disabled: {type: Boolean, default: false},
    readonly: {type: Boolean, default: false}
  },
  emits: ["update:modelValue"],
  setup(props, { emit }) {
    const modelValue = useVModel(props, "modelValue", emit, {
      passive: true,
      defaultValue: props.defaultValue,
    });

    return { props, modelValue, cn };
  },
};
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
      @invalid="e => e.target.setCustomValidity(`${label} tidak boleh kosong atau tidak sesuai format`)"
      @input="e => e.target.setCustomValidity('')"
      :min="min?? null"
      :max="max?? null"
      :step="step?? 1"
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
