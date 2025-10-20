<template>
  <div class="grid grid-cols-3 gap-2 h-[calc(100vh-180px)]">
    <!-- Product Selection -->
    <div class="col-span-2 border border-gray-200 rounded-lg shadow-sm">
      <div class="border-b border-gray-200 rounded-lg p-2">
        <div class="flex gap-2 mb-2">
          <div class="relative flex-1">
            <Search class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 h-3 w-3" />
            <input
              v-model="searchTerm"
              placeholder="Search products..."
              class="pl-7 h-7 text-xs border border-gray-300 rounded w-full px-2"
            />
          </div>
        </div>
        <div class="flex gap-1">
          <button
            v-for="category in categories"
            :key="category"
            @click="selectedCategory = category"
            :class="`h-6 px-2 text-xs border rounded transition-colors ${
              selectedCategory === category 
                ? 'bg-orange-500 text-white border-orange-400' 
                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
            }`"
          >
            {{ category }}
          </button>
        </div>
      </div>

      <div class="p-2 overflow-y-auto h-[calc(100%-80px)]">
        <div class="grid grid-cols-4 gap-2">
          <button
            v-for="product in filteredProducts"
            :key="product.id"
            @click="addToCart(product)"
            class="p-2 border border-gray-300 rounded-lg shadow-sm bg-white hover:bg-gray-50 text-left transition-colors"
          >
            <div class="text-xs font-medium text-gray-900 mb-1">{{ product.name }}</div>
            <div class="text-xs text-gray-600 mb-1">{{ product.category }}</div>
            <div class="text-sm font-bold text-green-600">${{ product.price.toFixed(2) }}</div>
            <div class="text-xs text-gray-500">Stock: {{ product.stock }}</div>
          </button>
        </div>
      </div>
    </div>

    <!-- Cart and Payment -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm flex flex-col">
      <!-- Cart Header -->
      <div class="border-b rounded-lg p-2">
        <div class="flex justify-between items-center">
          <h3 class="text-sm font-bold text-gray-900">Current Order</h3>
          <button
            @click="clearCart"
            class="h-6 px-2 text-xs bg-transparent border border-gray-300 hover:bg-gray-50 rounded flex items-center"
          >
            <Trash2 class="h-3 w-3 mr-1" />
            Clear
          </button>
        </div>
      </div>

      <!-- Cart Items -->
      <div class="flex-1 overflow-y-auto p-2">
        <div v-if="cart.length === 0" class="text-center text-gray-500 text-xs mt-8">
          No items in cart
        </div>
        <div v-else class="space-y-1">
          <div
            v-for="item in cart"
            :key="item.id"
            class="border border-gray-200 p-2 bg-gray-50"
          >
            <div class="flex justify-between items-start mb-1">
              <div class="text-xs font-medium text-gray-900">{{ item.name }}</div>
              <button
                @click="removeFromCart(item.id)"
                class="text-red-500 hover:text-red-700"
              >
                <Trash2 class="h-3 w-3" />
              </button>
            </div>
            <div class="flex justify-between items-center">
              <div class="flex items-center gap-1">
                <button
                  @click="updateQuantity(item.id, -1)"
                  class="w-5 h-5 border border-gray-300 bg-white hover:bg-gray-50 flex items-center justify-center"
                >
                  <Minus class="h-2 w-2" />
                </button>
                <span class="text-xs font-medium w-6 text-center">{{ item.quantity }}</span>
                <button
                  @click="updateQuantity(item.id, 1)"
                  class="w-5 h-5 border border-gray-300 bg-white hover:bg-gray-50 flex items-center justify-center"
                >
                  <Plus class="h-2 w-2" />
                </button>
              </div>
              <div class="text-xs font-bold text-green-600">
                ${{ (item.price * item.quantity).toFixed(2) }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="border-t border-gray-200 p-2 bg-gray-50">
        <div class="space-y-1 text-xs">
          <div class="flex justify-between">
            <span>Subtotal:</span>
            <span>${{ subtotal.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between">
            <span>Tax (8%):</span>
            <span>${{ tax.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between font-bold text-sm border-t border-gray-300 pt-1">
            <span>Total:</span>
            <span>${{ total.toFixed(2) }}</span>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="mt-2 space-y-2">
          <div class="flex gap-1">
            <button
              @click="paymentMethod = 'cash'"
              :class="`flex-1 h-6 text-xs border rounded flex items-center justify-center transition-colors ${
                paymentMethod === 'cash'
                  ? 'bg-orange-500 text-white border-orange-400'
                  : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
              }`"
            >
              <DollarSign class="h-3 w-3 mr-1" />
              Cash
            </button>
            <button
              @click="paymentMethod = 'card'"
              :class="`flex-1 h-6 text-xs border rounded flex items-center justify-center transition-colors ${
                paymentMethod === 'card'
                  ? 'bg-orange-500 text-white border-orange-400'
                  : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
              }`"
            >
              <CreditCard class="h-3 w-3 mr-1" />
              Card
            </button>
          </div>

          <div v-if="paymentMethod === 'cash'" class="space-y-1">
            <input
              v-model="cashReceived"
              placeholder="Cash received"
              class="h-7 text-xs border border-gray-300 rounded w-full px-2"
              type="number"
              step="0.01"
            />
            <div v-if="cashReceived" class="text-xs">
              <span class="font-medium">Change: </span>
              <span :class="change >= 0 ? 'text-green-600' : 'text-red-600'">
                ${{ change.toFixed(2) }}
              </span>
            </div>
          </div>

          <button
            @click="completeSale"
            :disabled="cart.length === 0 || (paymentMethod === 'cash' && change < 0)"
            class="w-full h-8 text-xs bg-orange-600 text-white rounded hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
          >
            <Receipt class="h-3 w-3 mr-1" />
            Complete Sale
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Search, Plus, Minus, Trash2, CreditCard, DollarSign, Receipt } from 'lucide-vue-next'

const cart = ref([])
const selectedCategory = ref('All')
const searchTerm = ref('')
const paymentMethod = ref('cash')
const cashReceived = ref('')

const products = ref([
  { id: "1", name: "Coffee", price: 3.5, category: "Beverages", stock: 50 },
  { id: "2", name: "Sandwich", price: 8.99, category: "Food", stock: 25 },
  { id: "3", name: "Muffin", price: 4.25, category: "Food", stock: 30 },
  { id: "4", name: "Tea", price: 2.75, category: "Beverages", stock: 40 },
  { id: "5", name: "Salad", price: 12.5, category: "Food", stock: 15 },
  { id: "6", name: "Juice", price: 4.99, category: "Beverages", stock: 20 },
  { id: "7", name: "Bagel", price: 3.75, category: "Food", stock: 35 },
  { id: "8", name: "Smoothie", price: 6.25, category: "Beverages", stock: 18 },
])

const categories = ref(['All', 'Food', 'Beverages'])

const filteredProducts = computed(() => {
  return products.value.filter((product) => {
    const matchesCategory = selectedCategory.value === 'All' || product.category === selectedCategory.value
    const matchesSearch = product.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    return matchesCategory && matchesSearch
  })
})

const subtotal = computed(() => 
  cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0)
)

const tax = computed(() => subtotal.value * 0.08)
const total = computed(() => subtotal.value + tax.value)
const change = computed(() => 
  paymentMethod.value === 'cash' ? Math.max(0, parseFloat(cashReceived.value || '0') - total.value) : 0
)

const addToCart = (product) => {
  const existing = cart.value.find(item => item.id === product.id)
  if (existing) {
    existing.quantity += 1
  } else {
    cart.value.push({ ...product, quantity: 1 })
  }
}

const updateQuantity = (id, change) => {
  const item = cart.value.find(item => item.id === id)
  if (item) {
    item.quantity += change
    if (item.quantity <= 0) {
      removeFromCart(id)
    }
  }
}

const removeFromCart = (id) => {
  const index = cart.value.findIndex(item => item.id === id)
  if (index > -1) {
    cart.value.splice(index, 1)
  }
}

const clearCart = () => {
  cart.value = []
  cashReceived.value = ''
}

const completeSale = () => {
  console.log('Sale completed:', {
    items: cart.value,
    total: total.value,
    paymentMethod: paymentMethod.value,
    cashReceived: cashReceived.value,
    change: change.value
  })
  clearCart()
}
</script>
