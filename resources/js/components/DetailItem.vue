<template>
    <div class="space-y-1">
        <dt class="text-xs text-muted-foreground uppercase">{{ label }}</dt>
        <dd class="font-medium">{{ value }}</dd>
    </div>
</template>

<script setup>
const props = defineProps({
    label: {
        type: String,
    },
    value: {
        type: String,
    },
});
</script>
