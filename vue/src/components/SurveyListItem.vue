<template>
    <div>
        <div class="flex flex-col py-4 px-6 shadow-md bg-white hover:bg-gray-50 h-[470px] rounded-lg relative">
            <div class="absolute px-3 py-2 rounded-lg text-sm font-bold top-6 left-8 bg-white">
                By {{ survey.user }}
            </div>
            <img v-if="survey.image_url" :src="survey.image_url" :alt="survey.title" class="w-full h-48 object-cover rounded-lg">
            <h4 class="mt-4 text-lg font-bold">{{ survey.title }}</h4>
            <div v-html="survey.description" class="over-flow-hidden  flex-1"></div>
            <div class="flex justify-between items-center mt-3">
                <router-link :to="{ name: 'SurveyView', params: { id: survey.id } }" class="flex py-2 px-4 border border-transparent text-sm rounded-md text-white bg-indigo-600 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Edit
                </router-link>
                <div class="flex items-center">
                    <a 
                        :href="`view/survey/${survey.slug}`"
                        target="_blank"
                        class="mx-4 px-4 py-2 flex items-center justify-center rounded-lg border border-transparent text-white bg-green-500 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Start
                    </a>
                    <button 
                        v-if="survey.id"
                        type="button"
                        @click="emit('delete', survey.id)"
                        class="px-4 py-2 bg-red-500 text-white rounded-md border border-transparent focus:ring-2 focus:ring-red-400 focus:ring-offset-2">
                        
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineEmits, defineProps } from 'vue'
 
defineProps({
    survey: Object
});

const emit = defineEmits(['delete']);
</script>

<style>

</style>