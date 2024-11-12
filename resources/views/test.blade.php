<!DOCTYPE html>
<html>
<head>
</head>
<body>
<div style="width: 360px;">
	<div style="width: 100%; height: 35px; display: flex; align-items: center;">
		<button type="button" style="margin-left: auto; margin-right: 5px;">X</button>
	</div>
	<table width="100%">
		<tr>
			<th>저장된 코드</th>
			<th>저장일</th>
			<th>불러오기</th>
		</tr>
		<tr v-for="list in codelists">
			<td>{{ list.view_name }}</td>
			<td>{{ list.created_at }}</td>
			<td><button type="button">불러오기</button></td>
		</tr>
		<tr>
			{{ pagination }}
		</tr>
	</table>
</div>
</body>
</html>