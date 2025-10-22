<template>
  <AdminLayout>
    <div class="relative z-1">
      <PageHeader title="Basis Data" description="Pengaturan Basis Data"/>
      <div class="flex gap-1 p-2">
        <Card class="h-full flex-1 py-1 pb-4 px-2 bg-white/10">
          <div class="mx-4">
            <nav class="flex space-x-2" aria-label="Tabs">
              <button
                @click="activeTab = 'db'"
                :class="[
                  activeTab === 'db'
                    ? 'border-orange-500 text-orange-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                Basis Data
              </button>
              <button
                @click="activeTab = 'command'"
                :class="[
                  activeTab === 'command'
                    ? 'border-orange-500 text-orange-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                Command
              </button>
              <button
                @click="activeTab = 'backup_db'"
                :class="[
                  activeTab === 'backup_db'
                    ? 'border-orange-500 text-orange-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                Backup Basis Data
              </button>
            </nav>
          </div>
          <div class="px-4">
            <div v-if="activeTab === 'db'">
              <div class="grid grid-cols-2 md:grid-cols-2 max-h-120">
                <form @submit.prevent="saveLocalConfig" >
                  <div class="space-y-4 grid grid-cols-2 md:grid-cols-2 gap-2">
                    <Select
                      v-model="form.driver"
                      :options="drivers"
                      label="Tipe Database"
                      hint="Pilih tipe database yang akan digunakan"
                      required
                      name="type"
                      id="type"
                    />
                    <Input
                      v-if="form.driver == 'sqlite'"
                      v-model="form.database"
                      label="SQlite Path"
                      hint="Masukkan Path database SQlite"
                      :required="form.driver == 'sqlite'?true:false"
                      name="database"
                      id="database"
                    />
                    <template v-else-if="['mysql','pgsql','mariadb'].includes(form.driver)">
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
                      <Input
                        v-model="form.charset"
                        label="Charset"
                        hint="Charset database"
                        name="charset"
                        id="charset"
                        type="charset"
                      />
                      <Input
                        v-model="form.collation"
                        label="Collation"
                        hint="Collation database"
                        name="collation"
                        id="collation"
                        type="collation"
                        v-if="['mysql','mariadb'].includes(form.driver)"
                      />
                    </template>
                  </div>
                  <div v-if="form.driver != ''" class="space-y-2 grid grid-cols-1 md:grid-cols-1 mt-4 gap-1">
                    <button
                      type="button"
                      @click="testDb"
                      class="w-full py-1 bg-green-50 border-1 border-green-200 rounded-md hover:bg-green-200 text-green-700 transition-colors delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103"
                    >
                      Uji Coba Koneksi
                    </button>
                    <button
                      type="submit"
                      class="w-full py-1 bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 transition-colors delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103"
                    >
                      Simpan
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <div v-if="activeTab === 'command'">
              <div class="grid grid-cols-2 md:grid-cols-2 max-h-120">
                <div class="space-y-4 grid grid-cols-1 md:grid-cols-1 gap-2">
                  <ul class="list-disc text-red-600 pl-4 text-xs">
                    <li class="mb-2 rounded text-xs italic"><b>[Peringatan]</b> Migrasi basis data : akan mempengaruhi struktur basis data anda ke versi terbaru, silakan backup data terlebih dahulu.</li>
                    <li class="mb-2 rounded text-xs italic"><b>[Peringatan]</b> Seed Data : akan menghapus data yang sudah ada dan memuat data contoh ke basis data anda, silakan backup data terlebih dahulu.</li>
                    <li class="mb-2 rounded text-xs italic">Config cache : akan memuat ulang configurasi terbaru</li>
                    <li class="mb-2 rounded text-xs italic">Route cache : akan memuat ulang routing halaman terbaru</li>
                  </ul>
                  <div class="space-y-4 grid grid-cols-1 md:grid-cols-2 pb-4  gap-2">
                    <Radio
                        label="Migrasi Basis Data"
                        v-model="command.migrate_db"
                        name="migrate_db"
                        id="migrate_db"
                        hint="Migrasi Basis Data"
                        :options="data_options_migrate"
                        direction="row"
                    />
                    <!-- <Radio
                        label="Seed Data"
                        v-model="command.seed_db"
                        name="seed_db"
                        id="seed_db"
                        hint="Seed Basis Data"
                        :options="data_options"
                        direction="row"
                    /> -->
                  <Radio
                      label="Config Cache"
                      v-model="command.config_cache"
                      name="config_cache"
                      id="config_cache"
                      hint="Config Cache"
                      :options="data_options"
                      direction="row"
                  />
                  <Radio
                      label="Config Clear"
                      v-model="command.config_clear"
                      name="config_clear"
                      id="config_clear"
                      hint="Config Clear"
                      :options="data_options"
                      direction="row"
                  />
                  <Radio
                      label="Cache Clear"
                      v-model="command.cache_clear"
                      name="cache_clear"
                      id="cache_clear"
                      hint="Cache clear"
                      :options="data_options"
                      direction="row"
                  />
                  <Radio
                      label="Route Cache"
                      v-model="command.route_cache"
                      name="route_cache"
                      id="route_cache"
                      hint="Route Cache"
                      :options="data_options"
                      direction="row"
                  />
                  <button @click="runCommand" type="submit" class="w-full py-1 bg-orange-50 border-1 border-orange-200 rounded-md hover:bg-orange-200 text-orange-500 transition-colors delay-50 duration-100 ease-in-out hover:-translate-y-0.5 hover:scale-103"
                  >
                    Jalankan Command
                  </button>
                </div>
              </div>
            </div>
            </div>
            <div v-if="activeTab === 'backup_db'">
              <div class="grid grid-cols-3 md:grid-cols-3 max-h-120">
                <form @submit.prevent="handleSubmit" >
                  <div class="space-y-2 grid grid-cols-1 md:grid-cols-1 gap-1">
                    <Select
                      v-model="backup.driver"
                      :options="backupDatabaseTypes"
                      label="Tipe Database"
                      hint="Pilih tipe database yang akan digunakan"
                      required
                      name="type"
                      id="type"
                    />
                    <Input
                      v-if="backup.driver == 'sqlite'"
                      v-model="backup.database"
                      label="SQlite Path"
                      hint="Path database SQlite"
                      :required="backup.driver == 'sqlite'?true:false"
                      name="database"
                      id="database"
                    />
                    <template v-else>
                      <Input
                        v-model="backup.host"
                        label="Host"
                        hint="alamat host database"
                        required
                        name="host"
                        id="host"
                      />
                      <Input
                        v-model="backup.port"
                        label="Port"
                        hint="nomor port database"
                        required
                        name="port"
                        id="port"
                        type="number"
                      />
                      <Input
                        v-model="backup.database"
                        label="Database"
                        hint="nama database"
                        required
                        name="database"
                        id="database"
                      />
                      <Input
                        v-model="backup.username"
                        label="Username"
                        hint="username database"
                        required
                        name="username"
                        id="username"
                      />
                      <Input
                        v-model="backup.password"
                        label="Password"
                        hint="password database"
                        name="password"
                        id="password"
                        type="password"
                      />
                      <Input
                        v-model="backup.charset"
                        label="Charset"
                        hint="Charset database"
                        name="charset"
                        id="charset"
                        type="charset"
                      />
                      <Input
                        v-model="backup.collation"
                        label="Collation"
                        hint="Collation database"
                        name="collation"
                        id="collation"
                        type="collation"
                        v-if="['mysql','mariadb'].includes(backup.driver)"
                      />
                    </template>
                  </div>
                  <div v-if="backup.driver != ''" class="space-y-4 grid grid-cols-1 md:grid-cols-1 mt-4 gap-1">
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
                      Simpan
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </Card>        
        <div v-if="loading" class="absolute inset-0 flex pt-80 justify-center bg-white/70 z-10" >
            <LoaderCircle class="w-14 h-14 animate-spin text-orange-500" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
