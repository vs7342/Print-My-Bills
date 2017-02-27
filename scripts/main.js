/**
 * Function to display a temporary message. Its CSS properties are based on the type of message.
 */
function displayTemporaryMessage(type, msg){
	var msgTypeClass;
	
	switch(type){
		case 'Success':
			msgTypeClass = 'alert-success';
			break;
			
		case 'Failure':
			msgTypeClass = 'alert-danger';
			break;
			
		case 'Warning':
			msgTypeClass = 'alert-warning';
			break;
			
		default :
			msgTypeClass = 'alert-info';
			break;
		
	}
	
	document.getElementById('userMessages').innerHTML = 
	'<div class="alert '+msgTypeClass+' alert-dismissible fade in tempMsg" role="alert">\
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\
		 <span aria-hidden="true">&times;</span>\
	  </button>\
	  <strong>'+type+'</strong> '+msg+'\
	</div>';
	
	$('.tempMsg').fadeIn('slow').delay(2750).hide(250);
	
	document.body.scrollTop = document.documentElement.scrollTop = 0;
}