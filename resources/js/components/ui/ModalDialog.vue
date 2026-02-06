<script setup>
const emit = defineEmits(["confirm", "cancel"]);
</script>

<template>
    <div>
        <button @click="emit('confirm')">OK</button>
        <button @click="emit('cancel')">Batal</button>
    </div>
</template>
