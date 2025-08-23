<template>
  <div class="flex-1 w-full">
    <div class="flex flex-col md:flex-row md:items-center gap-2">
        <!-- Select 1: Kolom -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                <ListFilter class="h-4 w-4 text-gray-400" />
            </div>
            <select id="column" v-model="filter.column" @change="onChangeColumn" class="pl-8 pr-3 py-1.5 text-sm border-1 text-gray-600 transition-colors rounded-md focus:border-transparent" >
                <option value="" disabled selected>-- Pilih Kolom --</option>
                <option v-for="o in filterColumns" :value="o.name" :data-data="JSON.stringify(o)" :key="o.name">{{ o.label }}</option>
            </select>
        </div>
 
        <!-- Select 2: Operator -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                <ListFilter class="h-4 w-4 text-gray-400" />
            </div>
            <select id="operator" v-model="filter.operator" class="pl-8 pr-3 py-1.5 text-sm border-1 text-gray-600 transition-colors rounded-md focus:border-transparent" >
                <option value="" disabled selected>-- Pilih Operator --</option>
                <option v-for="o in operators" :value="o.value" :key="o.value">{{ o.label }}</option>
            </select>
        </div>

        <!-- Input: value -->
        <template v-if="isSelect">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                    <ListFilter class="h-4 w-4 text-gray-400" />
                </div>
                <select id="operator" v-model="filter.value" class="pl-8 pr-3 py-1.5 text-sm border-1 text-gray-600 transition-colors rounded-md focus:border-transparent" >
                    <option value="" disabled selected>-- Pilih --</option>
                    <option v-for="o in options" :value="o.value" :key="o.value">{{ o.label }}</option>
                </select>
            </div>
        </template>
        <template v-else>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                    <LetterText class="h-4 w-4 text-gray-400" />
                </div>
                <input v-model="filter.value" type="text" placeholder="Nilai pencarian lanjutan" class="pl-8 pr-3 py-1.5 text-sm border rounded-sm focus:border-transparent" />
            </div>
        </template>

      <!-- Tombol Aksi -->
        <button class="px-3 py-1.5 bg-orange-100 text-orange-600 rounded-md border-1 hover:bg-orange-200 transition-colors"  @click="load()">
            <Funnel class="h-5 w-5 text-orange-600"/>
        </button>
        <button @click="reset()" class="px-3 py-1.5 bg-orange-100 text-white border-1 rounded-md hover:bg-orange-200 transition-colors">
            <FunnelX class="h-5 w-5 text-orange-600"/>
        </button> 
    </div>
  </div>
</template>
<script>
import { Search, ListFilter, Tag, Filter, LetterText, Equal, FunnelX, Funnel} from "lucide-vue-next"
import _ from 'lodash'
export default {
    name: "FilterHeader",
    components: {
        Search,
        LetterText,
        Tag,
        Equal,
        ListFilter,
        FunnelX,
        Funnel
    },
    props: {
        filter: {
            type: Object,
            default: {},
        },
        columns: {
            type: Array,
            default: [],
        },
        config: {
            type: Object,
            default: {
                upload: false,
                template: false,
                download: false,
                report: false,
            }
        },
        loading: {
            type: Boolean,
            default: false
        },
        pagination: {
            type: Object,
            default: {},
        },
        operators: {
            type: Array,
            default: []
        }

    },
    watch : {
        'columns' (n,o) {
            this.filterColumns = _.filter(n, x => x.option_filter == true)
        }
    },
    data() {
        return {
            filterColumns: [],
            isSelect: false,
            options: []
        }
    },
    methods: {
        load() {
            this.$emit('load')
        },
        reset() {
            this.$emit('load',true)
        },
        onChangeColumn(event) {
            const selectedOption = event.target.selectedOptions[0]
            if (selectedOption.dataset.data == 'undefined') {
                this.isSelect = false;
            } else {
                const obj = JSON.parse(selectedOption.dataset.data)
                if (obj?.type == 'select') {
                    this.isSelect = true;
                    this.options = obj.options
                } else this.isSelect = false
            }
            
        }

    }

}
</script>