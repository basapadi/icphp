<template>
  <div class="flex flex-col gap-1.5 w-full">
    <Label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" >
      <span class="text-gray-500 text-shadow-2xs">{{ label }}</span>
      <span v-if="required" class="text-red-800"> *</span>
    </Label>

    <div :class="wrapperClass">
        <textarea
          :id="id"
          :name="name || id"
          :value="modelValue"
          :class="computeClass"
          :required="required"
          :disabled="disabled"
          :readonly="readonly"
          @invalid="e => e.target.setCustomValidity(`${label} tidak boleh kosong`)"
          @input.capture="e => e.target.setCustomValidity('')"
          @input="$emit('update:modelValue', $event.target.value)"
        ></textarea>
    </div>
  </div>
</template>

<script>
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";

export default {
  name: "Textarea",
  components: { Label },
  props: {
    modelValue: { type: String, default: null },
    id: { type: String, default: "" },
    name: { type: String, default: "" },
    label: { type: String, default: "" },
    hint: { type: String, default: "" },
    class: { type: String, default: "" },
    required: { type: Boolean, default: false },
    disabled: {type: Boolean, default: false},
    readonly: {type: Boolean, default: false}
  },
  emits: ["update:modelValue"],
  computed: {
    computeClass() {
        return cn(
            "flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50",
            this.class
        );
    },
    wrapperClass() {
      return cn(
        "flex gap-4"
      );
    }
  }
};
</script>
