// Based on condenser/src/app/utils/Links.js

const urlChar = '[^\\s"<>\\]\\[\\(\\)]';
const urlCharEnd = urlChar.replace(/\]$/, ".,']"); // insert bad chars to end on
const imagePath = '(?:(?:\\.(?:tiff?|jpe?g|gif|png|svg|ico)|ipfs/[a-z\\d]{40,}))';
const domainPath = '(?:[-a-zA-Z0-9\\._]*[-a-zA-Z0-9])';
const urlChars = '(?:' + urlChar + '*' + urlCharEnd + ')?';

const urlSet = (domain: string, path?: string) =>
{
	// urlChars is everything but html or markdown stop chars
	return `https?:\/\/${domain}(?::\\d{2,5})?(?:[/\\?#]${urlChars}${path ? path : ''})${path ? '' : '?'}`;
};

export function GetAnyURLRegExp()
{
	return new RegExp(urlSet(domainPath), 'gi');
}

export function GetLocalURLRegExp()
{
	return new RegExp(urlSet('(?:localhost|(?:.*\\.)?steemit.com)'), 'i');
}

export function GetAnyImageURLRegExp()
{
	return new RegExp(urlSet(domainPath, imagePath), 'gi');
}

export function GetAnyYouTubeURLRegExp()
{
	return new RegExp(urlSet('(?:(?:.*.)?youtube.com|youtu.be)'), 'gi');
}

export function GetYouTubeIDRegExp()
{
	return new RegExp(/(?:(?:youtube.com\/watch\?v=)|(?:youtu.be\/)|(?:youtube.com\/embed\/))([A-Za-z0-9\_\-]+)/i);
}