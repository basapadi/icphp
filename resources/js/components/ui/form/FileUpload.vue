<template>
  <div class="flex flex-col gap-1.5">
    <Label
      :for="id"
      class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
    >
      <span class="text-gray-500 text-shadow-2xs">{{ label }}</span>
      <span v-if="required" class="text-red-800"> *</span>
    </Label>

    <!-- Label custom -->
    <div class="flex items-center gap-2">
      <label :for="id" class="cursor-pointer px-4 py-1 bg-orange-600 text-sm text-white rounded-md hover:bg-orange-700 transition" >
        Pilih File
      </label>

      <!-- Teks file terpilih -->
      <span class="text-sm text-gray-600 italic truncate max-w-[200px]">
        {{ fileLabel }}
      </span>
    </div>

    <ul v-if="hint" class="text-xs text-muted-foreground italic">
     <li>- Ekstensi : {{ extension }}</li>
     <li>- Ukuran file : {{maxsize}} MB</li>
     <li>- Jumlah file : {{maxfile}} file</li>
    </ul>

    <!-- Input file disembunyikan -->
    <input
      ref="fileInput"
      type="file"
      :id="id"
      :name="name"
      :required="required"
      :accept="extension"
      :multiple="multiple"
      class="input-hidden"
      @change="onFileChange"
      @invalid="e => e.target.setCustomValidity(`File ${label} tidak boleh kosong`)"
      @input="e => e.target.setCustomValidity('')"
    />
  </div>
</template>
<style>
.input-hidden {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0,0,0,0);
  border: 0;
}

</style>
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
    extension: { type: String, default: "image/*" },
    multiple: { type: Boolean, default: false },
    maxsize: { type: Number, default: 0 },
    maxfile: { type: Number, default: 0 },
  },
  data() {
    return {
      fileLabel: "Belum ada file"
    };
  },
  methods: {
    onFileChange(e) {
      const files = e.target.files;
      if (this.multiple) {
        this.$emit("update:modelValue", Array.from(files));
        this.fileLabel = files.length
          ? Array.from(files).map(f => f.name).join(", ")
          : "Belum ada file";
      } else {
        this.$emit("update:modelValue", files[0] || null);
        this.fileLabel = files.length ? files[0].name : "Belum ada file";
      }
    }
  }
};
</script>
