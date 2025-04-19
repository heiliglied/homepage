<template>
	<div class="contents bg-white">
		<div class="sign_layer">
			<div class="sign_form">				
				<span class="sign_title">가입확인</span>
				<div class="form-group">
					<label for="email">이메일 입력</label>
					<div class="input-group">
						<input type="text" name="email" v-model="email" class="form-control" required>
					</div>
				</div>
				<div class="form-group" id="error_msg">
					
				</div>
				<span class="sign_btn">
					<button type="button" @click="findId()" class="btn btn-primary">ID 전송</button>
				</span>				
			</div>
			<div class="sign_addon"></div>
		</div>
	</div>
</template>
<script setup>
import { csrfReset } from '../../../js/csrfReset.js';
import Swal from "sweetalert2";
import { ref, onMounted } from 'vue';

const email = ref('');

function findId() {
	if(email == '') {
		Swal.fire({
			title: '이메일을 입력 해 주세요.',
			confirmButtonColor: '#3085d6',
			confirmButtonText: '확인',
		});
	}
	
	csrfReset().then(resolve => {
		axios.post(document.getElementById('idCheck').getAttribute('data'), {
			email: email.value,
			_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
		}).then((response) => {
			if(response.data.status == 'success') {
				Swal.fire({
					title: response.data.msg,
					confirmButtonColor: '#3085d6',
					confirmButtonText: '확인',
					allowOutsideClick: false,
					allowEscapeKey: false,
				}).then(result => {
					if(result.isConfirmed) {
						location.href = document.getElementById('loginUrl').getAttribute('data');
					}
				});
			} else {
				Swal.fire({
					title: response.data.msg,
					confirmButtonColor: '#3085d6',
					confirmButtonText: '확인',
				});
			}
		});
	});
}
</script>