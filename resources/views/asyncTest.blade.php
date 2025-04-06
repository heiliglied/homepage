<!DOCTYPE html>
<html>
<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div id="testView"></div>

<button type="button" onClick="asyncProgressTest()">연동 테스트</button><br/>
<button type="button" onClick="pubsubTest()">PUB/SUB 테스트</button>
</body>
<script>
function asyncProgressTest() {
	let view = document.getElementById("testView");
	let xhr = new XMLHttpRequest();
	xhr.open('GET', '/test/getAsyncData', true);
	
    xhr.onprogress = function (event) {
        // 부분 데이터 처리
        let responseText = event.target.responseText.trim();
        let lines = responseText.split("\n");
        let lastLine = lines[lines.length - 1]; // 마지막 청크 처리
        try {
            let data = JSON.parse(lastLine);
            if (data.status === 'loading') {
                view.innerHTML += '진행 중: ' + data.msg + '<br>';
            } else if (data.status === 'finish') {
                view.innerHTML += '<strong>작업 완료!</strong>';
            }
        } catch (e) {
            console.error('JSON 파싱 오류', e);
        }
    };
    xhr.onload = function () {
        alert('최종 작업 완료!');
    };
    xhr.send();
}

function pubsubTest() {
	let jobkey = crypto.randomUUID();
	
	let xhr = new XMLHttpRequest();
	xhr.open('POST', '/test/likepubsub', true);
	xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
	xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                let response = JSON.parse(xhr.responseText);
                console.log(response);
            } else {
                console.error('Error:', xhr.status, xhr.statusText);
            }
        }
    };

    let params = {
        jobkey: jobkey,
        type: 'create' // 필요한 타입 설정
    };

    xhr.send(JSON.stringify(params)); // JSON 형태로 파라미터 전송
}
/*
function asyncProgressTest() {
	let view = document.getElementById("testView");
	let previousLength = 0;
	let xmlHttpRequest = new XMLHttpRequest();
	xmlHttpRequest.open('GET', '/test/getAsyncData');
	xmlHttpRequest.send(null);
	xmlHttpRequest.onreadystatechange = function () {
		if (xmlHttpRequest.status == 200) {
			let request = xmlHttpRequest.responseText;
			if (xmlHttpRequest.readyState == XMLHttpRequest.LOADING) {
				let newData = request.substring(previousLength).trim();
				console.log(newData);
				view.innerHTML = view.value + newData;
				previousLength = request.length;
			}
			// All request COMPLETE (FINISH/DONE)
			if (xmlHttpRequest.readyState == XMLHttpRequest.DONE) {
				let endData = request.substring(previousLength).trim();
				console.log(endData);
				view.innerHTML = view.value + endData;
				alert('끝?');
			}
		}
	}
}
*/
</script>
</html>