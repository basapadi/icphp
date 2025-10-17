<template>
  <div class="max-w-2xl mx-2">
    <h2 class="text-xl mb-4 text-gray-600">Histori Perubahan Kode Sumber</h2>

    <!-- Loading State -->
    <div v-if="loading" class="text-gray-500 text-center">Memuat data perubahan...</div>

    <!-- Error State -->
    <div v-else-if="error" class="text-red-600 text-center">{{ error }}</div>

    <!-- Commit List -->
    <ul v-else class="space-y-2 max-h-84 overflow-y-auto">
      <li
        v-for="(commit, index) in commits"
        :key="index"
        class="p-2 border border-dashed rounded-lg bg-white shadow-sm hover:shadow-md transition"
      >
        <!-- Pesan commit -->
        <div class="text-gray-600 text-sm" v-html="renderEmoji(commit.message)"></div>

        <!-- Info author + tanggal -->
        <div class="mt-2 text-sm text-gray-600 flex justify-between items-center">
          <div class="flex items-center space-x-2">
            <a
              v-if="commit.profile"
              :href="commit.profile"
              target="_blank"
              rel="noopener noreferrer"
              class="flex items-center space-x-2 hover:text-orange-600 transition"
            >
              <!-- Avatar author -->
              <img
                :src="commit.avatar || '/images/default-avatar.png'"
                alt="Author avatar"
                class="w-6 h-6 rounded-full border"
              />
              <span class="italic text-xs text-orange-500">{{ commit.author }} <small class="text-gray-300">({{ commit.username }}) </small></span>
            </a>
          </div>

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
