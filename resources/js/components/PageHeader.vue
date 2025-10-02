<template>
  <div class="bg-gray-50 border-b border-gray-200 px-4 py-2">
    <span class="text-sm italic mr-1 font-bold text-gray-700 mb-0">{{ title }} -</span>
    <span class="text-sm italic text-orange-500"> {{ description }}</span>
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