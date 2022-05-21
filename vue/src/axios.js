import axios from "axios";
import store from './store';
import router from "./router";

const axiosClient = axios.create({
    baseURL: `${import.meta.env.VITE_API_BASE_URL}/api`
})

axiosClient.interceptors.request.use((config)=>{
    config.headers.Authorization = `Bearer ${store.state.user.token}`
    return config;
})
axiosClient.interceptors.response.use(undefined, (error) => {
    if (error.response && error.response.status === 401) {
        store.commit('logout');
        router.push({name: 'Login'});
    }
    if (error.response && error.response.status === 404) {
        router.push({name: 'Dashboard'});
    }
    return Promise.reject({...error.response.data, responceCode: error.response.status});
});
export default axiosClient
