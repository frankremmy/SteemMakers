// Based on busy/src/client/helpers/image.js

const IMG_PROXY = 'https://steemitimages.com/0x0/';

export function proxyfyImageURL(url :string) :string
{
	return `${IMG_PROXY}${url}`;
};