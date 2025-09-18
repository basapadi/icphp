<template>
  <div class="bg-gray-50 border-b border-gray-200 py-2 px-4">
    <span class="text-lg mr-2 font-bold text-gray-700 mb-0">{{ title }}</span>
    <span class="text-xs text-gray-500">{{ description }}</span>
  </div>
</template>
<script>
export default {
  name: "PageHeader",
  props: {
    title: { type: String, default: "" },
    description: { type: String, default: "" },
  }
}
</script>