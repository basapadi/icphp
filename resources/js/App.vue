<template>
  <div id="app">
    <vue3-confirm-dialog></vue3-confirm-dialog>
    <router-view />
  </div>
</template>
<style>
  .watermark {  
    opacity:1;
    font-size: 1em;
    font-weight: 800;
    margin-left: 5px;
    border-radius: 5px;
    border-width: 1;
    padding-left: 5px;
    padding-right: 5px;
    text-shadow: 1em;
    color:rgb(255, 255, 255);
  }
</style>
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