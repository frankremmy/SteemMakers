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
		isLoggedIn: false,
		profileImage: null,
		username: null,			// fixed
		accountName: null,		// can be changed?
		accessToken: '',
	},
	mutations:
	{
		authenticated (state, authenticationData)
		{
			state.isLoggedIn = true;
			state.username = authenticationData.username;
			state.accessToken = authenticationData.accessToken;
			steemConnectManager.accessToken = authenticationData.accessToken;
			steemConnectManager.requestProfileInfoUpdate();
			
		},
		profileUpdated(state, profileData)
		{
			state.profileImage = profileData;
		},
		logout (state)
		{
			state.isLoggedIn = false;
			state.profileImage = null;
			state.username = null;
		}
	},
	actions:
	{
		login (context)
		{
			steemConnectManager.login();
		},
	}
});