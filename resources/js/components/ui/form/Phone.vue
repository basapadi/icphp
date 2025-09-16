<script lang="ts">
import type { HTMLAttributes } from "vue";
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";

export default {
  name: "Phone",
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
    format: { type: String, default: "" },
    disabled: {type: Boolean, default: false},
    readonly: {type: Boolean, default: false}
  },
  emits: ["update:modelValue"],
  setup(props, { emit }) {
    const modelValue = useVModel(props, "modelValue", emit, {
      passive: true,
      defaultValue: props.defaultValue,
    });

    function handleInput(e: Event) {
      const target = e.target as HTMLInputElement;
      if (props.format) {
        const regex = new RegExp(props.format);
        if (!regex.test(target.value)) {
          target.setCustomValidity("Data tidak boleh kosong atau format tidak sesuai");
        } else {
          target.setCustomValidity("");
        }
      } else {
        target.setCustomValidity("");
      }
      // Update modelValue
      modelValue.value = target.value;
    }

    const inputClass = (propsClass: HTMLAttributes["class"]) =>
      cn(
        'flex h-8 w-full text-sm rounded-md border border-input bg-background px-3 py-2 ring-offset-background file:border-0 file:bg-transparent file:text-foreground file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
        propsClass
      );

    return { props, modelValue, handleInput, inputClass };
  },
};
</script>

<template>
  <div class="flex flex-col gap-1.5 w-full">
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
      :disabled="disabled"
      :readonly="readonly"
      @invalid="e => e.target.setCustomValidity(`${label} tidak boleh kosong atau tidak sesuai format`)"
      @input="handleInput"
      :pattern="props.format || undefined"
      :class="inputClass(props.class)"
    />
  </div>
</template>
