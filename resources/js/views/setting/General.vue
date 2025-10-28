<template>
  <AdminLayout>
    <div class="relative z-2 h-screen">
      <PageHeader title="Pengaturan Umum" description="Pengaturan Umum Aplikasi"/>
      <div class="flex gap-1 p-2">
        <Card class="flex-1 pt-1 bg-white/10">
          <!-- Tabs -->
          <div class="mx-4">
            <nav class=" flex space-x-2" aria-label="Tabs">
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
                @click="activeTab = 'mail'"
                :class="[
                  activeTab === 'mail'
                    ? 'border-orange-500 text-orange-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                Mailing
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
                Keuangan
              </button>
            </nav>
          </div>
          <!-- Tab Content -->
          <div class="px-4">
            <div v-if="activeTab === 'toko'">
              <form @submit.prevent="handleSubmit('toko')" enctype="multipart/form-data">
                <div class="grid grid-cols-3 md:grid-cols-3 max-h-120">
                  <div class="space-y-4 px-2 gap-4">
                    <Input
                      v-model="settings.toko.data.namaToko"
                      label="Nama Toko"
                      hint="Masukkan nama toko anda"
                      required
                      name="namaToko"
                      id="namaToko"
                      min="5"
                    />
                    <Phone
                      v-model="settings.toko.data.telepon"
                      label="Telepon Toko/WA"
                      hint="Masukkan telepon toko anda atau nomor whatsapp bisnis anda"
                      required
                      name="telepon"
                      id="telepon"
                      min="11"
                    />
                    <Input
                      v-model="settings.toko.data.email"
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
                        v-model="settings.toko.data.alamat"
                        name="alamat"
                        id="alamat"
                        hint="Masukkan alamat toko anda"
                        required
                    />
                  </div>
                  <div class="space-y-4 px-2 gap-4">
                    <Input
                      v-model="settings.toko.data.pemilik"
                      label="Nama Pemilik"
                      hint="Masukkan nama pemilik toko"
                      name="pemilik"
                      id="pemilik"
                      type="pemilik"
                    />
                    <FileUpload
                        label="Logo"
                        type="file"
                        v-model="settings.toko.data.logo"
                        name="logo"
                        id="logo"
                        hint="Logo toko anda"
                        extension=".png"
                        :maxsize="3"
                        :maxfile="1"
                        :required="settings.toko.data.logo == ''?true:false"
                        :multiple="false"
                        :preview="settings.toko.data.logo"
                    />
                  </div>
                </div>
                <div class="mt-4 mx-2 gap-4">
                  <button
                    type="submit"
                    class="px-2 py-1 bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 text-sm"
                  >
                    Simpan Pengaturan
                  </button>
                </div>
              </form>
            </div>
            <div v-if="activeTab === 'mail'">
              <form @submit.prevent="handleSubmit('mailing')" >
                <div class="grid grid-cols-3 md:grid-cols-3 max-h-120">
                  <div class="space-y-4 px-2 gap-4">
                    <Select
                        key="driver"
                        label="Driver"
                        v-model="settings.mailing.data.driver"
                        name="driver"
                        id="driver"
                        required=true
                        :options="mailDrivers"
                        hint="Tipe Driver"
                    />
                    <Input
                        key="host"
                        label="Host"
                        v-model="settings.mailing.data.host"
                        name="host"
                        id="host"
                        required=true
                        hint="Alamat server pengirim"
                    />
                    <Input
                        key="port"
                        label="Port"
                        v-model="settings.mailing.data.port"
                        name="port"
                        id="port"
                        required=true
                        hint="Port SMTP"
                    />
                    <Select
                        key="encryption"
                        label="Encryption"
                        v-model="settings.mailing.data.encryption"
                        name="encryption"
                        id="encryption"
                        required=true
                        :options="{'tls':'TLS','ssl':'SSL'}"
                        hint="Enkripsi yang digunakan"
                    />
                  </div>
                  <div class="space-y-4 px-2 gap-4">
                    <Input
                        key="username"
                        label="Username"
                        v-model="settings.mailing.data.username"
                        name="username"
                        id="username"
                        required=true
                        hint="Username login SMTP, biasanya berupa email"
                    />
                    <Password
                        key="password"
                        label="Password"
                        v-model="settings.mailing.data.password"
                        name="password"
                        id="password"
                        hint="Password login SMTP, kosongkan apabila tidak ada perubahan password"
                    />
                    <Input
                        key="fromAddress"
                        label="Alamat Email Pengirim"
                        v-model="settings.mailing.data.fromAddress"
                        name="fromAddress"
                        id="fromAddress"
                        required=true
                        hint="Alamat pengirim default"
                    />
                    <Input
                        key="fromName"
                        label="Nama Pengirim"
                        v-model="settings.mailing.data.fromName"
                        name="fromName"
                        id="fromName"
                        required=true
                        hint="Nama pengirim default"
                    />
                  </div>
                </div>
                <div class="mt-4 mx-2 gap-4">
                  <button
                    type="submit"
                    class="px-2 py-1 bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 text-sm"
                  >
                    Simpan Pengaturan
                  </button>
                </div>
              </form>
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
import {Input,Select,Radio, FileUpload, Textarea, Phone, Password} from '@/components/ui/form'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
export default {
  name: 'General',
  components: {
    AdminLayout,
    PageHeader,
    Input,
    Password,
    FileUpload,
    Textarea,
    Phone,
    Select,
    Card
  },
  setup(){
    const activeTab = ref('toko')

    return {activeTab}
  },
  data(){
    return {
      mailDrivers: {'smtp':'SMTP','sendmail':'Sendmail'},
      settings: {
        'toko': {
          'data': {}
        },
        'mailing': {
          'data': {}
        }
      },
    }
  },
  methods: {
    async load(){
      try {
       const {data} = await this.$store.dispatch('setting/all')
        this.settings= data.data
      } catch (resp) {
        // console.log('LOAD :',resp?.response)
      }
    },
    async handleSubmit(tab){
      let payload = this.settings[tab]['data']
      payload.key = tab
      await this.$store.dispatch('setting/save', payload)
        .then(({ data }) => {
            this.load();
            alert(data.message)
        })
        .catch((resp) => {
            alert(resp.response.data.message)
        })
        .finally((f) => {

        })
    }
  },
  created(){
    this.load()
  }
}
</script>
