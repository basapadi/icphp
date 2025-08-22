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
              :placeholder="`Cari Data ${title}` "
              class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-transparent"
            />
            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
              <Search class="h-4 w-4 text-gray-400" />
            </div>
          </div>
          <button 
            @click="tambahData"
            class="px-3 py-1.5 text-sm bg-orange-500 text-white rounded-md hover:bg-orange-600 transition-colors" v-if="allowCreate"
          >
            Tambah {{ title }}
          </button>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full border-collapse border border-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <template v-if="properties.multipleSelect">
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-2 border-gray-200">
                <input 
                  v-model="selectAll"
                  @change="toggleSelectAll"
                  type="checkbox" 
                  class="rounded border-gray-300 text-orange-600 focus:ring-orange-500" 
                />
              </th>
            </template>
            <template v-for="column in columns" :key="column.value">
              <template v-if="column.name == 'actions'">
                <th  class="px-4 py-2 text-left font-bold text-xs text-gray-500 uppercase tracking-wider border-2 border-gray-200" style="width: 50px;" >{{ column.label }}</th>
              </template>
              <template v-else>
                <th  :class="`px-4 py-2 text-left font-bold text-xs text-gray-500 uppercase tracking-wider border-2 border-gray-200 text-${column.align}`" :style="`${column.styles}`">{{ column.label }}</th>
              </template>
            </template>
            
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr 
            v-for="(data,i) in filterData" 
            :key="data.id" 
            class="hover:bg-gray-50 transition-colors"
          >
            <template v-if="properties.multipleSelect">
              <td class="px-4 whitespace-nowrap border-2 border-gray-200" style="width: 10px;">
                <input 
                  v-model="selectedData"
                  :value="data.encode_id"
                  type="checkbox" 
                  class="rounded border-gray-300 text-orange-600 focus:ring-orange-500" 
                />
              </td>
            </template>
            <td v-for="column in columns" :key="column.value"  :class="`px-4 py-1 whitespace-nowrap border-2 border-gray-200  text-${column.align}`">
              <template v-if="column.name == 'detail'">
                <div class="items-beetwen space-x-2" :style="`${column.style}`">
                    <button 
                      @click="viewData(data)"
                      class="text-green-800 hover:text-blue-90"
                      style="cursor: pointer;"
                    >
                      <EyeIcon class="h-5 text-green-800" />
                    </button>
                </div>
              </template>
              <template v-if="column.name == 'actions'">
                <div class="flex items-beetwen space-x-2">
                    <template v-if="column.options.includes('edit')">
                      <button 
                        @click="editData(data)"
                        class="text-orange-600 hover:text-blue-90"
                        style="cursor: pointer;"
                      >
                        <PencilSquareIcon class="h-5 text-orange-500" />
                      </button>
                    </template>
                    <template v-if="column.options.includes('delete')">
                      <button 
                        @click="hapusData(data.id)"
                        class="text-red-600 hover:text-red-900 "
                        style="cursor: pointer;"
                      >
                        <TrashIcon class="h-5 text-red-500" />
                      </button>
                    </template>
                  </div>
              </template>
              <template v-else-if="column.type === 'badge'">
                <div :class="`inline-flex items-center rounded-md bg-${data[`color_${column.name}`]}/50 px-2 py-1 text-xs font-medium text-${data[`color_${column.name}`]} inset-ring inset-ring-${data[`color_${column.name}`]}/50`">{{ $helpers.getSubObjectValue(data, column.name) }}</div>
              </template>
              <template v-else>
                <span :class="`text-sm text-gray-600 ${column.class}`" :style="`${column.styles}`">{{ $helpers.getSubObjectValue(data, column.name) }}</span>
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
          <span class="font-medium">{{ ((currentPage -1) * itemsPerPage) + rows?.length }}</span> dari 
          <span class="font-medium">{{ total }}</span> hasil
        </div>
        <div class="flex items-center space-x-2">
          <button
            @click="initPage"
            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
            Awal
          </button>
          <button
            @click="previousPage"
            :disabled="currentPage === 1"
            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Sebelumnya
          </button>
          <button class="px-3 py-1 text-sm bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors">
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
  <FormDialog 
      :open="showDialog" 
      :title="title"
      :fields="form.fields"
      :data="form.data"
      @close="showDialog = false"
      @submit="handleSubmit"
    />
</template>

<script>
import { ref, computed } from 'vue'
import { Search } from 'lucide-vue-next'
import { useStore, mapGetters, mapActions } from 'vuex'
import { TrashIcon, PencilSquareIcon,EyeIcon } from '@heroicons/vue/24/outline'
import { Badge } from '@/components/forms'
import FormDialog from '@/components/FormDialog.vue'

export default {
  components: { Search,TrashIcon,PencilSquareIcon,Badge, FormDialog, EyeIcon },
  props: {
    title: {
      type: String,
      default: 'title'
    },
    store_grid: {
      type: String,
      default: ''
    },
    store_form: {
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
      itemsPerPage: 18,
      total: 0,
      rows: [],
      form: [],
      columns: [],
      properties: {},
      allowCreate: false,
      showDialog: false
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
    ...mapGetters({
      menuRoles: 'menu/getMenuRoles'
    }),
    filterData() {
      if (this.searchQuery) {
        this.currentPage = 1;
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
      await this.$store.dispatch(this.store_grid, { q: this.searchQuery, _limit: this.itemsPerPage, _page: this.currentPage }).then(({ data }) => {
        data = data.data
        this.rows = data.rows
        this.columns = data.columns
        this.total = data.total
        this.properties = data.properties
      })
      this.allowCreate = this.menuRoles.find(role => role.route === this.$route.path)?.create

    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString()
    },
    toggleSelectAll() {
      if (this.selectAll) {
        this.selectedData = this.filterData.map(dt => dt.encode_id)
      } else {
        this.selectedData = []
      }
    },
    async tambahData() {
      await this.$store.dispatch(this.store_form).then(({ data }) => {
        this.form = data.data
      })
      this.showDialog = true
    },
    editData(user) {
      alert('Action Edit disini:', user)
    },
    hapusData(userId) {
      alert('Action Hapus disini:', userId)
    },
    viewData(data) {
      alert('Action view detail data:')
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
