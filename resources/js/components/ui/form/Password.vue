<script lang="ts">
import type { HTMLAttributes } from "vue";
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";
import Label from "@/components/ui/Label.vue";
import { ref } from "vue";
import { Eye, EyeOff } from "lucide-vue-next"; // pastikan sudah install lucide-vue-next

export default {
  name: "Password",
  components: { Label, Eye, EyeOff },
  props: {
    defaultValue: { type: [String, Number], default: "" },
    modelValue: { type: [String, Number], default: "" },
    label: { type: String, required: true },
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

    const showPassword = ref(false);

    function togglePassword() {
      showPassword.value = !showPassword.value;
    }

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
        } catch {
          target.setCustomValidity("");
        }
      } else {
        target.setCustomValidity("");
      }

      modelValue.value = value;
    }

    const inputClass = cn(
      "flex h-8 w-full text-sm rounded-md border border-input bg-background pr-10 pl-3 py-2 ring-offset-background file:border-0 file:bg-transparent file:text-foreground file:text-sm file:font-medium placeholder-gray-400 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50",
      props.class
    );

    return { props, modelValue, handleInput, inputClass, showPassword, togglePassword };
  }
};
</script>

<template>
  <div class="flex flex-col gap-1.5 relative w-full">
    <Label :for="id">
      <span class="text-gray-500 text-shadow-2xs">{{ label }}</span>
      <span v-if="required" class="text-red-800"> *</span>
    </Label>

    <div class="relative">
      <input
        :id="id"
        v-model="modelValue"
        :type="showPassword ? 'text' : 'password'"
        :name="name"
        :placeholder="hint"
        :required="required"
        :pattern="props.format || undefined"
        @invalid="e => e.target.setCustomValidity(`${label} tidak boleh kosong atau tidak sesuai format`)"
        @input="handleInput"
        :class="inputClass"
        :disabled="disabled"
        :readonly="readonly"
      />

      <button
        type="button"
        @click="togglePassword"
        class="absolute inset-y-0 right-2 flex items-center text-gray-500 hover:text-gray-700"
        tabindex="-1"
      >
        <Eye v-if="!showPassword" class="w-4 h-4" />
        <EyeOff v-else class="w-4 h-4" />
      </button>
    </div>
  </div>
</template>
