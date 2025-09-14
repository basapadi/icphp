<template>
  <div class="flex flex-col gap-1.5">
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
          :id="id"
          placeholder="Pilih..."
          class="flex-1 border-none px-2 focus:ring-0 outline-none text-sm"
          autocomplete="off"
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
          class="py-1 text-sm text-gray-600  border-b border-gray-200 hover:bg-gray-3 00 cursor-pointer"
          @click="addItem(i)"
        >
          {{ o }}
        </li>
      </ul>
    </div>

    <!-- Single select now uses searchable input instead of HTML select -->
    <!-- SINGLE SELECT (SEARCHABLE) -->
    <div v-else class="relative">
      <!-- Input field for single select -->
      <input
        v-model="displayValue"
        ref="singleInput"
        type="text"
        :id="id"
        :placeholder="modelValue ? getSelectedLabel() : '-- Pilih --'"
        :class="computeClass"
        :disabled="disabled"
        :readonly="readonly"
        autocomplete="off"
        @focus="openSingleSelect"
        @input="onSingleSearch"
        @keydown.escape="open = false"
        @keydown.enter.prevent="selectFirstOption"
        @keydown.arrow-down.prevent="navigateDown"
        @keydown.arrow-up.prevent="navigateUp"
      />

      <!-- Hidden input for form submission -->
      <input
        v-if="required"
        type="hidden"
        :name="name"
        :value="modelValue"
        required
        @invalid="e => e.target.setCustomValidity(`${label} tidak boleh kosong`)"
        @input="e => e.target.setCustomValidity('')"
      />

      <!-- Dropdown arrow -->
      <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
      </div>

      <!-- Dropdown list for single select -->
      <ul
        v-if="open"
        class="absolute z-10 w-full border border-gray-300 max-h-60 overflow-auto rounded-md bg-white shadow-lg mt-1"
      >
        <li
          v-if="!required"
          class="px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 cursor-pointer"
          :class="{ 'bg-blue-100': highlightedIndex === -1 }"
          @click="selectSingleOption('', '')"
        >
          -- Pilih --
        </li>
        <li
          v-for="(option, index) in filteredSingleOptions"
          :key="option.key"
          class="px-4 py-1 text-sm text-gray-600  border-b border-gray-200 hover:bg-gray-200 cursor-pointer"
          :class="{ 'bg-blue-100': highlightedIndex === index }"
          @click="selectSingleOption(option.key, option.value)"
        >
          {{ option.value }}
        </li>
        <li v-if="filteredSingleOptions.length === 0" class="px-3 py-2 text-gray-500 italic">
          Tidak ada pilihan yang cocok
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import Label from "@/components/ui/Label.vue";
import {Info} from "lucide-vue-next";
export default {
  components: { Label,Info },
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
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false }
  },
  data() {
    return {
      search: '',
      displayValue: '', // Added for single select search input
      open: false,
      filteredOptions: [],
      filteredSingleOptions: [], // Added for single select filtering
      highlightedIndex: -1, // Added for keyboard navigation
    };
  },
  watch: {
    search() {
      this.filterOptions();
    },
    displayValue() {
      if (!this.multiple) {
        this.filterSingleOptions();
      }
    },
    modelValue() {
      if (!this.multiple) {
        this.displayValue = this.getSelectedLabel();
      }
    }
  },
  mounted() {
    this.filteredOptions = Object.values(this.options);
    this.filteredSingleOptions = this.getOptionsArray();
    if (!this.multiple) {
      this.displayValue = this.getSelectedLabel();
    }
    document.addEventListener('click', this.handleClickOutside);
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside);
  },
  computed: {
    computeClass() {
      return `flex h-8 w-full rounded-md border border-gray-300 bg-white px-3 text-sm pr-8 ${this.class}`;
    },
  },
  methods: {
    handleClickOutside(event) {
      // Cek elemen komponen
      if (!this.$el.contains(event.target)) {
        this.open = false;
        this.highlightedIndex = -1; // Reset highlight on close
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
    getSelectedLabel() {
      return this.modelValue && this.options[this.modelValue] ? this.options[this.modelValue] : '';
    },
    getOptionsArray() {
      return Object.entries(this.options).map(([key, value]) => ({ key, value }));
    },
    openSingleSelect() {
      if (!this.disabled && !this.readonly) {
        this.open = true;
        this.displayValue = '';
        this.filterSingleOptions();
        this.highlightedIndex = -1;
      }
    },
    onSingleSearch() {
      this.open = true;
      this.filterSingleOptions();
      this.highlightedIndex = -1;
    },
    filterSingleOptions() {
      const searchTerm = this.displayValue.toLowerCase();
      if (!searchTerm || searchTerm === this.getSelectedLabel().toLowerCase()) {
        this.filteredSingleOptions = this.getOptionsArray();
      } else {
        this.filteredSingleOptions = this.getOptionsArray().filter(option =>
          option.value.toLowerCase().includes(searchTerm)
        );
      }
    },
    selectSingleOption(key, value) {
      this.$emit('update:modelValue', key);
      this.displayValue = value;
      this.open = false;
      this.highlightedIndex = -1;
    },
    selectFirstOption() {
      if (this.filteredSingleOptions.length > 0) {
        const option = this.filteredSingleOptions[0];
        this.selectSingleOption(option.key, option.value);
      }
    },
    navigateDown() {
      if (this.open) {
        this.highlightedIndex = Math.min(this.highlightedIndex + 1, this.filteredSingleOptions.length - 1);
      }
    },
    navigateUp() {
      if (this.open) {
        this.highlightedIndex = Math.max(this.highlightedIndex - 1, -1);
      }
    },
    setInput(e) {
      e.target.setCustomValidity('');
      this.$emit('update:modelValue', e.target.value);
    }
  },
};
</script>
