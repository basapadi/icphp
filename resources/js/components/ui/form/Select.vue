<template>
  <div class="">
    <Label :for="id" class="text-sm font-medium">
      <span class="text-gray-500 text-shadow-2xs">{{ label }}</span> <span v-if="required" class="text-red-600">*</span>
    </Label>

    <!-- MULTIPLE SELECT -->
    <div v-if="multiple">
    <div
        class="flex flex-wrap gap-1 border border-gray-300 rounded-md p-1 cursor-text"
        @click="$refs.input.focus()"
    >
        <!-- Chips -->
        <template v-for="(item, index) in modelValue" :key="index">
        <span class="flex items-center bg-orange-100 text-orange-800 rounded-full px-3 py-0.5 text-sm">
            {{ options[item] }}
            <button type="button" class="ml-1 text-orange-600 hover:text-orange-900" @click.stop="removeItem(index)">
            &times;
            </button>
        </span>
        </template>

        <!-- Input untuk search -->
        <input
        v-model="search"
        ref="input"
        type="text"
        placeholder="Pilih..."
        class="flex-1 border-none px-2 focus:ring-0 outline-none text-sm"
        @focus="open = true"
        />
    </div>

    <!-- Hidden input agar required jalan -->
    <input
        v-if="required"
        type="hidden"
        :name="name"
        :value="modelValue.length ? modelValue.join(',') : ''"
        required
        @invalid="e => e.target.setCustomValidity(`Pilihan ${label} tidak boleh kosong`)"
        @input="e => e.target.setCustomValidity('')"
    />

    <!-- Dropdown list -->
    <ul
        v-if="open"
        class="border border-gray-300 max-h-60 overflow-auto rounded-md bg-white shadow-lg z-10"
    >
        <li
        v-for="(o, i) in filteredOptions"
        :key="i"
        class="px-3 py-1 hover:bg-gray-200 cursor-pointer"
        @click="addItem(i)"
        >
        {{ o }}
        </li>
    </ul>
    </div>

    <!-- SINGLE SELECT -->
    <select
      v-else
      :id="id"
      :value="modelValue"
      :name="name"
      :required="required"
      :class="computeClass"
      @invalid="e => e.target.setCustomValidity(`Pilihan ${label} tidak boleh kosong`)"
      @input="setInput"
    >
      <option value="">-- Pilih --</option>
      <option v-for="(o, i) in options" :value="i" :key="i">{{ o }}</option>
    </select>

    <p v-if="hint" class="text-xs text-gray-500 italic mt-1">{{ hint }}</p>
  </div>
</template>

<script>
import Label from "@/components/ui/Label.vue";
export default {
  components: { Label },
  props: {
    modelValue: { type: [String, Array], default: '' },
    options: { type: Object, default: () => ({}) },
    label: { type: String, default: '' },
    required: { type: Boolean, default: false },
    id: { type: String, default: '' },
    name: { type: String, default: '' },
    hint: { type: String, default: '' },
    multiple: { type: Boolean, default: false },
    class: { type: String, default: '' },
  },
  data() {
    return {
      search: '',
      open: false,
      filteredOptions: [],
    };
  },
  watch: {
    search() {
      this.filterOptions();
    },
  },
  mounted() {
    this.filteredOptions = Object.values(this.options);
    document.addEventListener('click', this.handleClickOutside);
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside);
  },
  computed: {
    computeClass() {
      return `flex h-8 w-full rounded-md border border-gray-300 bg-white px-3 text-sm ${this.class}`;
    },
  },
  methods: {
    handleClickOutside(event) {
        // Cek elemen komponen
        if (!this.$el.contains(event.target)) {
        this.open = false;
        }
    },
    addItem(index) {
      const key = Object.keys(this.options)[index];
      if (!this.modelValue.includes(key)) {
        this.$emit('update:modelValue', [...this.modelValue, key]);
      }
      this.search = '';
      this.open = false;
      this.filterOptions();
    },
    removeItem(index) {
      const newVal = [...this.modelValue];
      newVal.splice(index, 1);
      this.$emit('update:modelValue', newVal);
    },
    filterOptions() {
      const selectedKeys = new Set(this.modelValue);
      this.filteredOptions = Object.values(this.options).filter(
        (v, i) => !selectedKeys.has(Object.keys(this.options)[i]) &&
                   v.toLowerCase().includes(this.search.toLowerCase())
      );
      this.open = this.filteredOptions.length > 0;
    },
    setInput(e){
        e.target.setCustomValidity('')
        this.$emit('update:modelValue', e.target.value)
       
    }
  },
};
</script>
