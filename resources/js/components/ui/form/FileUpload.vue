<template>
  <div class="flex flex-col gap-1.5">
    <Label
      :for="id"
      class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
    >
      {{ label }}
      <span v-if="required" class="text-red-800"> *</span>
    </Label>

    <input
      type="file"
      :id="id"
      :name="name"
      :required="required"
      :accept="accept"
      :multiple="multiple"
      :class="computeClass"
      @change="onFileChange"
    />

    <p v-if="hint" class="text-xs text-muted-foreground">{{ hint }}</p>
  </div>
</template>

<script>
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";

export default {
  name: "FileUpload",
  components: { Label },
  props: {
    modelValue: { type: [File, Array, null], default: null },
    id: { type: String, default: "" },
    name: { type: String, default: "" },
    label: { type: String, default: "" },
    hint: { type: String, default: "" },
    class: { type: String, default: "" },
    required: { type: Boolean, default: false },
    accept: { type: String, default: "" }, // contoh: "image/*" atau ".pdf"
    multiple: { type: Boolean, default: false }
  },
  computed: {
    computeClass() {
      return cn(
        "flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-foreground file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50",
        this.class
      );
    }
  },
  methods: {
    onFileChange(e) {
      const files = e.target.files;
      if (this.multiple) {
        this.$emit("update:modelValue", Array.from(files));
      } else {
        this.$emit("update:modelValue", files[0] || null);
      }
    }
  }
};
</script>
