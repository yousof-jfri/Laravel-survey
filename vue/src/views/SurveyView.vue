<template>
    <div>
        
        <HeaderLayout>
            <template v-slot:header>
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-bold">{{ model.id ? model.title : 'Create a Survey' }}</h1>
                    <button v-if="this.$route.params.id" @click="deleteSurvey()" class="rounded-md bg-red-500 px-3 py-2 font-semibold text-white">{{ loading ? 'deleting...' : 'Delete this survey' }}</button>
                </div>
            </template>

            <div v-if="surveyLoading" class="flex justify-center">Loading...</div>

            <div v-if="!surveyLoading">
                <form @submit.prevent="saveSurvey" class="animate-fade-in-down">
                    <div class="shadow sm:rounded-md sm:overflow-hidden">
                        <!-- Survey Fields:start -->
                        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                            <!-- image -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Image
                                </label>
                                <div class="mt-1 flex items-center">
                                    <img v-if="model.image_url" :src="model.image_url" :alt="model.title" class="w-64 h-48 object-cover">
                                    <span v-else class="flex items-center justify-center h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                                        P
                                    </span>
                                    <button type="button" class="relative overflow-hidden ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <input type="file" @change="onImageChoose" class="absolute left-0 top-0 right-0 bottom-0 opacity-0 cusor-pointer">
                                        Change
                                    </button>
                                </div>
                            </div>

                            <!-- title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>

                                <input placeholder="title of your survey" type="text" name="title" id="title" v-model="model.title" autocomplete="survey_title" class="mt-1 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <!-- description -->
                            <div>
                                <label for="about" class="block text-sm font-medium text-gray-700">Description</label>

                                <textarea placeholder="descrip your survey" rows="3" type="text" name="description" id="about" v-model="model.description" autocomplete="survey_description" class="mt-1 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>

                            <!-- expire date -->
                            <div>
                                <label for="expire_data" class="block text-sm font-medium text-gray-700">Expire date</label>
                                <input type="date" name="expire_data" id="expire_data" v-model="model.expire_data" class="mt-1 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 border border-gray-300 shadow-sm rounded-md px-4 py-1 focus:border-indigo-500">
                            </div>

                            <!-- status -->
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" id="status" name="status" v-model="model.status" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>

                                <div class="ml-3 text-sm">
                                    <label for="status" class="font-medium text-gray-700">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- Survey Fields:end -->

                        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                            <h3 class="text-2xl font-semibold flex items-center justify-between">
                                Questions

                                <button type="button" @click="addQuestion()" class="flex items-center text-sm py-1 px-4 rounded-sm text-white bg-gray-600 hover:bg-gray-700">Add Question</button>
                            </h3>

                            <div v-if="!model.questions.length" class="text-center text-gray-700">
                                you don't have any question
                            </div>
                            <div v-else v-for="(question, index) in model.questions" :key="question.id">
                                <QuestionEditor
                                    :question="question"
                                    :index="index"
                                    @change="questionChange"
                                    @addQuestion="addQuestion"
                                    @deleteQuestion="deleteQuestion">
                                    </QuestionEditor>
                            </div>
                        </div>

                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </HeaderLayout>
    </div>
</template>

<script>
import { v4 as uuidv4 } from 'uuid'
import HeaderLayout from '../components/HeaderLayout.vue'
import QuestionEditor from '../components/QuestionEditor.vue'

import { useRouter } from 'vue-router'

import { mapState, mapActions } from 'vuex'

export default {
    name: 'SurveyView',

    components: {
        HeaderLayout,
        QuestionEditor
    },

    data(){
        return {
            model: {
                title: '',
                status: false,
                description: null,
                image: null,
                image_url: null,
                expire_data: null,
                questions: [],
            },

            router : useRouter(),
        }
    },

    computed: {
        ...mapState(['surveys', 'currentSurvey', 'loading']),

        surveyLoading(){
            return this.currentSurvey.loading;
        }
    },

    async mounted(){
        if(this.$route.params.id){

            await this.getSurvey(this.$route.params.id)

            this.model = JSON.parse(localStorage.getItem('survey'))
        }
    },

     watch:{
        'currentSurvey.data': (newVal, oldVal) => {
            let survey = {
                ...JSON.parse(JSON.stringify(newVal)),
                status: newVal.status !== 'draft',
            }

            localStorage.setItem('survey', JSON.stringify(survey));
        }
    },

    methods: {
        ...mapActions(['saveSurveyV', 'getSurvey', 'deleteSurveyV']),

        deleteSurvey(){

            if (confirm('are you sure do you want to delete this quiz')) {
                this.deleteSurveyV(this.model.id).then(res => {
                    this.router.push({
                        name: 'Survey'
                    })
                })
                console.log('work')
            }
        },

        saveSurvey(){
            this.saveSurveyV(this.model).then(({ data }) => {
                this.router.push({
                    name: 'Survey',
                })
            })
        },

        onImageChoose(event){
            const file = event.target.files[0];

            const reader = new FileReader();
            reader.onload = () => {
                this.model.image = reader.result;  
                this.model.image_url = reader.result; 
            }
            reader.readAsDataURL(file);
        },

        addQuestion(index){
            const newQuestion = {
                id: uuidv4(),
                type: "text",
                question: '',
                description: null,
                data: {}
            }

            this.model.questions.splice(index, 0, newQuestion);
        },

        deleteQuestion(question){
            this.model.questions = this.model.questions.filter(ques => {
                return ques !== question
            })
        },

        questionChange(questions){
            this.model.questions = this.model.questions.map(ques => {
                if(ques.id === questions.id){
                    return JSON.parse(JSON.stringify(questions));
                }
                return ques;
            })
        }
    }
}
</script>

<style>

</style>