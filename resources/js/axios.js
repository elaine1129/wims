import axios from 'axios';
import store from './store';
import router from "./router";

const axiosClient = axios.create({
    baseURL: 'http://localhost:8000/api'

})

axiosClient.interceptors.request.use(config => {
    config.headers.Authorization = `Bearer ${store.state.user.token}`
    return config;
})

axiosClient.interceptors.response.use((response) => {
    console.log("from axiosCLient", response);
    if (response.status === 403) {

        alert("Your login is expired. Please login again");
        sessionStorage.clear();
    }
    return response;
}, (error) => {
    if (error.response && error.response.data) {
        //add your code
        if (error.response.status === 403) {

            alert("Your login is expired. Please login again");
            sessionStorage.clear();
            window.location.reload();
        }
        console.log("from axiosCLient error", error);
        return Promise.reject(error);
    }
    return Promise.reject(error.message);
});

export default axiosClient;