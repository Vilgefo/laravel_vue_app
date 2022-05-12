<template>
    <page-component>
        <template v-slot:header>
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Surveys</h1>
                <router-link :to="{name: 'SurveyCreate'}"
                             class="py-2 px-3 text-white bg-emerald-500 rounded-md hover:bg-emerald-600 flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Add new survey
                </router-link>
            </div>
        </template>
        <div  class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3">
            <SurveyListItem v-for="survey in surveys"
                            :key="survey.id"
                            :survey="survey"
                            @delete="deleteSurvey(survey)"/>
        </div>
    </page-component>
</template>

<script setup>
import PageComponent from '../components/PageComponent.vue'
import store from "../store";
import {computed} from "vue";
import SurveyListItem from "../components/SurveyListItem.vue";

const surveys = computed(() => store.state.surveys.data)
store.dispatch("getSurveys");
function deleteSurvey(survey) {
    if (confirm("Are you sure you want to delete this survey? Operation can't be undone!!")) {
        store.dispatch('deleteSurvey', survey.id).then(()=>{
            store.dispatch('getSurveys')
        })
    }
}
</script>
