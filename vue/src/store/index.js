import {createStore} from "vuex";
import axiosClient from "../axios";

const store = createStore({
    state: {
        user: {
            data: {},
            token: sessionStorage.getItem('TOKEN'),

        },
        currentSurvey: {
            loading: false,
            data: {}
        },
        surveys: {
            loading: false,
            data: []
        },
        questionTypes: ['text', 'select', 'radio', 'checkbox', 'textarea'],
        notification: {
            show: false,
            type: 'success',
            message: ''
        }
    },
    getters: {},
    actions: {
        getSurveys({ commit }, {url = null} = {}) {
            commit('setSurveysLoading', true)
            url = url || "/survey";
            return axiosClient.get(url).then((res) => {
                commit('setSurveysLoading', false)
                commit("setSurveys", res.data);
                return res;
            });
        },
        getSurvey({ commit }, id) {
            commit("setCurrentSurveyLoading", true);
            return axiosClient
                .get(`/survey/${id}`)
                .then((res) => {
                    commit("setCurrentSurvey", res.data);
                    commit("setCurrentSurveyLoading", false);
                    return res;
                })
                .catch((err) => {
                    commit("setCurrentSurveyLoading", false);
                    throw err;
                });
        },
        register({commit}, user) {
            return axiosClient.post('/register', user).then(({data}) => {
                commit('setUser', data)
                return data
            })
        },
        login({commit}, user) {
            return axiosClient.post('/login', user).then(({data}) => {
                commit('setUser', data)
                return data
            })
        },
        logout({commit}) {
            return axiosClient.post('/logout').then(response => {
                commit('logout')
                return response
            })
        },
        saveSurvey({commit}, survey) {
            delete survey.image_url
            let response
            if (survey.id) {
                response = axiosClient.put(`/survey/${survey.id}`, survey).then(res=>{
                    commit('setCurrentSurvey', res.data);
                    return res;
                })
            } else {
                response = axiosClient.post(`/survey`, survey).then(res=>{
                    commit('setCurrentSurvey', res.data);
                    return res;
                })
            }
            return response
        },
        deleteSurvey({  }, id) {
            return axiosClient.delete(`/survey/${id}`).then((res) => {
                return res;
            });
        },
    },
    mutations: {
        setSurveysLoading: (state, loading) => {
              state.surveys.loading = loading
        },
        setCurrentSurveyLoading: (state, loading) => {
            state.currentSurvey.loading = loading;
        },
        setCurrentSurvey: (state, survey) => {
            state.currentSurvey.data = survey.data;
        },
        setSurveys: (state, surveys)=>{
            state.surveys.data = surveys.data
        },
        logout: state => {
            state.user.data = {}
            state.user.token = null
            sessionStorage.clear();
        },
        setUser: (state, userData) => {
            state.user.token = userData.token;
            state.user.data = userData.user;
            console.log(userData, state.user);
            sessionStorage.setItem('TOKEN', userData.token)
        },
        notify: (state, {message, type}) => {
            state.notification.show = true;
            state.notification.type = type;
            state.notification.message = message;
            setTimeout(() => {
                state.notification.show = false;
            }, 3000)
        },
    }, modules: {}
})

export default store;
