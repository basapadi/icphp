<script setup>
  const emit = defineEmits(['confirm', 'cancel'])
</script>

<template>
  <div>
    <!-- The modal component body -->
    <button @click="emit('confirm')">OK</button>
    <button @click="emit('cancel')">Batal</button>
  </div>
</template>