<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Table Header -->
    <div class="px-4 py-3 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <h2 class="text-base font-semibold text-gray-900">Users</h2>
        <div class="flex items-center space-x-2">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search users..."
              class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
              <Search class="h-4 w-4 text-gray-400" />
            </div>
          </div>
          <button 
            @click="addUser"
            class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
          >
            Add User
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
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Join Date</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr 
            v-for="user in filteredUsers" 
            :key="user.id" 
            class="hover:bg-gray-50 transition-colors"
          >
            <td class="px-4 py-2 whitespace-nowrap">
              <input 
                v-model="selectedUsers"
                :value="user.id"
                type="checkbox" 
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
              />
            </td>
            <td class="px-4 py-2 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-8 w-8">
                  <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                    <span class="text-xs font-medium text-gray-700">
                      {{ getInitials(user.name) }}
                    </span>
                  </div>
                </div>
                <div class="ml-3">
                  <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                  <div class="text-xs text-gray-500">{{ user.email }}</div>
                </div>
              </div>
            </td>
            <td class="px-4 py-2 whitespace-nowrap">
              <span :class="getRoleBadge(user.role)">{{ user.role }}</span>
            </td>
            <td class="px-4 py-2 whitespace-nowrap">
              <span :class="getStatusBadge(user.status)">{{ user.status }}</span>
            </td>
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
              {{ formatDate(user.joinDate) }}
            </td>
            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
              <div class="flex items-center space-x-2">
                <button 
                  @click="editUser(user)"
                  class="text-blue-600 hover:text-blue-900 transition-colors"
                >
                  Edit
                </button>
                <button 
                  @click="deleteUser(user.id)"
                  class="text-red-600 hover:text-red-900 transition-colors"
                >
                  Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Table Footer -->
    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Showing <span class="font-medium">{{ startIndex }}</span> to 
          <span class="font-medium">{{ endIndex }}</span> of 
          <span class="font-medium">{{ filteredUsers.length }}</span> results
        </div>
        <div class="flex items-center space-x-2">
          <button
            @click="previousPage"
            :disabled="currentPage === 1"
            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Previous
          </button>
          <button class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
            {{ currentPage }}
          </button>
          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Search } from 'lucide-vue-next'

const searchQuery = ref('')
const selectAll = ref(false)
const selectedUsers = ref([])
const currentPage = ref(1)
const itemsPerPage = ref(8)

const tableData = ref([
  { id: 1, name: "John Doe", email: "john@example.com", role: "Admin", status: "Active", joinDate: "2024-01-15" },
  { id: 2, name: "Jane Smith", email: "jane@example.com", role: "Editor", status: "Active", joinDate: "2024-02-20" },
  { id: 3, name: "Mike Johnson", email: "mike@example.com", role: "User", status: "Inactive", joinDate: "2024-03-10" },
  { id: 4, name: "Sarah Wilson", email: "sarah@example.com", role: "Admin", status: "Active", joinDate: "2024-01-25" },
  { id: 5, name: "Tom Brown", email: "tom@example.com", role: "Editor", status: "Active", joinDate: "2024-02-14" },
  { id: 6, name: "Lisa Davis", email: "lisa@example.com", role: "User", status: "Pending", joinDate: "2024-03-22" },
  { id: 7, name: "David Miller", email: "david@example.com", role: "User", status: "Active", joinDate: "2024-02-28" },
  { id: 8, name: "Emma Garcia", email: "emma@example.com", role: "Editor", status: "Active", joinDate: "2024-01-30" },
])

const filteredUsers = computed(() => {
  if (!searchQuery.value) return tableData.value
  return tableData.value.filter(user => 
    user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const totalPages = computed(() => 
  Math.ceil(filteredUsers.value.length / itemsPerPage.value)
)

const startIndex = computed(() => 
  (currentPage.value - 1) * itemsPerPage.value + 1
)

const endIndex = computed(() => 
  Math.min(currentPage.value * itemsPerPage.value, filteredUsers.value.length)
)

const getInitials = (name) => {
  return name.split(" ").map(n => n[0]).join("")
}

const getStatusBadge = (status) => {
  const baseClasses = "px-2 py-1 text-xs font-medium rounded-full"
  switch (status) {
    case "Active":
      return `${baseClasses} bg-green-100 text-green-800`
    case "Inactive":
      return `${baseClasses} bg-red-100 text-red-800`
    case "Pending":
      return `${baseClasses} bg-yellow-100 text-yellow-800`
    default:
      return `${baseClasses} bg-gray-100 text-gray-800`
  }
}

const getRoleBadge = (role) => {
  const baseClasses = "px-2 py-1 text-xs font-medium rounded-full"
  switch (role) {
    case "Admin":
      return `${baseClasses} bg-purple-100 text-purple-800`
    case "Editor":
      return `${baseClasses} bg-blue-100 text-blue-800`
    case "User":
      return `${baseClasses} bg-gray-100 text-gray-800`
    default:
      return `${baseClasses} bg-gray-100 text-gray-800`
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedUsers.value = filteredUsers.value.map(user => user.id)
  } else {
    selectedUsers.value = []
  }
}

const addUser = () => {
  console.log('Add user clicked')
}

const editUser = (user) => {
  console.log('Edit user:', user)
}

const deleteUser = (userId) => {
  console.log('Delete user:', userId)
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}
</script>
