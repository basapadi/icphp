<template>
  <div class="flex flex-col gap-1.5">
    <Label
      :for="id"
      class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
    >
      {{ label }}
      <span v-if="required" class="text-red-800"> *</span>
    </Label>

    <div :class="wrapperClass">
      <label
        v-for="(o, i) in options"
        :key="i"
        class="flex items-center gap-2 text-sm"
      >
        <input
          type="radio"
          :id="`${id}-${i}`"
          :name="name || id"
          :value="i"
          :checked="modelValue === i"
          :required="required"
          :class="computeClass"
          @change="$emit('update:modelValue', i)"
        />
        <span>{{ o }}</span>
      </label>
    </div>
  </div>
</template>

<script>
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";

export default {
  name: "Radio",
  components: { Label },
  props: {
    modelValue: { type: [String, Number], default: null },
    id: { type: String, default: "" },
    name: { type: String, default: "" },
    label: { type: String, default: "" },
    hint: { type: String, default: "" },
    class: { type: String, default: "" },
    required: { type: Boolean, default: false },
    options: { type: Array, default: () => [] },
    direction: { type: String, default: "row" } // "row" | "col"
  },
  computed: {
    computeClass() {
      return cn(
        "h-4 w-4 border border-input rounded-full text-primary focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50",
        this.class
      );
    },
    wrapperClass() {
      return cn(
        "flex gap-4",
        this.direction === "row" ? "flex-row" : "flex-col"
      );
    }
  }
};
</script>
