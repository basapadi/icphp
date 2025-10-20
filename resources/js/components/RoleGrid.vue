<template>
    <div class="bg-white rounded-xl shadow-sm border-1 border-gray-200">
        <div class="h-screen">
            <!-- Table Header -->
            <div class="px-1 py-1 h-auto border-b border-gray-100">
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
            <div class="overflow-x-auto z-1 h-15/20">
                <table class="min-w-full border border-1 border-dashed border-gray-300">
                    <thead class="bg-orange-50 sticky top-0">
                    <tr>
                        <template v-for="column in columns" :key="column.value">
                            <template v-if="column.name == 'actions'">
                                <th  class="px-4 py-2 text-left font-bold text-xs text-gray-500 uppercase tracking-wider border border-1 border-dashed border-gray-300" style="width: 50px;" >{{ column.label }}</th>
                            </template>
                            <template v-if="['view','create','edit','delete','download'].includes(column.name)">
                                <th  class="px-4 py-2 text-center font-bold text-xs text-gray-500 uppercase tracking-wider border border-1 border-dashed border-gray-300" style="width: 100px;" >{{ column.label }}</th>
                            </template>
                            <template v-else>
                                <th  class="px-4 py-2 text-left font-bold text-xs text-gray-500 uppercase tracking-wider border border-1 border-dashed border-gray-300" >{{ column.label }}</th>
                            </template>
                        </template>
                        
                    </tr>
                    </thead>
                    <tbody class="bg-white">
                    <tr v-for="(data,i) in filterData" :key="data.id"  class="hover:bg-gray-100 transition-colors odd:bg-gray-100 even:bg-white" >
                        <template v-for="column in columns" :key="column.value">
                            <td class="px-4 pt-2 whitespace-nowrap border border-1 border-dashed border-gray-300 text-center" v-if="['view','create','edit','update','delete','download'].includes(column.name)">
                                <template v-if="data.show[column.name]">
                                    <input :checked="data[column.name]" type="checkbox" class="role-cb h-4 w-4 text-orange-600" style="align-items: center;"/>
                                </template>
                            </td>
                            <td class="px-4 whitespace-nowrap border border-1 border-dashed border-gray-300" v-else>
                                <span :class="`text-sm items-center text-gray-600 ${column.class}`">{{ $helpers.getSubObjectValue(data, column.name) }}</span>
                            </td>
                        </template>
                    </tr>
                </tbody>
            </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                    Data <span class="font-medium">{{ (currentPage * itemsPerPage) - (itemsPerPage -1) }}</span> hingga 
                    <span class="font-medium">{{ ((currentPage -1) * itemsPerPage) + rows?.length }}</span> dari 
                    <span class="font-medium">{{ total }}</span> hasil
                    </div>
                    <div class="flex items-center space-x-2">
                        <button
                            @click="initPage"
                            class="bg-orange-50 text-sm border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 px-4 py-1.5"
                            >
                            Awal
                        </button>
                        <button
                            @click="previousPage"
                            :disabled="currentPage === 1"
                            class="bg-orange-50 text-sm border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 px-4 py-1.5"
                        >
                            Sebelumnya
                        </button>
                        <button class="bg-orange-50 text-sm border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 px-4 py-1.5">
                            {{ currentPage }}
                        </button>
                        <button
                            @click="nextPage"
                            :disabled="currentPage === totalPages"
                            class="bg-orange-50 text-sm border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 px-4 py-1.5"
                        >
                            Selanjutnya
                        </button>
                    </div>
                </div>
            </div>
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
        module: {
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
            properties: {},
            itemsPerPage: 30
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
        totalPages() {
            return Math.ceil(this.total / this.itemsPerPage)
        },
        startIndex() {
            return (this.currentPage) * this.itemsPerPage + 1
        },
        endIndex() {
            return Math.min(this.currentPage * this.itemsPerPage, this.filterData.length)
        }
    },
    methods: {
        async load() {
            await this.$store.dispatch(this.module+'/grid', { q: this.searchQuery, _page: this.currentPage, _limit: this.itemsPerPage }).then(({ data }) => {
                data = data.data
                this.rows = data.rows
                this.columns = data.columns
                this.total = data.total
                this.properties = data.properties
            })
        },
        async onCheck(event, data, column) {
            await this.$store.dispatch(this.module+'/singleUpdate', { value: event.target.checked, id: data.encode_id, column }).then(() => {
                this.load()
            })
        },
        previousPage() {
            if (this.currentPage > 1) this.currentPage--
        },
        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++
        },
        initPage() {
            this.currentPage = 1
        }
    }
}
</script>