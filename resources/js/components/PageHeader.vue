<template>
  <div class="relative border-b border-gray-200 px-4 py-2">
    <span class="relative text-sm italic z-2 mr-1 font-bold text-gray-700 mb-0">{{ title }} -</span>
    <span class="relative text-sm italic z-2 text-orange-500"> {{ description }}</span>
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