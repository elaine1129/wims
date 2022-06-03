require('./bootstrap');

import { createApp } from 'vue'
import HelloWorld from './components/welcome'
import router from './router'

const app = createApp({})

app.component('hello-world', HelloWorld)
app.use(router)
app.mount('#app')