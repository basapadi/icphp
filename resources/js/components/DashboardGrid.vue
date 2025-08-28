<template>
  <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="flex flex-row items-center justify-between p-2">
        <h3 class="text-sm font-semibold text-gray-500">Jatuh Tempo Penerimaan</h3>
        <button class="p-2 hover:bg-gray-100 rounded">
          <MoreHorizontal class="h-4 w-4" />
        </button>
      </div>
      <div class="p-4 pt-2">
        <div class="space-y-2">
          <div 
            v-for="(dt, index) in datas.receive_duedate" 
            :key="index" 
            class="flex items-center justify-between"
          >
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                <span class="text-xs font-medium text-gray-600">
                  {{ getInitials(dt.contact_name) }}
                </span>
              </div>
              <div>
                <p class="text-sm font-bold font-mono text-gray-900">{{ dt.kode_transaksi }}</p>
                <p class="text-xs text-gray-500">{{ dt.contact_name }}</p>
              </div>
            </div>
            <div class="text-left">
              <p class="text-xs text-gray-500">Syarat</p>
              <p class="text-sm font-medium text-gray-900">{{ dt.syarat }}</p>
            </div>
            <div class="text-left">
              <p class="text-xs text-gray-500">Jatuh Tempo</p>
              <p class="text-sm font-medium text-gray-900">{{ dt.tanggal_duedate }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="flex flex-row items-center justify-between p-2">
        <h3 class="text-sm font-semibold text-gray-500">Jatuh Tempo Penjualan</h3>
        <button class="p-2 hover:bg-gray-100 rounded">
          <MoreHorizontal class="h-4 w-4" />
        </button>
      </div>
      <div class="p-4 pt-2">
        <div class="space-y-2">
          <div 
            v-for="(dt, index) in datas.sale_duedate" 
            :key="index" 
            class="flex items-center justify-between"
          >
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                <span class="text-xs font-medium text-gray-600">
                  {{ getInitials(dt.contact_name) }}
                </span>
              </div>
              <div>
                <p class="text-sm font-bold font-mono text-gray-900">{{ dt.kode_transaksi }}</p>
                <p class="text-xs text-gray-500">{{ dt.contact_name }}</p>
              </div>
            </div>
            <div class="text-left">
              <p class="text-xs text-gray-500">Syarat</p>
              <p class="text-sm font-medium text-gray-900">{{ dt.syarat }}</p>
            </div>
            <div class="text-left">
              <p class="text-xs text-gray-500">Jatuh Tempo</p>
              <p class="text-sm font-medium text-gray-900">{{ dt.tanggal_duedate }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="flex flex-row items-center justify-between p-2">
        <h3 class="text-sm font-semibold text-gray-500">Barang paling laku bulan ini</h3>
        <button class="p-2 hover:bg-gray-100 rounded">
          <MoreHorizontal class="h-4 w-4" />
        </button>
      </div>
      <div class="p-4 pt-2">
        <div class="space-y-4">
          <div 
            v-for="(product, index) in datas.top_products" 
            :key="index" 
            class="flex items-center justify-between"
          >
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <span class="text-xs font-bold text-blue-600">#{{ index + 1 }}</span>
              </div>
              <div>
                <p class="text-xs text-gray-500">Barang</p>
                <p class="text-sm font-medium text-gray-900">{{ product.nama_barang }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-xs text-gray-500">Total Penjualan</p>
              <p class="text-sm font-medium text-gray-900">Rp.{{ product.revenue }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Minimum Stock Products -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="flex flex-row items-center justify-between p-2">
        <h3 class="text-sm font-semibold text-gray-500">Barang Hampir Habis</h3>
        <button class="p-2 hover:bg-gray-100 rounded">
          <MoreHorizontal class="h-4 w-4" />
        </button>
      </div>
      <div class="p-4 pt-2">
        <div class="space-y-4">
          <div 
            v-for="(product, index) in datas.minimum_stocks" 
            :key="index" 
            class="flex items-center justify-between"
          >
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <span class="text-xs font-bold text-blue-600">#{{ index + 1 }}</span>
              </div>
              <div>
                <p class="text-xs text-gray-500">Barang</p>
                <p class="text-sm font-medium text-gray-900">{{ product.nama_barang }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-xs text-gray-500">Jlh.Stok</p>
              <p class="text-sm font-bold font-mono text-gray-900">{{ product.stock }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script >
import { ref } from 'vue'
import { MoreHorizontal, ArrowUpRight } from 'lucide-vue-next'

export default {
  name: "DashboardGrid",
  components: {
    MoreHorizontal,
    ArrowUpRight
  },
  props: {
    datas: {
      type: Array,
      default: []
    }
  },
  data() {
    return {
      data: {},
    };
  },
  methods: {
    getInitials(name) {
      return name.split(" ").map(n => n[0]).join("")
    }
  }
}

</script>
