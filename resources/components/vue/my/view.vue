<template>
	<div class="mypage">
		<div class="form-group">
			<label for="">사용자 ID</label>
			<span class="form-control">{{ userProfile.user_id }}</span>
		</div>
		<div class="form-group">
			<label for="name">이름</label>
			<input type="text" name="name" class="form-control" id="name" placeholder="이름" v-model="userProfile.name">
		</div>
		<div class="form-check">
			<input class="form-check-input" type="checkbox" name="password_change" value="Y" id="password_change" @change="changePassword()">
			<label class="form-check-label" for="password_change">
			비밀번호 갱신
			</label>
		</div>
		<div class="form-group">
			<label for="password">새 비밀번호</label>
			<input type="password" name="password" class="form-control" id="password" placeholder="새 비밀번호" disabled pattern="(?=^.{8,}$)(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$">
		</div>
		<div class="form-group">
			<label for="password_confirmation">새 비밀번호 확인</label>
			<input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="새 비밀번호" disabled pattern="(?=^.{8,}$)(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$">
		</div>
		<div class="form-group">
			<label for="email">이메일</label>
			<input type="text" name="email" class="form-control" id="email" placeholder="이메일" v-model="userProfile.email">
		</div>
		<div class="form-group">
		</div>
		<div class="text-right">
			<button type="button" class="btn btn-primary" @click="change()">변경</button>
		</div>
	</div>
</template>

<script setup>
import { csrfReset } from '../../../js/csrfReset.js';
import Swal from 'sweetalert2';
import { ref, onMounted } from 'vue';

const userProfile = ref([]);

onMounted(() => {
	csrfReset().then(resolve => {
		axios.post(document.querySelector("#userProfile").getAttribute('data'), {
			_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
		}).then(result => {
			userProfile.value = result.data;
		});
	});
});

function changePassword() {
	if(document.getElementsByName('password_change')[0].checked == true) {
		document.getElementById('password').removeAttribute('disabled');
		document.getElementById('password_confirmation').removeAttribute('disabled');
	} else {
		document.getElementById('password').setAttribute('disabled', true);
		document.getElementById('password_confirmation').setAttribute('disabled', true);
	}
}

async function change() {
	csrfReset().then(resolve => {
		Swal.fire({
			title: '정보를 변경 하시겠습니까?',
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: '변경',
			cancelButtonText: '취소',
			showCancelButton: true,
		}).then(result => {
			if(result.isConfirmed) {
				axios.post(document.querySelector("#updateProfile").getAttribute('data'), {
					name: document.getElementsByName('name')[0].value,
					password_change: document.getElementsByName('password_change')[0].checked == true ? 'Y' : 'N',
					password: document.getElementsByName('password')[0].value,
					password_confirmation: document.getElementsByName('password_confirmation')[0].value,
					email: document.getElementsByName('email')[0].value,
					_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
				}).then(complete => {
					Swal.fire({
						title: complete.data.msg,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: '확인'
					});
					
					if(complete.data.status == 'success') {
						location.reload();
					}
				});
			}
		});
	});
}
</script>