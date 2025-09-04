<template>
  <div class="bg-gray-50 border-b border-gray-200 p-2">
    <span class="text-lg mr-2 font-bold text-gray-600 mb-0 text-shadow-2xs">{{ title }}</span>
    <span class="text-xs text-gray-400 text-shadow-2xs">{{ description }}</span>
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