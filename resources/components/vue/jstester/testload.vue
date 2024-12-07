<template>
	<div id="codeList">
		<div style="width: 100%; height: 35px; display: flex; align-items: center;">
			<button type="button" style="margin-left: auto; margin-right: 5px;" @click="$emit('close')">X</button>
		</div>
		<div style="position: relative">
			<table width="100%">
				<tr>
					<th>저장된 코드</th>
					<th>저장일</th>
					<th>불러오기</th>
					<th>삭제</th>
				</tr>
				<tr v-for="list in codeLists">
					<td>{{ list.view_name }}</td>
					<td>{{ list.created_at }}</td>
					<td><button type="button" @click="$emit('view', list.id)">불러오기</button></td>
					<td><button type="button" @click="$emit('delete', list.id)">삭제</button></td>
				</tr>
				<tr style="align: center">
					<ul>
						<li v-for="page in pagination" @click="$emit('load', page)">[{{ page }}]</li>
					</ul>
				</tr>
			</table>
		</div>
	</div>
</template>

<script setup>
import { defineModel } from 'vue';
const codeLists = defineModel('codeLists');
const pagination = defineModel('pagination');
</script>