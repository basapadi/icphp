<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Table Header -->
    <div class="px-4 py-1 border-b border-gray-200">
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
          <button 
            @click="tambahData"
            class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
          >
            Tambah {{ title }}
          </button>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <input 
                v-model="selectAll"
                @change="toggleSelectAll"
                type="checkbox" 
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
              />
            </th>
            <th v-for="column in columns" class="px-4 py-2 text-left font-bold text-xs text-gray-500 uppercase tracking-wider" :key="column.value">{{ column.label }}</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr 
            v-for="data in filterData" 
            :key="data.id" 
            class="hover:bg-gray-50 transition-colors"
          >
            <td class="px-4 whitespace-nowrap">
              <input 
                v-model="selectedData"
                :value="data.id"
                type="checkbox" 
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
              />
            </td>
            <td v-for="column in columns" :key="column.value" class="px-2 py-2 whitespace-nowrap">
              <template v-if="column.name == 'actions'">
                <td class="px-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-2">
                    <template v-if="column.options.includes('edit')">
                      <button 
                        @click="editData(data)"
                        class="text-blue-600 hover:text-blue-900 outline-blue-500/50 outline-2 rounded-xs px-2"
                      >
                        Edit
                      </button>
                    </template>
                    <template v-if="column.options.includes('delete')">
                      <button 
                        @click="hapusData(data.id)"
                        class="text-red-600 hover:text-red-900 outline-red-500/50 outline-2 rounded-xs px-2"
                      >
                        Delete
                      </button>
                    </template>
                  </div>
                </td>
              </template>
              <template v-else>
                <span class="text-sm text-gray-600">{{ data[column.name] }}</span>
              </template>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Table Footer -->
    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Data <span class="font-medium">{{ (currentPage * itemsPerPage) - (itemsPerPage -1) }}</span> hingga 
          <span class="font-medium">{{ rows?.length }}</span> dari 
          <span class="font-medium">{{ total }}</span> hasil
        </div>
        <div class="flex items-center space-x-2">
          <button
            @click="previousPage"
            :disabled="currentPage === 1"
            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Sebelumnya
          </button>
          <button class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
            {{ currentPage }}
          </button>
          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Selanjutnya
          </button>
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
    urlGrid: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      searchQuery: '',
      selectAll: false,
      selectedData: [],
      currentPage: 1,
      itemsPerPage: 10,
      total: 0,
      rows: [],
      columns: []
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
      await this.$store.dispatch(this.urlGrid, { q: this.searchQuery, _limit: this.itemsPerPage, _page: this.currentPage }).then(({ data }) => {
        data = data.data
        this.rows = data.rows
        this.columns = data.columns
        this.total = data.total
      })
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString()
    },
    toggleSelectAll() {
      if (this.selectAll) {
        this.selectedData = this.filterData.map(user => user.id)
      } else {
        this.selectedData = []
      }
    },
    tambahData() {
      console.log('Add user clicked')
    },
    editData(user) {
      console.log('Edit user:', user)
    },
    hapusData(userId) {
      console.log('Delete user:', userId)
    },
    previousPage() {
      if (this.currentPage > 1) this.currentPage--
    },
    nextPage() {
      if (this.currentPage < this.totalPages) this.currentPage++
    }
  }
}
</script>
