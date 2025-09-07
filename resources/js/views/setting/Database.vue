<template>
  <AdminLayout>
    <div class="w-full max-h-1">
      <PageHeader title="Basis Data" description="Pengaturan Basis Data"/>
      <div class="flex gap-4 p-2">
        <Card class="flex-1 max-w-md">
          <CardHeader>
            <CardTitle>Konfigurasi Koneksi Basis Data</CardTitle>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="handleSubmit" class="space-y-6">
              <Select
                v-model="form.type"
                :options="databaseTypes"
                label="Tipe Database"
                hint="Pilih tipe database yang akan digunakan"
                required
                name="type"
                id="type"
              />
              <Input
                v-if="form.type == 'sqlite'"
                v-model="form.sqlite_path"
                label="SQlite Path"
                hint="Masukkan Path database SQlite"
                :required="form.type == 'sqlite'?true:false"
                name="sqlite_path"
                id="sqlite_path"
              />
              <template v-else>
                <Input
                  v-model="form.host"
                  label="Host"
                  hint="Masukkan alamat host database"
                  required
                  name="host"
                  id="host"
                />
                <Input
                  v-model="form.port"
                  label="Port"
                  hint="Masukkan nomor port database"
                  required
                  name="port"
                  id="port"
                  type="number"
                />
                <Input
                  v-model="form.database"
                  label="Database"
                  hint="Masukkan nama database"
                  required
                  name="database"
                  id="database"
                />
                <Input
                  v-model="form.username"
                  label="Username"
                  hint="Masukkan username database"
                  required
                  name="username"
                  id="username"
                />
                <Input
                  v-model="form.password"
                  label="Password"
                  hint="Masukkan password database"
                  name="password"
                  id="password"
                  type="password"
                />
              </template>
              <button
                type="button"
                class="w-full py-1 bg-green-50 border-1 border-green-200 rounded-md hover:bg-green-200 text-green-700 transition-colors delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103"
              >
                Uji Coba Koneksi
              </button>
              <button
                type="submit"
                class="w-full py-1 bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 transition-colors delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103"
              >
                Simpan Pengaturan
              </button>
            </form>
          </CardContent>
        </Card>
        <Card class="flex-1 max-w-md">
          <CardHeader>
            <CardTitle>Command Konfigurasi Data</CardTitle>
            <ul class="list-disc text-red-600 pl-4 text-xs">
                <li class="mb-2 rounded text-xs italic"><b>[Peringatan]</b> Migrasi basis data : akan mempengaruhi struktur basis data anda ke versi terbaru, silakan backup data terlebih dahulu.</li>
                <li class="mb-2 rounded text-xs italic"><b>[Peringatan]</b> Seed Data : akan menghapus data yang sudah ada dan memuat data contoh ke basis data anda, silakan backup data terlebih dahulu.</li>
                <li class="mb-2 rounded text-xs italic">Config cache : akan memuat ulang configurasi terbaru</li>
                <li class="mb-2 rounded text-xs italic">Route cache : akan memuat ulang routing halaman terbaru</li>
            </ul>
          </CardHeader>
          <CardContent>
            <div class="space-y-3 p-4 grid grid-cols-2 gap-2 mb-4">
              <Radio
                  label="Migrasi Basis Data"
                  v-model="form.data.migrate_db"
                  name="migrate_db"
                  id="migrate_db"
                  hint="Migrasi Basis Data"
                  :options="data_options"
                  direction="row"
              />
              <Radio
                  label="Seed Data"
                  v-model="form.data.seed_db"
                  name="seed_db"
                  id="seed_db"
                  hint="Seed Basis Data"
                  :options="data_options"
                  direction="row"
              />
              <Radio
                  label="Config Cache"
                  v-model="form.data.config_cache"
                  name="config_cache"
                  id="config_cache"
                  hint="Config Cache"
                  :options="data_options"
                  direction="row"
              />
              <Radio
                  label="Route Cache"
                  v-model="form.data.route_cache"
                  name="route_cache"
                  id="route_cache"
                  hint="Route Cache"
                  :options="data_options"
                  direction="row"
              />
              
            </div>
            <button type="submit" class="w-full py-1 bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 transition-colors delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103"
              >
              Jalankan Perintah
            </button>
          </CardContent>
        </Card>
      </div>
    </div>
  </AdminLayout>
</template>
<script>
import { ref, computed } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import PageHeader from '@/components/PageHeader.vue'
import {Input,Select,Radio} from '@/components/ui/form'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'

export default {
  name: 'Database',
  components: {
    AdminLayout,
    PageHeader,
    Input,
    Select,
    Card,
    CardHeader,
    CardTitle,
    CardContent,
    Radio
  },
  setup() {
    const databaseTypes = {
      sqlite: 'SQLite',
      mysql: 'MySQL',
      postgresql: 'PostgreSQL'
    }

    const handleSubmit = () => {
      // TODO: Implement form submission logic
      console.log('Form submitted:', form.value)
    }

    return {
      databaseTypes,
      handleSubmit
    }
  },
  data(){
    return {
      form: ref({
        type: '',
        host: '',
        port: '',
        database: '',
        username: '',
        password: '',
        sqlite_path: '',
        data: {
          migrate_db: 0,
          seed_db:0,
          config_cache:0,
          route_cache:0
        },
      }),
      data_options:["Tidak","Ya"]
    }
  },
  methods:{
    async load(){

    }
  },
  mounted(){
    this.load()
  }
}
</script>
