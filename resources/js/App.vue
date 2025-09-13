<template>
  <div id="app">
    <vue3-confirm-dialog></vue3-confirm-dialog>
    <div class="watermark shadow-lg">
      Demo v{{ appVersion }}
    </div>
    <router-view />
  </div>
</template>
<style>
  .watermark {
    position:fixed;
    bottom:20px;
    left:35px;
    opacity:1;
    z-index:9999;
    font-size: 1.2em;
    font-weight: 800;
    margin: 5px;
    border-radius: 5px;
    border-width: 1;
    padding-left: 15px;
    padding-right: 15px;
    padding-top: 5px;
    padding-bottom: 5px;
    text-shadow: 1em;
    background-color: rgb(247, 132, 0);
    font-family:'monospace';
    color:rgb(255, 255, 255);
  }
</style>
<script setup>
import { onMounted, onBeforeUnmount } from 'vue'
const appVersion = import.meta.env.VITE_APP_VERSION
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