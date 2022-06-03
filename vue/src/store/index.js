import { createStore } from 'vuex';
import axiosClient from '../axios';

const store = createStore({
    state: {
        user: {
            data: {},
            token: sessionStorage.getItem('TOKEN') ,
        },

        dashboard: {
            loading: false,
            data: {},
        },

        surveys: [],

        surveysLoading: false,

        surveysLinks: [],

        currentSurvey: {
            loading: false,
            data: {},
        },

        questionTypes: ['text', 'select', 'radio', 'checkbox', 'textarea'],

        loading: false,

        notifications: {
            show: false,
            type: null,
            message: null,
        }
    },
    getters: {},
    actions: {
        async getDashboardData({commit}){
            commit('dashboardLoading', true);
            axiosClient.get('/dashboard')
                .then(res => {
                    commit('dashboardLoading', false);
                    commit('setDashboardData', res.data)
                    return res;
                })
                .catch(err => {
                    commit('dashboardLoading', false);
                    throw err
                })
        },

        async saveSurveyAnswer({commit}, { surveyId, answers }){
            return axiosClient.post(`/survey/${surveyId}/answer`, { answers })
                .then( res => {
                    return res;
                })
        },

        async getSurveyBySlug({commit}, slug){
            commit('setCurrentSurveyLoading', true);

            return await axiosClient.get(`/survey-by-slug/${slug}`)
                .then((response) => {
                    commit('setCurrentSurvey', response.data);
                    commit('setCurrentSurveyLoading', false);
                    return response
                })
                .catch(error => {
                    commit('setCurrentSurveyLoading', false);
                    throw error;
                })
        },

        async logout({commit}){
            return await axiosClient.post('/logout')
                .then(res => {
                    commit('logout')
                    return res
                })
        },

        async register({ commit }, user){
            commit('loading', true)
            return await axiosClient.post('/register', user)
                .then(({ data }) => {
                    commit('loading', false)
                    commit('setUser', data);
                    return data;
                })
        },

        async login({ commit }, user){
            commit('loading', true)
            return await axiosClient.post('/login', user)
                .then(({ data }) => {
                    commit('loading', false)
                    commit('setUser', data);
                    commit('notify', {
                        message: 'your login!',
                        type: 'success'
                    });
                    return data;
                })
        },

        async saveSurveyV({ commit }, survey){
            let response;
            if(survey.id) {
                response = await axiosClient.put(`survey/${survey.id}`, survey)
                .then(res => {
                    commit('updateSurvey', res.data)
                    commit('notify', {
                        message: 'survey updated succesfully',
                        type: 'success'
                    });
                    return res;
                })
            } else {
                response = await axiosClient.post('survey', survey)
                .then(res => {
                    commit('saveSurvey', res.data)
                    commit('notify', {
                        message: 'survey created succesfully',
                        type: 'success'
                    });
                    return res
                })
            }

            return response;
        },

        async getSurveys({ commit }, {url = null} = {}){
            url = url || '/survey'

            commit('setSurveyLoading', true)
            return await axiosClient.get(url)
            .then(res => {
                commit('setSurveyLoading', false)
                commit('setSurveys', res.data)
            })
        },

        async getSurvey({ commit }, id){
            commit('setCurrentSurveyLoading', true);

            return await axiosClient.get(`/survey/${id}`)
            .then(res => {
                commit('setCurrentSurvey', res.data);
                commit('setCurrentSurveyLoading', false);
                return res
            })
            .catch(error => {
                commit('setCurrentSurveyLoading', false);
                throw error
            })
        },

        async deleteSurveyV({ commit }, id){
            commit('loading', true)
            return await axiosClient.delete(`survey/${id}`)
            .then(res => {
                commit('loading', false)
                commit('notify', {
                    message: 'survey deleted succesfully',
                    type: 'success'
                });
                return res
            })
        }
    },
    mutations: {
        
        dashboardLoading(state, loading){
            state.dashboard.loading = loading;
        },

        setDashboardData(state, data){
            state.dashboard.data = data;
        },

        setSurveyLoading(state, loading){
            state.surveysLoading = loading;
        },

        loading(state, loading){
            state.loading = loading;
        },

        loadingClientChange(state, loading){
            state.loading = loading;
        },

        setSurveys(state, surveys){
            state.surveys = JSON.parse(JSON.stringify(surveys.data));
            state.surveysLinks = surveys.meta.links
        },

        setCurrentSurveyLoading(state, loading){
            state.currentSurvey.loading = loading;
        },

        setCurrentSurvey(state, survey){
            state.currentSurvey.data = survey.data;
        },

        saveSurvey(state, survey){
            state.surveys = [...state.surveys, survey.data];
        },
        
        updateSurvey(state, survey){
            state.surveys = state.surveys.map(sur => {
                if(sur.id == survey.data.id){
                    return survey.data;
                }
                return sur;
            })
        },

        logout: state => {
            state.user.data = {};
            state.user.token = null;
        },

        setUser: (state, userData) => {
            state.user.token = userData.token;
            state.user.data = userData.user;
            sessionStorage.setItem('TOKEN', userData.token)
        },

        setError: (state, error) => {
            state.error = error
        },

        notify: (state, { message, type }) => {
            state.notifications.show = true;
            state.notifications.message = message;
            state.notifications.type = type;
            setTimeout(() => {
                state.notifications.show = false;
            }, 3000)
        },

    },
    modules: {},
});

export default store;

