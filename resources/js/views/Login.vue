<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-t from-white to-orange-100">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center">
        <div class="flex items-center justify-center">
          <img src="./../../assets/ihand-512.png" alt="Logo" class="w-40 h-40 object-contain" />
        </div>
      </div>
      <h2 class="mt-6 text-center text-3xl font-bold text-gray-500 text-shadow-sm">
        Masuk ke Akun Anda
      </h2>
    </div>

    <div v-if="!initial" class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="handleLogin" class="space-y-6">
          <div>
            <label for="username" class="block text-sm font-medium text-gray-700">
              Username
            </label>
            <div class="mt-1">
              <input
                id="username"
                v-model="username"
                type="text"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                :class="{ 'border-red-300': usernameError }"
                placeholder="Masukkan username"
              />
              <p v-if="usernameError" class="mt-2 text-sm text-red-600">
                {{ usernameError }}
              </p>
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Password
            </label>
            <div class="mt-1 relative">
              <input
                id="password"
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                required
                class="appearance-none block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                :class="{ 'border-red-300': passwordError }"
                placeholder="Masukkan password"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
              >
                <svg v-if="!showPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg v-else class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                </svg>
              </button>
              <p v-if="passwordError" class="mt-2 text-sm text-red-600">
                {{ passwordError }}
              </p>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember-me"
                v-model="rememberMe"
                type="checkbox"
                class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-900 text-shadow-sm">
                Ingat saya
              </label>
            </div>
          </div>

          <div>
            <button
              type="submit"
              :disabled="isLoading"
              class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isLoading" class="absolute left-0 inset-y-0 flex items-center pl-3">
                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                </svg>
              </span>
              {{ isLoading ? 'Memproses...' : 'Masuk' }}
            </button>
          </div>

          <div v-if="loginError" class="rounded-md bg-red-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-800">{{ loginError }}</p>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div v-else class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <Card class="flex-1 max-w-2xl">
        <CardHeader>
          <CardTitle>Konfigurasi Basis Data</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="saveLocalConfig" >
            <div class="space-y-4 grid grid-cols-1 md:grid-cols-1 gap-2">
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
                Simpan COnfigurasi Database
              </button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  name: 'LoginPage',
  data() {
    return {
      username: '',
      password: '',
      rememberMe: false,
      showPassword: false,
      isLoading: false,
      usernameError: '',
      passwordError: '',
      loginError: '',
      initial: false,
      form: {
        driver: 'sqlite'
      },
      drivers: []
    }
  },
  methods: {
    ...mapActions('auth', ['login']),
    async handleLogin() {
      this.usernameError = ''
      this.passwordError = ''
      this.loginError = ''

      if (!this.username) {
        this.usernameError = 'Username harus diisi'
        return
      }
      if (this.username.length < 3) {
        this.usernameError = 'Username minimal 3 karakter'
        return
      }
      if (!this.password) {
        this.passwordError = 'Password harus diisi'
        return
      }
      if (this.password.length < 8) {
        this.passwordError = 'Password minimal 6 karakter'
        return
      }

      this.isLoading = true
      try {
        const resp = await this.login({ username: this.username, password: this.password })
        if (resp) this.$router.push('/')
      } catch (error) {
        this.loginError = error.response?.data?.message || 'Login gagal'
      } finally {
        this.isLoading = false
      }
    }
  }
}
</script>
