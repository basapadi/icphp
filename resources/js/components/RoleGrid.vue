<template>
    <div class="bg-white rounded-lg shadow-sm border-2 border-gray-100">
        <!-- Table Header -->
        <div class="px-4 py-1 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900"></h2>
                <div class="flex items-center space-x-2">
                <div class="relative">
                    <input
                    v-model="searchQuery"
                    type="text"
                    :placeholder="`Cari data ${title}` "
                    class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                    <Search class="h-4 w-4 text-gray-400" />
                    </div>
                </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto p-4">
            <table class="w-full overflow-y-scroll table-auto border-collapse border-2 border-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th  class="px-4 py-2 text-left font-bold text-xs text-gray-500 uppercase tracking-wider border-2 border-gray-200" style="width:50px">No</th>
                    <template v-for="column in columns" :key="column.value">
                        <template v-if="column.name == 'actions'">
                            <th  class="px-4 py-2 text-left font-bold text-xs text-gray-500 uppercase tracking-wider border-2 border-gray-200" style="width: 50px;" >{{ column.label }}</th>
                        </template>
                        <template v-if="['view','create','delete','download'].includes(column.name)">
                            <th  class="px-4 py-2 text-center font-bold text-xs text-gray-500 uppercase tracking-wider border-2 border-gray-200" style="width: 100px;" >{{ column.label }}</th>
                        </template>
                        <template v-else>
                            <th  class="px-4 py-2 text-left font-bold text-xs text-gray-500 uppercase tracking-wider border-2 border-gray-200" >{{ column.label }}</th>
                        </template>
                    </template>
                    
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(data,i) in filterData" :key="data.id"  class="hover:bg-gray-50 transition-colors" >
                    <td class="px-4 py-1 whitespace-nowrap border-2 border-gray-200"><span class="text-sm text-gray-600">{{ i+1 }}</span></td>
                    <template v-for="column in columns" :key="column.value">
                        <td class="px-4 py-1 whitespace-nowrap border-2 border-gray-200 text-center" v-if="['view','create','delete','download'].includes(column.name)">
                            <input :checked="data[column.name]" @click="onCheck($event,data,column.name)" type="checkbox" class="role-cb h-4 w-4 text-orange-600" style="align-items: center;"/>
                        </td>
                        <td class="px-4 py-1 whitespace-nowrap border-2 border-gray-200" v-else><span class="text-sm text-gray-600 ">{{ $helpers.getSubObjectValue(data, column.name) }}</span></td>
                    </template>
                </tr>
                </tbody>
            </table>
    </div>
    </div>
</template>
<script>
import { ref, computed } from 'vue'
import { Search } from 'lucide-vue-next'
import { useStore, mapGetters, mapActions } from 'vuex'

export default {
    components: { Search },
    props: {
        title: {
            type: String,
            default: 'title'
        },
        store: {
            type: String,
            default: ''
        },
        storeSingleUpdate: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            searchQuery: '',
            currentPage: 1,
            total: 0,
            rows: [],
            columns: [],
            properties: {}
        }
    },
    watch: {
        searchQuery: {
        handler() {
            this.load()
        },
        immediate: true // langsung load pertama kali juga
        },
        currentPage() {
            this.load()
        }
    },
    computed: {
        filterData() {
            if (this.searchQuery) {
                this.currentPage = 0;
            }
            return this.rows
        },
    },
    methods: {
        async load() {
            await this.$store.dispatch(this.store, { q: this.searchQuery, _page: this.currentPage, _limit: 100000 }).then(({ data }) => {
                data = data.data
                this.rows = data.rows
                this.columns = data.columns
                this.total = data.total
                this.properties = data.properties
            })
        },
        async onCheck(event, data, column) {
            await this.$store.dispatch(this.storeSingleUpdate, { value: event.target.checked, id: data.encode_id, column }).then(() => {
                this.load()
            })
        }
    }
}
</script>