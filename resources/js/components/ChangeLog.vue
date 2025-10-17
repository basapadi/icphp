<template>
  <div class="max-w-2xl mx-2 p-2">
    <h2 class="text-xl mb-4 text-gray-600">Histori Perubahan Kode Sumber</h2>

    <!-- Loading State -->
    <div v-if="loading" class="text-gray-500 text-center">Memuat commits...</div>

    <!-- Error State -->
    <div v-else-if="error" class="text-red-600 text-center">{{ error }}</div>

    <!-- Commit List -->
    <ul v-else class="space-y-2 max-h-84 overflow-y-auto">
      <li v-for="(commit, index) in commits" :key="index" class="p-2 border border-dashed rounded-lg bg-white shadow-sm hover:shadow-md transition">
        <div class="text-gray-600 font-semibold" v-html="renderEmoji(commit.message)"></div>
        <div class="mt-1 text-sm text-gray-600 flex justify-between">
          <span class="italic text-xs text-muted-foreground">by {{ commit.author }}</span>
          <span class="italic text-xs">{{ formatDate(commit.date) }}</span>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import * as emoji from "node-emoji";

const commits = ref([])
const loading = ref(true)
const error = ref(null)

const fetchCommits = async () => {
  try {
    const { data } = await axios.get('/api/auth/change-log')
    commits.value = data.data
  } catch (e) {
    error.value = 'Gagal memuat commits'
  } finally {
    loading.value = false
  }
}

const formatDate = (isoString) => {
  const d = new Date(isoString)
  return d.toLocaleString('id-ID', {
    dateStyle: 'medium',
    timeStyle: 'short'
  })
}

const renderEmoji = (text) => {
  return emoji.emojify(text);
}

onMounted(fetchCommits)
</script>
