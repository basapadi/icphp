<template>
    <div
        class="flex flex-col items-center rounded-2xl bg-background gap-3 w-full py-4 relative"
    >
        <!-- Prev -->
        <button
            v-if="list.length > 1"
            class="absolute left-4 top-1/2 -translate-y-1/2 p-2 bg-accent text-primary rounded-full"
            @click="prev"
        >
            <ChevronLeft />
        </button>

        <!-- Next -->
        <button
            v-if="list.length > 1"
            class="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-accent text-primary rounded-full"
            @click="next"
        >
            <ChevronRight />
        </button>

        <div
            class="absolute top-2 right-0 -translate-x-1/2 bg-muted text-primary text-sm py-1 px-2 rounded"
        >
            {{ `${index+1}/${list.length}` }}
        </div>

        <div class="relative w-full max-w-3xl">
            <img
                :src="current.url ?? './../../assets/empty.jpg'"
                :alt="current.nama ?? 'Image'"
                class="w-full object-contain rounded-md"
                @error="onError"
            />

            <!-- Caption -->
            <div
                class="absolute bottom-2 left-1/2 -translate-x-1/2 bg-black/40 text-white text-sm py-1 px-2 rounded"
            >
                <p>{{ `${current.nama} ${current.no_id}` }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ChevronLeft, ChevronRight } from "lucide-vue-next";
import { computed, ref, watch } from "vue";

const props = defineProps({
    images: {
        type: [Array, Object, String],
        default: () => [],
    },
});

const list = ref([]);
const index = ref(0);

const FALLBACK = "ihandcashier.png";

function normalize(input) {
    if (!input) return [];

    // String → jadikan 1 item
    if (typeof input === "string") {
        return [{ url: input, nama: null, no_id: null }];
    }

    // Object (single)
    if (!Array.isArray(input)) {
        return [
            {
                url: input.url ?? "",
                nama: input.nama ?? null,
                no_id: input.no_id ?? null,
            },
        ];
    }

    // Array (string / object)
    return input.map((item) => {
        if (typeof item === "string") {
            return { url: item, nama: null, no_id: null };
        }

        return {
            url: item?.url ?? "",
            nama: item?.nama ?? null,
            no_id: item?.no_id ?? null,
        };
    });
}

watch(
    () => props.images,
    (val) => {
        list.value = normalize(val).map(i => ({
            url: i.url?.trim() ? i.url : FALLBACK,
            nama: i.nama,
            no_id: i.no_id,
        }));

        index.value = 0; // reset slide tiap data berubah
    },
    {
        immediate: true, // jalan pas component pertama kali render
        deep: true,      // aman kalau object / array
    }
);

const current = computed(() => list.value[index.value] ?? {});

function next() {
    index.value = (index.value + 1) % list.value.length;
}

function prev() {
    index.value = (index.value - 1 + list.value.length) % list.value.length;
}

function onError(e) {
    if (!e.target.src.includes("ihandcashier.png")) {
        e.target.src = FALLBACK;
    }
}
</script>

<style scoped>
img {
    max-height: 480px;
}
</style>