<script>
import { ref } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import PageHeader from '@/components/PageHeader.vue'
import {Input,Select,Radio} from '@/components/ui/form'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { mapGetters } from "vuex";
import { LoaderCircle } from "lucide-vue-next"

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
    Radio,
    LoaderCircle
  },
  setup() {
    let drivers = {
      sqlite: 'SQLite'
    }

    const activeTab = ref('db')

    const backupDatabaseTypes = {
      mysql: 'MySQL',
      mariadb: 'MariaDB',
      pgsql: 'PostgreSQL'
    }

    return {
      drivers,
      backupDatabaseTypes,
      activeTab
    }
  },
  computed: {
    ...mapGetters({
        getForm: "database/getForm"
    }),
  },
  data(){
    return {
      loading: false,
      form: {
        driver: '',
        host: '',
        port: '',
        database: '',
        username: '',
        password: '',
        charset: 'utf8mb4',
        collation: 'utf8mb4_unicode_ci'
      },
      backup: {
        driver: '',
        host: '',
        port: '',
        database: '',
        username: '',
        password: '',
        charset: 'utf8mb4',
        collation: 'utf8mb4_unicode_ci'
      },
      command: {
        migrate_db: 0,
        seed_db:0,
        config_cache:0,
        config_clear:0,
        route_cache:0,
        cache_clear:0
      },
      data_options:["Tidak","Ya"],
      data_options_migrate:["Tidak","Ya","Rollback","Refresh"]
    }
  },
  methods:{
    async load(){
      try {
       const {data} = await this.$store.dispatch('database/edit')
        this.form = data.data.config
        this.drivers = data.data.drivers
      } catch (resp) {
        // alert(resp?.response?.message)
        console.log('LOAD :',resp?.response)
      }
    },
    async testDb(){
        let payload = this.form
        try {
          const {data} = await this.$store.dispatch('database/test', payload)
          alert(data.message)
        } catch (resp) {
          alert(resp.response?.data?.message)
        }
    },
    async saveLocalConfig(){
        this.loading = true;
        let payload = this.form
        try {
          await this.$store.dispatch('database/saveLocalConfig', payload).finally(() => {
            this.loading = false;
          })
        } catch (resp) {
          this.loading = false;
          alert(resp.response?.data?.message)
        }
    },
    async runCommand(){
      this.loading = true;
      let payload = this.command
      try {
        await this.$store.dispatch('database/runCommand', payload).then(({data}) => {
          this.loading = false;
          alert(data.message)
        }).finally(() => {
          this.loading = false;
        })
        
      } catch (resp) {
        alert(resp.response?.data?.message)
      }
    }
  },
  mounted(){
    this.load()
  }
}
</script>
