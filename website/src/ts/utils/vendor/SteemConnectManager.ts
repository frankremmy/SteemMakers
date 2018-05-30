import VueRouter from 'vue-router';
import * as sc2 from 'sc2-sdk';

declare var SC_CALLBACK_URL: string;

export class SteemConnectManager
{
	private steemConnect :SC2;
	constructor()
	{
		this.steemConnect = sc2.Initialize({
			app: 'steemmakers.app',
			callbackURL: SC_CALLBACK_URL,
			scope: ['login'],
		});
	}

	login()
	{
		let loginURL: string = this.steemConnect.getLoginURL();
		window.location.href = loginURL;
	}
}