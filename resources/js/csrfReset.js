export function csrfReset() {
	return new Promise(function(resolve, reject) {
		axios.get("/csrfReset").then(response => {
			document.querySelector('meta[name="csrf-token"]').setAttribute('content', response.data.token);
			var hidden_token = document.getElementsByName('_token');
			if(hidden_token.length > 0) {
				for(var i = 0; i < hidden_token.length; i++) {
					document.getElementsByName('_token')[i].value = response.data.token;
				}
			}
			resolve();
		});		
	});
}