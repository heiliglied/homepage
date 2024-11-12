<template>
	<div class="contents bg-white">
		<div class="sign_layer">
			<form @submit.prevent="codeCheck">
			<div class="sign_form">				
				<span class="sign_title">인증코드 확인</span>
				<div class="form-group">
					<label for="token">인증코드 입력</label>
					<div class="input-group">
						<input type="text" name="token" v-model="token" class="form-control" required>
					</div>
				</div>
				<div class="form-group" id="error_msg">
					
				</div>
				<span class="sign_btn">
					<button type="submit" class="btn btn-primary">본인인증</button>
				</span>				
			</div>
			</form>
			<div class="sign_addon"></div>
		</div>
	</div>
</template>

<script>
import { csrfReset } from '../../../js/csrfReset.js';
import Swal from "sweetalert2";

export default {
	data() {
		return {
			token: '',
		}
	},
	methods: {
		codeCheck() {
			csrfReset().then(resolve => {
				axios.post(document.querySelector("#checkToken").getAttribute('data'), {
					id: document.querySelector("#user_id").getAttribute('data'),
					email: document.querySelector("#user_email").getAttribute('data'),
					token: this.token,
					_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
				}).then(response => {
					if(response.data.status == 'success') {
						Swal.fire({
							title: response.data.msg,
							text: "",
							type: 'info',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: '확인',
							cancelButtonText: '취소',
						}).then((result) => {
							if(result.value == true) {
								location.href = '/';
							}
						});
					} else {
						document.getElementById("error_msg").style.color = 'red';
						document.getElementById("error_msg").innerText = response.data.msg;
					}
				});
			});
		}
	}
}
</script>