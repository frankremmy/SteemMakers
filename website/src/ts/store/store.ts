import Vue from 'vue';
import axios from 'axios';
import Vuex from 'vuex';
import {SteemConnectManager} from '../utils/vendor/SteemConnectManager';
import * as Cookies from 'js-cookie';

Vue.use(Vuex);

const steemConnectManager = new SteemConnectManager();

export const store = new Vuex.Store({
	strict: true,
	state:
	{
		isLoggedIn: false,
		isReviewer: false,
		profileImage: null,
		username: '',			// fixed
		accessToken: '',
		expiresInDays: 0,
	},
	mutations:
	{
		authenticate (state, authenticationData)
		{
			state.isLoggedIn = true;
			state.username = authenticationData.username;
			state.accessToken = authenticationData.accessToken;
			state.expiresInDays = authenticationData.expiresInDays;
			steemConnectManager.accessToken = authenticationData.accessToken;

			var auth = 'Basic ' + new Buffer(state.username + ':' + state.accessToken).toString('base64');
			axios.defaults.headers.common['Authorization'] = auth;
		},
		isReviewer(state, isReviewer)
		{
			state.isReviewer = isReviewer;
		},
		profileUpdated(state, profileData)
		{
			state.profileImage = profileData;
		},
		logout (state)
		{
			state.isLoggedIn = false;
			state.isReviewer = false;
			state.profileImage = null;
			state.username = '';
			state.accessToken = '';
			state.expiresInDays = 0;
			steemConnectManager.accessToken = '';
		}
	},
	actions:
	{
		initialiseStore(context)
		{
			let username :string|undefined = Cookies.get('username');
			let accessToken :string|undefined = Cookies.get('access_token');
			let expiresIn :string|undefined = Cookies.get('expires_in');

			if(username !== undefined && accessToken !== undefined && expiresIn !== undefined)
			{
				context.commit('authenticate',
				{
					username: username,
					accessToken: accessToken,
					expiresIn: parseInt(expiresIn)
				});

				steemConnectManager.requestProfileInfoUpdate();
				context.dispatch('authorize');
			}
		},
		login (context)
		{
			steemConnectManager.login();
		},
		logout (context)
		{
			context.commit('logout');
			Cookies.remove('username', { path: '/' });
			Cookies.remove('access_token', { path: '/' });
			Cookies.remove('expires_in', { path: '/' });
		},
		authenticated (context, authenticationData)
		{
			context.commit('authenticate', authenticationData);
			Cookies.set('username', authenticationData.username, { expires:  authenticationData.expiresInDays, path: '/' });
			Cookies.set('access_token', authenticationData.accessToken, { expires:  authenticationData.expiresInDays, path: '/' });
			Cookies.set('expires_in', authenticationData.expiresInDays, { expires:  authenticationData.expiresInDays, path: '/' });
			
			steemConnectManager.requestProfileInfoUpdate();
			context.dispatch('authorize');
		},
		authorize(context)
		{
			fetch("./api/v1/profiles.php?usernames=" + context.state.username)
				.then(response => response.json())
				.then((data) =>
				{
					let isReviewer :boolean = false;
					if(data.length > 0 && data[0].reviewer == 1)
					{
						isReviewer = true;
					}
					context.commit('isReviewer', isReviewer);
				});
		}
	}
});