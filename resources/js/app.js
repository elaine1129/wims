require('./bootstrap');

import { createApp } from 'vue'
import DashBoard from './components/dashboard'
import router from './router'
import locale from 'view-ui-plus/dist/locale/en-US';

import ViewUIPlus from 'view-ui-plus'
import 'view-ui-plus/dist/styles/viewuiplus.css'
import 'jquery/dist/jquery.min.js';
import '../../public/css/tailwind.css'
//Datatable Modules
import "datatables.net-dt/js/dataTables.dataTables"
import "datatables.net-dt/css/jquery.dataTables.min.css"
import store from './store'
import common from './common';
const app = createApp({})

app.component('top-side-dashboard', DashBoard)
app.use(router)
app.use(ViewUIPlus, {
    locale
})
app.mixin(common) //mixin merge methods in the file to all components, so that they can be used anywhere
app.use(store)
app.mount('#app')