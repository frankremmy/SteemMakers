/* https://helloacm.com/api/steemit/converter/?cached */


function initDelegation(){

	$( "input[name='sp']" ).change(function() {
  		$( "input[name='vesting_shares']" ).val( $( "input[name='sp']" ).val() + ' SP' );
	});

	$.getJSON( "https://uploadbeta.com/api/steemit/delegators/?cached&id=steemmakers", function( data ) {
		var items = [];
		$.each( data, function( key, value ) {
			items.push( "<tr><td><a href='https://steemit.com/@" +value['delegator'] +"/'>@" + value['delegator'] + "</a></td><td></td><td>" + Math.round(value['sp']) +  "</td></tr>" );
		});
		
		$('#result tr:last').after(items.join( "" ));
	});
}

