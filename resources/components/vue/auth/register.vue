<template>
	<div class="contents bg-white">
		<div class="sign_layer">
			<div class="sign_form">
				<span class="sign_title">회 원 가 입</span>
				<input type="hidden" name="enable_id" v-model="enable_id" value="disable">
				<div class="form-group">
					<label for="user_id">사용자ID </label>
					<div class="input-group">
						<input type="email" name="user_id" value="" id="user_id" class="form-control" v-model="user_id" @change="id_change()" required>
						<div class="input-group-prepend">
							<button type="button" class="btn btn-info" @click="check_id()">ID 중복체크</button>
						</div>
					</div>
					<div id="id_status">
					</div>
				</div>
				<div style="color: red;" id="id_checker">
					
				</div>
				<div class="form-group">
					<label for="password">비밀번호 </label>
					<input type="password" name="password" value="" v-model="password" id="password" class="form-control" required pattern="(?=^.{8,}$)(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$">
				</div>
				<div class="form-group">
					<label for="password_confirmation">비밀번호확인 </label>
					<input type="password" name="password_confirmation" value="" v-model="password_confirmation" id="password_confirmation" class="form-control" required pattern="(?=^.{8,}$)(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$">
					<div id="password_status"></div>
				</div>
				<div class="form-group">
					<label for="email">이메일 </label>
					<input type="email" name="email" class="form-control" id="email" v-model="email" required>
					<div id="email_status"></div>
				</div>
				<div class="form-group" id="error_message">
					
				</div>
				<span class="sign_btn">
					<button type="button" class="btn btn-primary" @click="regist()">등록</button>
				</span>
			</div>
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
			enable_id: '',
			password: '',
			password_confirmation: '',
			email: '',
		}
	},
	methods: {
		id_change() {
			this.enable_id = '';
		},
		check_id() {
			csrfReset().then(resolve => {
				axios.post(document.querySelector("#findUrl").getAttribute('data'), {
					user_id: this.user_id,
					_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
				}).then(response => {
					if(response.data == 'enable') {
						document.getElementById("id_checker").style.color = 'blue';
						document.getElementById("id_checker").innerText = '사용할 수 있는 ID입니다.';
						this.enable_id.value = 'enable';
					} else if(response.data == 'void') {					
						document.getElementById("id_checker").style.color = 'red';
						document.getElementById("id_checker").innerText = '사용자 아이디를 입력 해 주세요.';
					} else {
						document.getElementById("id_checker").style.color = 'red';
						document.getElementById("id_checker").innerText = '사용할 수 없는 ID입니다.';
					}
				});
			});
		},
		regist() {
			if(this.enable_id != 'enable') {
				document.querySelector("#error_message").innerText = '사용 가능한 아이디를 먼저 확인 해 주세요.';
				return false;
			}
		
			axios.post(document.querySelector("#signUp").getAttribute('data'), {
				user_id: this.user_id,
				password: this.password,
				password_confirmation: this.password_confirmation,
				email: this.email
			}).then(response => {
				if(response.data.status == 'error') {
					document.querySelector("#error_message").innerText = response.data.msg;
				}
			});
		}
	}
}
</script>