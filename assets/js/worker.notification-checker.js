var ajax = new XMLHttpRequest(),
	result,
	i = 0,
	accessType = '';

function checkNotification() {
	i = i + 1;
	ajax.open("POST", 'http://localhost:7575/asmp/' + accessType + '/check_new_im');
	ajax.setRequestHeader("Content-Type", "application/json");
	ajax.onreadystatechange = function () { // Call a function when the state changes.
		if (this.readyState === ajax.DONE && this.status === 200) {
			result = this.responseText;
			postMessage({
				counter: i,
				result
			});
		}
	}
	ajax.send();
	setTimeout("checkNotification()", 2000);
}

onmessage = function (e) {
	accessType = e.data;
	checkNotification();
}