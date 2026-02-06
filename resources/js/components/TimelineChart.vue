<template>
  <div class="space-y-6">
    <div v-if="title" class="text-lg font-semibold text-gray-800">
      {{ title }}
    </div>

    <div class="relative pl-8">
      <div class="absolute left-3 top-0 h-full w-px bg-gray-300"></div>

      <div
        v-for="(item, i) in items"
        :key="i"
        class="relative mb-8 last:mb-0"
      >
        <div
          class="absolute left-0 flex items-center justify-center w-6 h-6 rounded-full border-2"
          :style="{ borderColor: item.color }"
        >
          <div
            class="w-3 h-3 rounded-full"
            :style="{ backgroundColor: item.color }"
          ></div>
        </div>

        <div class="ml-6">
          <div class="text-base font-medium text-gray-800">
            {{ item.month }}
          </div>

          <div class="text-sm text-gray-700 mt-1">
            Total pengadaan: <span class="font-semibold">{{ item.count }}</span>
          </div>

          <div class="text-sm text-gray-600 mt-1">
            {{ item.description }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  title: { type: String, default: '' },
  items: {
    type: Array,
    required: true,
    validator: arr =>
      arr.every(x => x.month && x.count !== undefined)
  }
})
</script>
