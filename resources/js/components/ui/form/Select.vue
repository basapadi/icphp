<template>
    <div class="flex flex-col gap-1.5 w-full">
        <Label
            :for="id"
            class="flex items-center gap-1 text-sm font-medium leading-none"
        >
            <span class="text-muted-foreground text-shadow-2xs">{{
                label
            }}</span>
            <span v-if="required" class="text-destructive">*</span>

            <!-- wrapper untuk icon + tooltip -->
            <div class="relative group">
                <Info class="h-4 w-4 text-primary cursor-pointer" />

                <!-- Tooltip -->
                <div
                    style="z-index: 99999"
                    class="absolute left-full ml-2 top-1/2 -translate-y-1/2 w-60 p-2 text-xs bg-primary text-primary-foreground rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none"
                >
                    {{ hint }}
                </div>
            </div>
        </Label>
        <!-- MULTIPLE SELECT -->
        <div v-if="multiple">
            <div
                class="flex flex-wrap gap-1 border border-border rounded-md p-1 cursor-text"
                @click="$refs.input.focus()"
            >
                <!-- Chips -->
                <template v-for="(item, index) in modelValue" :key="index">
                    <span
                        class="flex items-center bg-primary/20 text-primary rounded-full px-3 py-0.5 text-sm"
                    >
                        {{ options[item] }}
                        <button
                            type="button"
                            class="ml-1 text-primary hover:text-primary/80"
                            @click.stop="removeItem(index)"
                        >
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
                    class="flex-1 border-none px-2 focus:ring-0 outline-none text-sm bg-transparent"
                    autocomplete="off"
                    @focus="openMultipleSelect"
                />
                <button v-if="openFilter != null" type="button" @click="openSelectFilter = !openSelectFilter">
                    <IconListFilter class="w-4 h-4 text-primary" />
                </button>
            </div>

            <!-- Hidden input agar required jalan -->
            <input
                v-if="required"
                type="hidden"
                :name="name"
                :value="modelValue.length ? modelValue.join(',') : ''"
                required
                @invalid="
                    (e) =>
                        e.target.setCustomValidity(
                            `Pilihan ${label} tidak boleh kosong`
                        )
                "
                @input="(e) => e.target.setCustomValidity('')"
            />

            <!-- Dropdown list -->
            <Teleport to="body" v-if="openFilter == null">
                <ul
                    v-if="open"
                    ref="multipleDropdown"
                    class="fixed z-[9999] mt-1 border border-border max-h-60 overflow-auto rounded-md bg-card shadow-lg"
                    :style="{
                        top: `${multipleDropdownPosition.top}px`,
                        left: `${multipleDropdownPosition.left}px`,
                        width: `${multipleDropdownPosition.width}px`,
                    }"
                >
                    <li
                        v-for="o in filteredOptions"
                        :key="o.key"
                        @mousedown.prevent.stop
                        class="px-4 py-1 text-sm text-foreground border-b border-border hover:bg-accent cursor-pointer"
                        @click="addItem(o.key)"
                    >
                        <div class="flex items-center space-x-2">
                            <Cmp class="w-3 h-3 text-primary" />
                            <span class="text-foreground">{{ o.value }}</span>
                        </div>
                    </li>
                </ul>
            </Teleport>
        </div>

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
                @invalid="
                    (e) =>
                        e.target.setCustomValidity(
                            `${label} tidak boleh kosong`
                        )
                "
                @input="(e) => e.target.setCustomValidity('')"
            />

            <!-- Dropdown arrow -->
            <div
                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none"
            >
                <svg
                    class="w-4 h-4 text-muted-foreground"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7"
                    ></path>
                </svg>
            </div>

            <!-- Dropdown list for single select -->
            <Teleport to="body">
                <ul
                    v-if="open && !multiple"
                    ref="singleDropdown"
                    class="fixed z-[9999] mt-1 border border-border max-h-35 overflow-auto rounded-md bg-card shadow-lg"
                    :style="{
                        top: `${dropdownPosition.top}px`,
                        left: `${dropdownPosition.left}px`,
                        width: `${dropdownPosition.width}px`,
                    }"
                >
                    <li
                        v-if="!required"
                        class="px-3 py-2 text-sm text-muted-foreground hover:bg-accent cursor-pointer"
                        :class="{ 'bg-primary/20': highlightedIndex === -1 }"
                        @click="selectSingleOption('', '')"
                    >
                        -- Pilih --
                    </li>
                    <li
                        v-for="(option, index) in filteredSingleOptions"
                        :key="option.key"
                        class="px-4 py-1 text-sm text-foreground border-b border-border hover:bg-accent cursor-pointer"
                        :class="{ 'bg-primary/20': highlightedIndex === index }"
                        @click="selectSingleOption(option.key, option.value)"
                    >
                        <div class="flex items-center space-x-2">
                            <Cmp class="w-3 h-3 text-primary" />
                            <span class="text-foreground">{{
                                option.value
                            }}</span>
                        </div>
                    </li>
                    <li
                        v-if="filteredSingleOptions.length === 0"
                        class="px-3 py-2 text-muted-foreground italic"
                    >
                        Tidak ada pilihan yang cocok
                    </li>
                </ul>
            </Teleport>
        </div>
        <SelectFilter v-if="openSelectFilter" :openFilter="openFilter" @selected="handleSelected"/>
    </div>
