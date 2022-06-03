<template>
    <div>
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold">
                {{ index + 1 }}.{{ model.question }}
            </h3>
            <div class="flex items-center">
                <!-- add new Question -->
                <button type="button" @click="addQuestion()" class="flex items-center text-xs py-1 px-3 mr-2 rounded-sm text-white bg-gray-600 hover:bg-gray-700">Add</button>

                <!-- deleteQuestion -->
                <button type="button" @click="deleteQuestion()" class="flex items-center text-xs py-1 px-3 mr-2 rounded-sm text-white bg-red-600 hover:bg-red-700">Delete</button>
            </div>
        </div>
        <div class="grid gap-3 grid-cols-12">
            <!-- Question -->
            <div class="mt-3 col-span-9">
                <label :for="'question_text_' + model.data" class="block text-sm font-medium text-gray-700">Question Text</label>

                <input type="text" :name="'question_text_' + model.data" v-model="model.question" @change="dataChange" :id="'question_text_' + model.data" class="mt-1 focus:ring-2 focus:ring-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded">
            </div>

            <!-- Question Type -->
            <div class="mt-3 col-span-3">
                <label for="question_type" class="block text-sm font-medium text-gray-700">Select Question Type</label>

                <select name="question_type" id="question_type" v-model="model.type" @change="typeChange" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option v-for="type in questionTypes" :key="type" :value="type">{{ upperCaseFirst(type) }}</option>
                </select>
            </div>
        </div>

        <!-- Question Description -->
        <div class="mt-2 col-span-9">
            <label :for="'question_description_' + model.id " class="block text-sm font-medium text-gray-700">Description</label>

            <textarea :name="'question_description_' + model.id " :id="'question_description_' + model.id "  rows="3" v-model="model.description" @change="dataChange" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
        </div>

        <!-- Data -->
        <div>
            <div v-if="shouldHaveOptions()" class="mt-2">
                <h4 class="">
                    Options

                    <!-- Add new option -->
                    <button type="button" @click="addOption()" class="flex items-center text-xs py-1 px-2 rounded-sm text-white bg-gray-600 hover:bg-gray-700">
                        Add Option
                    </button>

                </h4>
            </div>
            <div v-else class="text-xs text-gray-600 text-center py-3">
                you don't have any options defined
            </div>
            <div v-if="shouldHaveOptions()" class="mt-3">
                <div v-for="( option, index ) in model.data.options" :key="option.uuid" class="flex items-center mb-1">
                    <span class="w-6 text-sm">{{ index + 1 }}.</span>
                    <input type="text" v-model="option.text" @change="dataChange" class="w-full rounded-sm py-1 px-2 text-xs border border-gray-300 focus:border-indigo-500">

                    <!-- delete -->
                    <button class="px-2 font-bold bg-red-500 mx-4 text-white rounded hover:bg-red-600" @click="removeOption(option)">X</button>
                </div>
            </div>
        </div>


        <hr class="my-4">

    </div>
</template>

<script setup>
    import { ref, computed } from 'vue'
    import { v4 as uuidv4 } from 'uuid'
    import store from '../store'

    const props = defineProps({
        question: Object,
        index: Number,
    });


    const emit = defineEmits(['change', 'addQuestion', 'deleteQuestion']);

    const model = ref(JSON.parse(JSON.stringify(props.question)));

    const questionTypes = computed(() => {
        return store.state.questionTypes;
    });


    // functions

    function upperCaseFirst(str){
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function shouldHaveOptions(){
        return ['select', 'radio', 'checkbox'].includes(model.value.type)
    }

    function getOptions(){
        return model.value.data.options;
    }

    function setOptions(options){
        return model.value.data.options = options;
    }

    //add option 
    function addOption(){
        setOptions([
            ...getOptions(),
            { uuid: uuidv4(), text: '' },
        ])
        dataChange()
    }

    function removeOption(option){
        setOptions(getOptions().filter(op => op !== option))
        dataChange()
    }

    function typeChange(){
        if(shouldHaveOptions()){
            setOptions(getOptions() || []);
        } 
        dataChange()
    }

    function dataChange(){
        const data = JSON.parse(JSON.stringify(model.value));
        if(!shouldHaveOptions()){
            delete data.data.options;
        }

        emit('change', data)
    }

    function addQuestion(){
        emit('addQuestion', props.index + 1);
    }

    function deleteQuestion(){
        emit('deleteQuestion', props.question)
    }
</script>

<style>

</style>