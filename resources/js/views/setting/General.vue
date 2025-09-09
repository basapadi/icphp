<template>
  <AdminLayout>
    <div class="w-full">
      <PageHeader title="Pengaturan Umum" description="Pengaturan Umum Aplikasi"/>
      <div class="flex gap-1 p-2">
        <Card class="flex-1">
          <!-- Tabs -->
          <div class="mx-4">
            <nav class=" flex space-x-6" aria-label="Tabs">
              <button
                @click="activeTab = 'toko'"
                :class="[
                  activeTab === 'toko'
                    ? 'border-orange-500 text-orange-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                Toko
              </button>
              <button
                @click="activeTab = 'aplikasi'"
                :class="[
                  activeTab === 'aplikasi'
                    ? 'border-orange-500 text-orange-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                Aplikasi
              </button>
              <button
                @click="activeTab = 'keuangan'"
                :class="[
                  activeTab === 'keuangan'
                    ? 'border-orange-500 text-orange-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                Keuangan
              </button>
            </nav>
          </div>
          <!-- Tab Content -->
          <div class="px-4 bg-white">
            <div v-if="activeTab === 'toko'">
              <form @submit.prevent="handleSubmit" >
                <div class="grid grid-cols-3 md:grid-cols-3">
                  <div class="space-y-2 px-2 overflow-y-auto grid grid-cols-1 md:grid-cols-1 gap-4">
                    <Input
                      v-model="form.nama_toko"
                      label="Nama Toko"
                      hint="Masukkan nama toko anda"
                      required
                      name="nama_toko"
                      id="nama_toko"
                      min="5"
                    />
                    <Phone
                      v-model="form.telepon"
                      label="Telepon Toko"
                      hint="Masukkan telepon toko anda"
                      required
                      name="telepon"
                      id="telepon"
                      min="11"
                    />
                    <Input
                      v-model="form.email"
                      label="Email Toko"
                      hint="Masukkan email toko anda"
                      required
                      name="email"
                      id="email"
                      type="email"
                    />
                    <Textarea
                        key="alamat"
                        label="Alamat"
                        v-model="form.alamat"
                        name="alamat"
                        id="alamat"
                        hint="Masukkan alamat toko anda"
                        required
                    />
                    <FileUpload
                        key="logo"
                        label="Logo"
                        v-model="form.logo"
                        name="logo"
                        id="logo"
                        hint="Logo toko anda"
                        extension=".png,.jpg"
                        :maxsize="3"
                        :maxfile="1"
                    />
                    <Input
                      v-model="form.pemilik"
                      label="Nama Pemilik"
                      hint="Masukkan nama pemilik toko"
                      name="pemilik"
                      id="pemilik"
                      type="pemilik"
                    />
                  </div>
                </div>
                <div class="space-y-4 grid grid-cols-1 md:grid-cols-3 mt-4 gap-1">
                  <button
                    type="submit"
                    class="w-full py-1 bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 transition-colors delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103"
                  >
                    Simpan Pengaturan Toko
                  </button> 
                </div>
              </form>
            </div>
            <div v-if="activeTab === 'aplikasi'">
              <h2 class="text-lg font-semibold mb-2">Pengaturan Sistem</h2>
              <p class="text-gray-600">Konten pengaturan sistem ditampilkan di sini.</p>
            </div>
            <div v-else-if="activeTab === 'keuangan'">
              <h2 class="text-lg font-semibold mb-2">Pengaturan Keuangan</h2>
              <p class="text-gray-600">Konten pengaturan keuangan ditampilkan di sini.</p>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import { ref } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import PageHeader from '@/components/PageHeader.vue'
import {Input,Select,Radio, FileUpload, Textarea, Phone} from '@/components/ui/form'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
export default {
  name: 'General',
  components: {
    AdminLayout,
    PageHeader,
    Input,
    FileUpload,
    Textarea,
    Phone,
    Card
  },
  setup(){
    const activeTab = ref('toko')

    return {activeTab}
  },
  data(){
    return {
      form: {}
    }
  },
  methods: {
    handleSubmit(){

    }
  }
}
</script>
