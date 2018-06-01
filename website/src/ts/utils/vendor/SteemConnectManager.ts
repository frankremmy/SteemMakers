import VueRouter from 'vue-router';
import {store} from '../../store/store'
import * as sc2 from 'sc2-sdk';

declare var SC_CALLBACK_URL: string;

export class SteemConnectManager
{
	private steemConnect :SC2;
	private _accessToken : string = '';
	constructor()
	{
		this.steemConnect = sc2.Initialize({
			app: 'steemmakers.app',
			callbackURL: SC_CALLBACK_URL,
			scope: ['login'],
		});
	}

	set accessToken(newAccessToken: string)
	{
		this._accessToken = newAccessToken;
		this.steemConnect.setAccessToken(newAccessToken);
	}

	login()
	{
		let loginURL: string = this.steemConnect.getLoginURL();
		window.location.href = loginURL;
	}

	requestProfileInfoUpdate()
	{
		this.steemConnect.me(function (err, result)
		{
			if (!err)
			{
				if(result.account.json_metadata)
				{
					let profileImage = JSON.parse(result.account.json_metadata)['profile']['profile_image'];
					store.commit('profileUpdated', profileImage);
				}
			}
		});
	}
}