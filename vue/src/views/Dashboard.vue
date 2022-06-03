<template>
    <div>
        <HeaderLayout>
            <template v-slot:header>
                <h1 class="text-xl font-bold">Dashboard</h1>
            </template>
            <div v-if="loading" class="flex justify-center">
                Loading...
            </div>
            <div 
                v-else 
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5"
            >
                <div style="animation-delay: 0.1s;" class="animate-fade-in-down bg-white shadow-md p-3 text-center flex flex-col order-1 lg:order-2">
                    <h3 class="text-2xl font-semibold">Total survey</h3>
                    <div class="text-8xl font-bold flex-1 flex items-center justify-center">
                        {{ data.totalSurveys }}
                    </div>
                </div>
                <div style="animation-delay: 0.2s;" class="animate-fade-in-down bg-white shadow-md p-3 text-center flex flex-col order-2 lg:order-4">
                    <h3 class="text-2xl font-semibold">Total Answers</h3>
                    <div class="text-8xl font-bold flex-1 flex items-center justify-center">
                        {{ data.totalAnswers }}
                    </div>
                </div>
                <div style="animation-delay: 0.3s;" class="animate-fade-in-down row-span-2 bg-white shadow-md p-3 order-3 lg:order-1">
                    <h3 class="text-2xl font-semibold">Latest Survey</h3>
                    <img :src="data.latesSurvey.image_url" class="w-[240px] mx-auto py-5" alt="">
                    <h3 class="font-bold text-xl mb-3">{{ data.latesSurvey.title }}</h3>
                    <div class="flex justify-between text-sm mb-1">
                        <div>Upload Data:</div>
                        <div>{{ data.latesSurvey.created_at }}</div>
                    </div>

                    <div class="flex justify-between text-sm mb-3">
                        <div>Answers:</div>
                        <div>{{ data.totalAnswers }}</div>
                    </div>
                    <div class="flex justify-between">
                        <router-link :to="{name: 'SurveyView', params: { id: data.latesSurvey.id }}" class="px-4 py-2 rounded-md text-indigo-500 duration-150 hover:text-white hover:bg-indigo-500">Edit Survey</router-link>
                    </div>
                </div>

                <div style="animation-delay: 0.4s;" class=" animate-fade-in-down bg-white shadow-md p-3 row-span-2 order-4 lg:order-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-semibold">Latest Answers</h3>
                    
                        <a href="#" class="text-sm text-blue-500 hover:decoration-blue-500">
                            View All
                        </a>
                    </div>
                    <a href="#" v-for="answer of data.latestAnswers" :key="answer.id" class="block p-2 hover:bg-gray-100/90">
                        <div class="font-semibold">{{ answer.survey.title }}</div>
                        <small>
                            Answer Made at:
                            <i class="font-semibold">{{ answer.end_data }}</i>
                        </small>
                    </a>
                </div>
            </div>
        </HeaderLayout>
    </div>
</template>

<script setup>
import HeaderLayout from '../components/HeaderLayout.vue'
import { computed } from 'vue'
import { useStore } from 'vuex'

const store = useStore();

const loading = computed(() => store.state.dashboard.loading)
const data = computed(() => store.state.dashboard.data)

store.dispatch('getDashboardData')


</script>

<style>

</style>