</template>

<script>
import Label from "@/components/ui/Label.vue";
import { Component as Cmp, ListFilter as IconListFilter } from "lucide-vue-next";
import SelectFilter from "./components/SelectFilter.vue";
export default {
    components: { Label, Cmp, SelectFilter, IconListFilter },
    props: {
        modelValue: { type: [String, Array, Number], default: "" },
        options: { type: Object, default: () => ({}) },
        label: { type: String, default: "" },
        required: { type: Boolean, default: false },
        id: { type: String, default: "" },
        name: { type: String, default: "" },
        hint: { type: String, default: "" },
        multiple: { type: Boolean, default: false },
        class: { type: String, default: "" },
        disabled: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
        openFilter: { type: Object, default: null },
    },
    data() {
        return {
            search: "",
            displayValue: "",
            open: false,
            filteredOptions: [],
            filteredSingleOptions: [],
            highlightedIndex: -1,
            dropdownDirection: "down", // 'down' | 'up'
            dropdownMaxHeight: 240, // max-h-60 (15rem)
            dropdownPosition: {
                top: 0,
                left: 0,
                width: 0,
            },
            multipleDropdownPosition: {
                top: 0,
                left: 0,
                width: 0,
            },
            openSelectFilter: false,
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
        },
        options: {
            immediate: true,
            deep: true,
            handler() {
                if (this.multiple) {
                    this.search = "";

                    // JANGAN update filteredOptions kalau dropdown sedang ditutup
                    if (this.open) {
                        this.filteredOptions = Object.entries(this.options).map(
                            ([key, value]) => ({ key, value })
                        );
                    }
                }
            }

        },
    },
    mounted() {
        this.filteredOptions = Object.values(this.options);
        this.filteredSingleOptions = this.getOptionsArray();
        if (!this.multiple) {
            this.displayValue = this.getSelectedLabel();
        }

        // pakai pointerdown + capture agar selalu tertangkap
        // document.addEventListener("pointerdown", this.handleClickOutside, true);
        window.addEventListener("mousedown", this.handleClickOutside);
        window.addEventListener("resize", this.calculateSingleDropdownPosition);
        window.addEventListener("scroll", this.calculateSingleDropdownPosition);
    },
    beforeUnmount() {
        // document.removeEventListener(
        //     "pointerdown",
        //     this.handleClickOutside,
        //     true
        // );
        window.removeEventListener("mousedown", this.handleClickOutside);
        window.removeEventListener("resize", this.calculateSingleDropdownPosition);
        window.removeEventListener("scroll", this.calculateSingleDropdownPosition);
    },
    computed: {
        computeClass() {
            return `flex h-8 w-full rounded-md border border-border bg-background px-3 text-sm pr-8 ${this.class}`;
        },
    },
    methods: {
        calculateSingleDropdownPosition() {
            const input = this.$refs.singleInput;
            if (!input) return;

            const rect = input.getBoundingClientRect();

            this.dropdownPosition = {
                width: rect.width,
                left: rect.left,
                top: rect.bottom, // selalu ke bawah
            };
        },
        calculateMultipleDropdownPosition() {
            const input = this.$refs.input;
            if (!input) return;

            const rect = input.getBoundingClientRect();

            this.multipleDropdownPosition = {
                width: rect.width,
                left: rect.left,
                top: rect.bottom, // selalu ke bawah
            };
        },
        handleClickOutside(event) {
            const el = this.$el;
            const singleDrop = this.$refs.singleDropdown;
            const multiDrop = this.$refs.multipleDropdown;

            const clickInsideComponent =
                el.contains(event.target) ||
                (singleDrop && singleDrop.contains(event.target)) ||
                (multiDrop && multiDrop.contains(event.target));

            if (!clickInsideComponent) { 
                this.open = false;
                this.highlightedIndex = -1;

                if (!this.multiple) {
                    this.displayValue = this.getSelectedLabel();
                }
            }
        },
        // addItem(index) {
        //     const key = Object.keys(this.options)[index];
        //     if (!this.modelValue.includes(key)) {
        //         const newValue = [...this.modelValue, key];
        //         this.$emit("update:modelValue", newValue);
        //         this.$emit("change", newValue);
        //     }
        //     this.search = "";
        //     this.open = false;
        //     this.filterOptions();
        // },
        addItem(key) {
            // const key = Object.keys(this.options)[index];
            if (!this.modelValue.includes(key)) {
                const newValue = [...this.modelValue, key];
                this.$emit("update:modelValue", newValue);
                this.$emit("change", newValue);
            }
            this.search = "";
            // this.open = false;
            this.filterOptions();
        },
        removeItem(index) {
            const newVal = [...this.modelValue];
            newVal.splice(index, 1);
            this.$emit("update:modelValue", newVal);
        },
        // filterOptions() {
        //     const selectedKeys = new Set(this.modelValue);
        //     this.filteredOptions = Object.values(this.options).filter(
        //         (v, i) =>
        //             !selectedKeys.has(Object.keys(this.options)[i]) &&
        //             v.toLowerCase().includes(this.search.toLowerCase())
        //     );
        //     this.open = this.filteredOptions.length > 0 || this.search !== "";
        // },
        filterOptions() {
        const selected = new Set(this.modelValue);
            this.filteredOptions = Object.entries(this.options)
                .filter(([key, value]) =>
                !selected.has(key) &&
                value.toLowerCase().includes(this.search.toLowerCase())
                )
                .map(([key, value]) => ({ key, value }));
        },
        getSelectedLabel() {
            return this.modelValue && this.options[this.modelValue]
                ? this.options[this.modelValue]
                : "";
        },
        getOptionsArray() {
            return Object.entries(this.options).map(([key, value]) => ({
                key,
                value,
            }));
        },
        openSingleSelect() {
            if (this.disabled || this.readonly) return;

            this.open = true;
            this.displayValue = "";
            this.filterSingleOptions();
            this.highlightedIndex = -1;

            this.$nextTick(() => {
                this.calculateSingleDropdownPosition();
            });
        },
        openMultipleSelect() {
            if (this.disabled || this.readonly) return;

            this.open = true;
            this.displayValue = "";
            this.filterOptions();
            this.highlightedIndex = -1;

            this.$nextTick(() => {
                this.calculateMultipleDropdownPosition();
            });
        },
        onSingleSearch() {
            this.open = true;
            this.filterSingleOptions();
            this.highlightedIndex = -1;
        },
        filterSingleOptions() {
            const searchTerm = this.displayValue.toLowerCase();
            this.filteredSingleOptions = this.getOptionsArray().filter(
                (option) => option.value.toLowerCase().includes(searchTerm)
            );
        },
        selectSingleOption(key, value) {
            this.$emit("update:modelValue", key);
            this.$emit("change", key);
            this.displayValue = value;
            this.open = false;
            this.highlightedIndex = -1;
        },
        selectFirstOption() {
            if (this.filteredSingleOptions.length > 0) {
                const option = this.filteredSingleOptions[0];
                this.selectSingleOption(option.key, option.value);
            } else if (!this.required) {
                this.selectSingleOption("", "");
            }
        },
        navigateDown() {
            if (this.open) {
                this.highlightedIndex = Math.min(
                    this.highlightedIndex + 1,
                    this.filteredSingleOptions.length - 1
                );
            }
        },
        navigateUp() {
            if (this.open) {
                this.highlightedIndex = Math.max(this.highlightedIndex - 1, -1);
            }
        },
        setInput(e) {
            e.target.setCustomValidity("");
            this.$emit("update:modelValue", e.target.value);
        },

        handleSelected(selected) {
            console.log({selected})
            // this.modelValue = selected.join(',');
            this.$emit("update:modelValue", selected);
            this.displayValue = this.getSelectedLabel();
            // this.openSelectFilter = false;
        }
    },
};
</script>
