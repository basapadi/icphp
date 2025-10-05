<template>
  <div class="flex-1 w-full" v-if="properties.advanceFilter">
    <div class="flex flex-col md:flex-row md:items-center gap-2">
      <!-- Select 1: Kolom -->
      <div class="relative delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103">
        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
          <ListFilter class="h-4 w-4 text-gray-400" />
        </div>
        <select
          id="column"
          v-model="filter.column"
          @change="onChangeColumn"
          class="pl-8 pr-3 py-1.5 text-sm border-1 text-gray-600 transition-colors rounded-md focus:border-transparent"
        >
          <option value="" disabled selected>-- Pilih Kolom --</option>
          <option
            v-for="o in filterColumns"
            :value="o.name"
            :data-data="JSON.stringify(o)"
            :key="o.name"
          >
            {{ o.label }}
          </option>
        </select>
      </div>

      <!-- Select 2: Operator -->
      <div class="relative delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103">
        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
          <ListFilter class="h-4 w-4 text-gray-400" />
        </div>
        <select
          id="operator"
          v-model="filter.operator"
          class="pl-8 w-40 pr-3 py-1.5 text-sm border-1 text-gray-600 transition-colors rounded-md focus:border-transparent"
        >
          <option value="" disabled selected>-- Pilih Operator --</option>
          <option v-for="o in operators" :value="o.value" :key="o.value">{{ o.label }}</option>
        </select>
      </div>

      <!-- Input: value -->
      <template v-if="!['_null','_notnull'].includes(filter.operator)">
        <template v-if="type == 'select'">
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
              <ListFilter class="h-4 w-4 text-gray-400" />
            </div>
            <select
              id="operator"
              v-model="filter.value"
              class="pl-8 pr-3 py-1.5 text-sm border-1 text-gray-600 transition-colors rounded-md focus:border-transparent"
            >
              <option value="" disabled selected>-- Pilih --</option>
              <option v-for="o in options" :value="o.value" :key="o.value">{{ o.label }}</option>
            </select>
          </div>
        </template>

        <!-- Input: date range -->
        <template v-else-if="type == 'date_range'">
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
              <Calendar class="h-4 w-4 text-gray-400" />
            </div>
            <input
              ref="fromDateRef"
              v-model="filter.value_from"
              type="text"
              name="from_date"
              placeholder="Dari Tanggal"
              class="pl-8 pr-3 py-1.5 text-sm border rounded-sm focus:border-transparent w-40"
              readonly
            />
          </div>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
              <Calendar class="h-4 w-4 text-gray-400" />
            </div>
            <input
              ref="toDateRef"
              v-model="filter.value_to"
              type="text"
              name="to_date"
              placeholder="Hingga Tanggal"
              class="pl-8 pr-3 py-1.5 text-sm border rounded-sm focus:border-transparent w-40"
              readonly
            />
          </div>
        </template>

        <!-- Input: text -->
        <template v-else>
          <div
            class="relative delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103"
          >
            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
              <LetterText class="h-4 w-4 text-gray-400" />
            </div>
            <input
              v-model="filter.value"
              type="text"
              placeholder="Nilai pencarian lanjutan"
              class="pl-8 pr-3 py-1.5 text-sm border rounded-sm focus:border-transparent"
            />
          </div>
        </template>
      </template>

      <!-- Tombol Aksi -->
      <button
        class="px-3 py-1.5 bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 transition-colors delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103"
        @click="load()"
      >
        <Funnel class="h-5 w-5 text-orange-500" />
      </button>
      <button
        @click="reset()"
        class="px-3 py-1.5 bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 transition-colors delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103"
      >
        <FunnelX class="h-5 w-5 text-orange-500" />
      </button>
    </div>
  </div>
</template>
<script>
import {
  Search,
  ListFilter,
  Tag,
  Filter,
  LetterText,
  Equal,
  FunnelX,
  Funnel,
  Calendar,
} from "lucide-vue-next";
import filter from "lodash/filter";

// flatpickr
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";
import { Indonesian } from "flatpickr/dist/l10n/id.js";

export default {
  name: "FilterHeader",
  components: {
    Search,
    LetterText,
    Tag,
    Equal,
    ListFilter,
    FunnelX,
    Funnel,
    Calendar,
  },
  props: {
    filter: { type: Object, default: {} },
    columns: { type: Array, default: [] },
    config: { type: Object, default: {} },
    loading: { type: Boolean, default: false },
    pagination: { type: Object, default: {} },
    operators: { type: Array, default: [] },
    properties: { type: Object, default: {} },
  },
  data() {
    return {
      filterColumns: [],
      type: "text",
      options: [],
      fpFrom: null,
      fpTo: null,
    };
  },
  watch: {
    columns(n) {
      this.filterColumns = filter(n, (x) => x.option_filter == true);
    },
    type(newType) {
      if (newType === "date_range") {
        this.$nextTick(() => {
          if (this.$refs.fromDateRef) {
            this.fpFrom = flatpickr(this.$refs.fromDateRef, {
              dateFormat: "d-m-Y",
              locale: Indonesian,
              defaultDate: this.filter.value_from || null,
              onChange: (_, dateStr) => {
                this.filter.value_from = dateStr;
              },
            });
          }
          if (this.$refs.toDateRef) {
            this.fpTo = flatpickr(this.$refs.toDateRef, {
              dateFormat: "d-m-Y",
              locale: Indonesian,
              defaultDate: this.filter.value_to || null,
              onChange: (_, dateStr) => {
                this.filter.value_to = dateStr;
              },
            });
          }
        });
      }
    },
  },
  methods: {
    load() {
      this.$emit("load");
    },
    reset() {
      this.type = "text";
      this.$emit("load", true);
    },
    onChangeColumn(event) {
      this.filter.value = "";
      const selectedOption = event.target.selectedOptions[0];
      if (selectedOption.dataset.data == "undefined") {
        this.type = "text";
      } else {
        const obj = JSON.parse(selectedOption.dataset.data);
        this.type = obj?.type;
        this.options = obj?.options;
      }

      if (this.type == "date_range") {
        this.filter.operator = "_between";
      } else {
        this.filter.operator = "_is";
      }
    },
  },
};
</script>
