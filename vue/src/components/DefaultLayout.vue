<template>
  <div class="bg-gray-100">
      <!-- navbar start -->
      <nav class="w-full px-5 py-3 bg-gray-800 md:flex justify-between">
          <!-- left side of the navbar -->
          <div class="md:flex">
              <!-- navbar logo -->
              <div class="mx-3 flex justify-between">
                  <img src="../assets/logo.png" alt="" class="w-9 h-9">

                  <button id="nav-btn" @click="showHeaderAndNavbar" class="md:hidden block border border-white p-2 rounded-xl text-white hover:bg-white hover:text-gray-800 duration-150 font-bold">menu</button>
              </div>

              <!-- menu -->
              <div id="navbar" class="mx-10 md:flex items-center md:my-0 my-3 hidden"> 
                  <ul class="md:flex md:items-center">
                      <li class="md:my-0 my-4 mx-2 md:w-auto w-full" v-for="navigation in navigations" :key="navigation">
                          <router-link class="md:px-3 py-2 px-40 py-3 text-white text-sm" :to="{ name: navigation.name }">{{ navigation.name }}</router-link>
                      </li>
                  </ul>
              </div>
          </div>

          <!-- righ side of the nav -->
          <div id="header" class="md:block md:border-none border-t border-gray-700 md:py-0 py-4 hidden">
              <div class="md:flex items-center justify-between">
                    <div class="flex justify-end mt-2 relative">
                        <div class="text-sm text-white text-right">
                            <span class="font-bold">{{ user.name }}</span>
                            <p>{{ user.email }}</p>
                        </div>
                        <img @click="showMenu" src="../assets/images/1.png" alt="" class="w-9 h-9 rounded-full mx-2  hover:outline outline-white duration-150 cursor-pointer">
                        
                        <div id="menu" class="hidden absolute w-40 bg-white rounded border top-12 right-5 shadow-lg">
                            <div class="w-full py-2 px-3 hover:bg-gray-100 duration-150">
                                <button @click="logout" class="text-gray-700 text-sm">Sign Out</button>
                            </div>
                        </div>
                    </div>
              </div>
          </div>
      </nav>
      <!-- navbar end -->
      
      <router-view></router-view>

      <Notification/>
  </div>
</template>

<script>
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import Notification from './Notification.vue'

export default {
    name: 'DefaultLayout',

    components: {
        Notification
    },

    data(){
        return {
            store: useStore(),
            router: useRouter(),

            navigations: [
                { name: 'Dashboard', to: { name: 'Dashboard'}},
                { name: 'Survey',  to: { name: 'Survey'}},
            ]
        }
    },

    computed: {
        user(){
            return this.store.state.user.data;
        }
    },

    methods: {
        // show the profile and logout menu
        showMenu(){
            let menu = document.querySelector('#menu')
            if(menu.classList.contains('hidden')){
                menu.classList.remove('hidden')
            }else{
                menu.classList.add('hidden')
            }
        },

        // show and hide
        showHeaderAndNavbar(){
            let nav = document.querySelector('#navbar')
            let header = document.querySelector('#header')
            let navBtn = document.querySelector('#nav-btn')

            if(nav.classList.contains('hidden') && header.classList.contains('hidden')){
                nav.classList.remove('hidden')
                header.classList.remove('hidden')
                navBtn.textContent = 'X';
                navBtn.classList.add('px-5')
            }else{
                nav.classList.add('hidden')
                header.classList.add('hidden')
                navBtn.textContent = 'menu';
            }
        },

        // logout
        logout(){
            sessionStorage.clear();
            this.store.dispatch('logout')
                .then((res) => {
                    this.router.push({
                        name: 'Login'
                    });
                })
        }
    }

    
}
</script>

<style>

</style>