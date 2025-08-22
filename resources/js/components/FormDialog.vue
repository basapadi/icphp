<template>
  <!-- Overlay -->
  <div v-if="open" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
    <!-- Dialog box -->
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-4xl p-6">
      <!-- Header -->
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Form {{ title }}</h2>
        <button @click="close" class="text-gray-400 hover:text-gray-600">&times;</button>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit">
        <div class="space-y-2 grid grid-cols-1 md:grid-cols-2 gap-4">
          <template v-for="(field) in fields">
              <div v-if="['text','email','phone'].includes(field.type)" :key="field.name">
                <label class="block text-sm font-semibold text-gray-700">{{ field.label }} <span v-if="field.required" class="text-red-800">*</span></label>
                <input :key="field.name" class="block w-full rounded-sm border border-gray-300 px-3 py-2 text-sm  text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" v-model="form[field.name]" :type="field.type" :name="field.name" :id="field.id" :hint="field.hint" :required="field.required"/>
              </div>
          </template>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-end gap-2">
          <button type="button" @click="close"
            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
            Batal
          </button>
          <button type="submit"
            class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
<script>
export default {
  name: "FormDialog",
  props: {
    open: Boolean,
    title: { type: String, default: "Form" },
    fields: { type: Array, default: () => [] },
    data: { type: Object, default: () => ({}) }
  },
  data() {
    return {
      form: {}
    }
  },
  methods: {
    load() {
      
    },
    close() {
      this.$emit("close")
    },
    submit() {
      this.$emit("submit", this.form)
      this.close()
    }
  },
  beforeMount() {
    this.fields.forEach(f => {
      this.form[f.name] = this.data[f.name] ?? ''
    })
  }
}
</script>