import Vue from 'vue'
import router from './router'
import App from './App.vue'
import BootstrapVue from 'bootstrap-vue'

import './assets/bootstrap.min.css'
import './assets/main.css'

Vue.use(BootstrapVue);

let v = new Vue({
	el: 'app',
	router,
	components:{App}
});