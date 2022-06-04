require('./bootstrap');

import { createApp } from 'vue'
import DashBoard from './components/dashboard'
import router from './router'
import ViewUIPlus from 'view-ui-plus'
import 'view-ui-plus/dist/styles/viewuiplus.css'
import 'jquery/dist/jquery.min.js';
//Datatable Modules
import "datatables.net-dt/js/dataTables.dataTables"
import "datatables.net-dt/css/jquery.dataTables.min.css"
const app = createApp({})

app.component('top-side-dashboard', DashBoard)
app.use(router)
app.use(ViewUIPlus)
app.mount('#app')