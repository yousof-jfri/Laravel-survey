<template>
    <div>
        <HeaderLayout >
            <!-- header:start -->
            
            <template v-slot:header>
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-bold">Surveys</h1>
                    <router-link
                        :to="{ name: 'SurveyCreate' }"
                        class="
                            py-2
                            px-3
                            text-white
                            bg-emerald-500
                            rounded-md
                            hover-bg-emerald-600
                        "
                    >Add New Survey</router-link>
                </div>
            </template>
            <!-- header:end -->
            <div v-if="surveysLoading" class="flex justify-center">Loading...</div>
            <div v-else>
                <!-- main content:start -->
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3">
                    <SurveyListItem 
                    v-for="(survey, index) in surveys" 
                    :key="survey.id" 
                    class="animate-fade-in-down"
                    :survey="survey"
                    :style="`animationDelay: ${index * 0.1}s `"
                    @delete="deleteSurvey"/>
                </div>
                <!-- main content:end -->

                <!-- pagination:start -->
                <div class="flex justify-center mt-5">
                    <nav class="relative z-0 inline-flex justify-center rounded-md shadow-sm" aria-label="pagination">
                        <a 
                            v-for="(link, index) in surveysLinks" :key="index" 
                            href="#"
                            :disabled="!link.url"
                            v-html="link.label"
                            @click="getforpage(event, link)"
                            aria-current="page"
                            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
                            :class="[
                                link.active
                                    ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                
                                index == 0 ? 'rounded-l-md' : '',
                                index == surveysLinks.length - 1 ? 'rounded-r-md' : '',

                            ]"
                        >


                        </a>
                    </nav>
                </div>
                <!-- pagination:end -->
            </div>
        </HeaderLayout>

        <Notification/>
    </div>
</template>
<script>
import HeaderLayout from '../components/HeaderLayout.vue'
import SurveyListItem from '../components/SurveyListItem.vue'
import Notification from '../components/Notification.vue'

import { mapState, mapActions } from 'vuex'
import { useRouter } from 'vue-router' 

export default {
    name: 'Surveys',

    data(){
        return {
            router: useRouter
        }
    },

    components: {
        HeaderLayout,
        SurveyListItem,
        Notification,
    },

    methods: {
        ...mapActions(['getSurveys', 'deleteSurveyV']),
        async deleteSurvey(id){
            if(confirm('Are you sure do you want to delete this survey?')){
                await this.deleteSurveyV(id).then(
                    this.getSurveys()
                )
            }
        },

        getforpage(event, link){
            event.preventDefault();
            if(!link.url || link.active){
                return;
            }

            this.getSurveys({url: link.url});
            
        }
    },

    computed:{
        ...mapState(['surveys', 'surveysLoading', 'loading', 'surveysLinks'])
    },

    created(){
        this.getSurveys();
    },
}
</script>