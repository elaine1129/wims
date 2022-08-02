import axios from 'axios';
import store from './store';

const axiosClient = axios.create({
    baseURL: 'http://localhost:8000/api'

})

axiosClient.interceptors.request.use(config => {
    config.headers.Authorization = `Bearer ${store.state.user.token}`
    return config;
})

axiosClient.interceptors.response.use((response) => {
    if (response.status === 403) {

        alert("Your login is expired. Please login again");
        sessionStorage.removeItem("TOKEN");
    }
    return response;
}, (error) => {
    if (error.response && error.response.data) {
        //add your code
        return Promise.reject(error);
    }
    return Promise.reject(error.message);
});

export default axiosClient;