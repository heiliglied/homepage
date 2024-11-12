<template>
	<div class="contents bg-white">
		<div class="sign_layer">
			<form @submit.prevent="login">
			<div class="sign_form">
				<span class="sign_title">로 그 인</span>
				<div class="form-group">
					<label for="user_id">사용자ID </label>
					<div class="input-group">
						<input type="text" name="user_id" v-model="user_id" class="form-control" required>
					</div>
					<div id="id_status">
					</div>
				</div>
				<div class="form-group">
					<label for="password">비밀번호 </label>
					<input type="password" name="password" value="" v-model="password" id="password" class="form-control" required pattern="(?=^.{8,}$)(?=.*[a-zA-z])(?=.*[0-9]).*$">
					<!--<input type="password" name="password" value="" id="password" class="form-control" required pattern="(?=^.{8,}$)(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$">-->
				</div>
				<div class="form-group">
					<label><input type="checkbox" name="remember" v-model="remember" style="width: auto;">Remember Me.</label>
				</div>
				<div class="form-group" id="error_msg">
					
				</div>
				<span class="sign_btn">
					<button type="submit" class="btn btn-primary">로그인</button>
				</span>
				<br/>
				<a v-bind:href='find_pass'><span class="sign_title">비밀번호를 잊으셨나요?</span></a>
			</div>
			</form>
			<div class="sign_addon"></div>
		</div>
	</div>
</template>

<script>
import { csrfReset } from '../../../js/csrfReset.js';

export default {
	data() {
		return {
			user_id: '',
			password: '',
			remember: '',
			find_pass: document.querySelector("#findPassword").getAttribute('data'),
		}
	},
	methods: {
		login() {
			csrfReset().then(resolve => {
				axios.post(document.querySelector("#loginUrl").getAttribute('data'), {
					user_id: this.user_id,
					password: this.password,
					remember: this.remember,
					_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
				}).then(response => {
					if(response.data.status == 'success') {
						window.location.href = response.data.url;
					} else {
						document.querySelector("#error_msg").innerText = response.data.msg;
					}
				});
			});
		}
	}
}
</script>