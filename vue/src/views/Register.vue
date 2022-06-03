<template>
    <div>
      <div>
        <img class="mx-auto h-12 w-auto" src="../assets/logo.png" alt="Workflow" />
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Register your account</h2>
      </div>
      <form class="mt-8 space-y-6" @submit="register">
        <input type="hidden" name="remember" value="true" />
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="name" class="sr-only">Name</label>
            <input v-model="user.name" id="name" name="name" type="text" autocomplete="name" required="" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="name" />
          </div>
          <div>
            <label for="email-address" class="sr-only">Email address</label>
            <input v-model="user.email" id="email-address" name="email" type="email" autocomplete="email" required="" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900  focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Email address" />
          </div>
          <div>
            <label for="password" class="sr-only">Password</label>
            <input v-model="user.password" id="password" name="password" type="password" autocomplete="current-password" required="" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password" />
          </div>
           <div>
            <label for="password_confirm" class="sr-only">Password confirm</label>
            <input v-model="user.password_confirmation" id="password_confirm" name="password_confirm" type="password" autocomplete="current-password_confirm" required="" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 rounded-b-md placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password Confirm" />
          </div>
        </div>

        <div v-if="error" @click="error = ''" class="px-10 flex justify-between w-full bg-red-600 rounded-lg font-bold py-2 cursor-pointer">
          <p class="text-white">{{ error.message }}</p>
          <span class="text-white">X</span>
        </div>
        <ul v-if="error">
          <li v-for="err in error.errors" :key="err" class="text-red-500">{{ err[0] }}</li>
        </ul>

        <div class="flex items-center justify-between">
          <div class="text-sm">
            <router-link :to="{ name: 'Login' }" class="font-medium text-indigo-600 hover:text-indigo-500"> already have an account? </router-link>
          </div>
        </div>

        <div>
          <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <LockClosedIcon class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" aria-hidden="true" />
            </span>
            Register
            <svg v-if="loading" class="animate-spin absolute right-10 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" view-box="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColer" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0  018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.93813-2.647z"></path>
            </svg>
          </button>
        </div>
      </form>
    </div>
</template>

<script>
import { LockClosedIcon } from '@heroicons/vue/solid'
import store from '../store'
import { mapMutations, mapState } from 'vuex'
import { useRouter } from 'vue-router'

export default {
  name: "Register",

  data(){
    return {
      user: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
      },

      router: useRouter(),

      error: null,
    }
  },

  computed:{
    ...mapState(['loading']),
  },

  methods:{
    ...mapMutations(['loadingClientChange']),
    register(event){
      event.preventDefault();

      store
        .dispatch('register', this.user)
          .then((res) => {
            this.router.push({ name: 'Dashboard' })  
          })
          .catch(err => {
            this.loadingClientChange(false);
            this.error = err.response.data;
          })
    }
  },  

  components: {
    LockClosedIcon,
  },
}
</script>