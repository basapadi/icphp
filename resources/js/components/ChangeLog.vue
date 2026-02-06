<template>
    <div class="max-w-2xl mx-2">
        <h2 class="text-xl mb-4 text-muted-foreground">
            Histori Perubahan Kode Sumber
        </h2>

        <!-- Loading State -->
        <div v-if="loading" class="text-muted-foreground text-center">
            Memuat data perubahan...
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-destructive text-center">
            {{ error }}
        </div>

        <!-- Commit List -->
        <ul v-else class="space-y-2 max-h-84 overflow-y-auto">
            <li
                v-for="(commit, index) in commits"
                :key="index"
                class="p-2 border border-dashed rounded-lg bg-card shadow-sm hover:shadow-md transition"
            >
                <!-- Pesan commit -->
                <div
                    class="text-muted-foreground text-sm"
                    v-html="renderEmoji(commit.message)"
                ></div>

                <!-- Info author + tanggal -->
                <div
                    class="mt-2 text-sm text-muted-foreground flex justify-between items-center"
                >
                    <div class="flex items-center space-x-2">
                        <a
                            v-if="commit.profile"
                            :href="commit.profile"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center space-x-2 hover:text-primary transition"
                        >
                            <!-- Avatar author -->
                            <img
                                :src="
                                    commit.avatar ||
                                    '/images/default-avatar.png'
                                "
                                alt="Author avatar"
                                class="w-6 h-6 rounded-full border"
                            />
                            <span class="italic text-xs text-primary"
                                >{{ commit.author }}
                                <small class="text-muted-foreground"
                                    >@{{ commit.username }}</small
                                ></span
                            >
                        </a>
                    </div>

                    <span class="italic text-xs">{{
                        formatDate(commit.date)
                    }}</span>
                </div>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import * as emoji from "node-emoji";

const commits = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchCommits = async () => {
    try {
        const { data } = await axios.get("/api/auth/change-log");
        commits.value = data.data;
    } catch (e) {
        error.value = "Gagal memuat commits";
    } finally {
        loading.value = false;
    }
};

const formatDate = (isoString) => {
    const d = new Date(isoString);
    return d.toLocaleString("id-ID", {
        dateStyle: "medium",
        timeStyle: "short",
    });
};

const renderEmoji = (text) => {
    return emoji.emojify(text).replace(/\n/g, "<br>");
};

onMounted(fetchCommits);
</script>
