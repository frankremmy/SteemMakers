export function parsePayoutAmount(amount :string)
{
	return parseFloat(String(amount).replace(/\s[A-Z]*$/, ''));
}