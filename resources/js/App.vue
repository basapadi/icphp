<template>
  <div id="app">
    <vue3-confirm-dialog></vue3-confirm-dialog>
    <router-view />
  </div>
</template>
<script setup>
import { onMounted, onBeforeUnmount } from 'vue'

onMounted(() => {
  document.addEventListener('contextmenu', preventRightClick)
})

onBeforeUnmount(() => {
  document.removeEventListener('contextmenu', preventRightClick)
})

function preventRightClick(e) {
  e.preventDefault()
}
</script>