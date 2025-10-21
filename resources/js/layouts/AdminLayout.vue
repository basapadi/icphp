<template>
  <div class="h-screen bg-gray-50">
    <AdminToolbar />
    <div class="flex h-screen">
      <!-- Sidebar -->
      <keep-alive>
        <AdminSidebar />
      </keep-alive>

      <!-- Main Content -->
      <!-- Dynamic margin based on sidebar collapsed state -->
      <main 
        :class="[
          'flex-1 mt-10 bg-white min-h-[calc(100vh-4rem)] ml-64 overflow-hidden'
        ]"
      >
        <div class="h-screen">
          <div class="absolute inset-0 z-1 bg-[length:15px_15px]
              [background-image:linear-gradient(to_right,rgba(107,114,128,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(107,114,128,0.04)_1px,transparent_1px)]
              pointer-events-none">
          </div>
          <div ref="gridOverlay" class="absolute inset-0 z-1 pointer-events-none"></div>
          <slot/>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminToolbar from '@/components/AdminToolbar.vue'
import AdminSidebar from '@/components/AdminSidebar.vue'
const gridOverlay = ref(null)
onMounted(() => {
  const overlay = gridOverlay.value
  const canvas = document.createElement('canvas')
  const size = 15
  const ctx = canvas.getContext('2d')
  const drawGrid = () => {
    canvas.width = window.innerWidth
    canvas.height = window.innerHeight

    for (let y = 0; y < canvas.height; y += size) {
      for (let x = 0; x < canvas.width; x += size) {
        if (Math.random() > 0.93) {
          ctx.fillStyle = 'rgba(230,230,230,0.4)'
          ctx.fillRect(x, y, size, size)
        }
      }
    }

    ctx.filter = 'blur(10px)'
  }

  drawGrid()

  canvas.classList.add('absolute', 'inset-0', 'pointer-events-none')
  overlay.appendChild(canvas)

  // saat window di-resize, gambar ulang
  window.addEventListener('resize', () => {
    drawGrid()
  })
})
</script>
