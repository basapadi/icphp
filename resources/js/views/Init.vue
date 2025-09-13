<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-t from-white to-orange-100">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center">
        <div class="flex items-center justify-center">
          <img src="./../../assets/ihand-512.png" alt="Logo" class="w-40 h-40 object-contain" />
        </div>
      </div>
      <h2 class="mt-6 text-center text-3xl font-bold text-gray-500 text-shadow-sm">
        Inisiasi Database
      </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        Init Database
      </div>
    </div>
  </div>
</template>

<script >
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useStore, mapGetters, mapActions } from 'vuex'

</script>