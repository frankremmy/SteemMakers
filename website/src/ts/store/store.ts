import Vue from 'vue'
import VueRouter from 'vue-router';
import Vuex from 'vuex'
import {SteemConnectManager} from '../utils/vendor/SteemConnectManager'

Vue.use(Vuex);

const steemConnectManager = new SteemConnectManager();

export const store = new Vuex.Store({
	strict: true,
	state:
	{
		
	},
	actions:
	{
		login (context)
		{
			steemConnectManager.login();
		}
	}
})