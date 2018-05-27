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
		username: null,
		isLoggedIn: false
	},
	mutations:
	{
		authenticated (state, authenticationData)
		{
			state.username = authenticationData.username;
			state.isLoggedIn = true;
		},
		logout (state)
		{
			state.isLoggedIn = false;
			state.username = null;
		}
	},
	actions:
	{
		login (context)
		{
			steemConnectManager.login();
		}
	}
})