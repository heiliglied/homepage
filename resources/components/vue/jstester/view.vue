<template>
	<div class="menu_area_bar">
		<button type="button" @click="save()">Save</button>
		<button type="button" @click="load()">Load</button>
		<button type="button" @click="run()">Run</button>
	</div>
	<div class="tester_area">
		<div class="flex1 source_area">
			<div class="flex1 full_page blank5">
				<div class="area_header">
					<ul class="tabs">
						<li>HTML</li>
					</ul>
				</div>
				<div class="code_area">
					<div id="html_area" class="code_block">
					</div>
				</div>
			</div>
			<div class="flex1 full_page blank5">
				<div class="area_header">
					<ul class="tabs">
						<li>CSS</li>
					</ul>
				</div>
				<div class="code_area">
					<div id="css_area" class="code_block">
					</div>
				</div>
			</div>
			<div class="flex1 full_page blank5">
				<div class="area_header">
					<ul class="tabs">
						<li>Javascript</li>
					</ul>
				</div>
				<div class="code_area">
					<div id="js_area" class="code_block">
					</div>
				</div>
			</div>				
		</div>
		<div class="frame_area" id="frame_data">
			<iframe id="realod_area" name="reload"></iframe>
		</div>
	</div>
	
	<div id="modal_data" style="display: none;">
		<LoadModalComponent></LoadModalComponent>
	</div>
</template>

<script>
import { csrfReset } from '../../../js/csrfReset.js';
import * as monaco from 'monaco-editor';
import Swal from 'sweetalert2';
import MultiModalControl from '../../../js/multiModalControl.js';
import * as cookieModule from '../../../js/cookie.js';
import LoadModalComponent from './testload.vue';

export default {
	components: {
		LoadModalComponent
	},
	data() {
		return {
			html: '',
			css: '',
			js: '',
			lists: [],
			pagination: '',
			page: 1,
		}
	},
	mounted() {
		window.html_editor = monaco.editor.create(document.getElementById('html_area'), {
			value: this.html ?? '',
			language: 'html',
			theme: 'vs-dark',
		});
		window.css_editor = monaco.editor.create(document.getElementById('css_area'), {
			value: this.css ?? '',
			language: 'css',
			theme: 'vs-dark',
		});
		window.js_editor = monaco.editor.create(document.getElementById('js_area'), {
			value: this.js ?? '',
			language: 'javascript',
			theme: 'vs-dark',
		});
	},
	methods: {
		save() {
			csrfReset().then(resolve => {
				Swal.fire({
					input: 'text',
					inputPlaceholder: '저장할 이름...',
					title: '코드 저장',
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: '저장',
					cancelButtonText: '취소',
				}).then(result => {
					if(result.isConfirmed) {
						let testcode = '';
						let expire = '';
						let old_cookie = cookieModule.getCookie('testcode');
						
						if(old_cookie == undefined) {
							testcode = this.randomStringGenerator(24);
							expire = new Date(Date.now() + 3600000 * 24 * 7).toUTCString();							
							cookieModule.setCookie('testcode', testcode, {expires: expire, path: '/jstester'});
						} else {
							testcode = old_cookie;
						}
						
						axios.post(document.querySelector("#saveUrl").getAttribute('data'), {
							html: window.html_editor.getValue(),
							css: window.css_editor.getValue(),
							js: window.js_editor.getValue(),
							_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
							name: result.value,
							cookie: testcode,
						}).then(complete => {
							console.log(complete);
							Swal.fire({
								title: complete.data.msg,
								confirmButtonColor: '#3085d6',
								cancelButtonColor: '#d33',
								confirmButtonText: '확인'
							});
						});
					}
				});
			});
		},
		load(getPage) {
			csrfReset().then(resolve => {
				axios.post(document.querySelector("#loadUrl").getAttribute('data'), {
					_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
					page: getPage ?? this.page,
					cookie: cookieModule.getCookie('testcode'),
				}).then(result => {
					console.log(result);
					this.lists = result.data.list;
					this.pagination = result.data.paging.pages;
					let modal_layer = new MultiModalControl();
					modal_layer.setModal(1, document.getElementById('codeList'), true, true, false, false);
				});
			});
		},
		run() {
			csrfReset().then(resolve => {
				/*
				let form;
				if(document.getElementsByTagName('form').length > 0) {
					form = document.getElementsByTagName('form')[0];
				} else {
					form = document.createElement('form');
				}
				
				form.setAttribute('method', 'post');
				form.setAttribute('target', 'reload');
				form.setAttribute('action', document.querySelector("#runUrl").getAttribute('data'));
				
				let token_input = document.createElement('input');
				token_input.setAttribute('type', 'hidden');
				token_input.setAttribute('name', '_token');
				token_input.setAttribute('value', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
				
				let html_input = document.createElement('input');
				html_input.setAttribute('type', 'text');
				html_input.setAttribute('name', 'html');
				html_input.setAttribute('value', window.html_editor.getValue());
				
				let css_input = document.createElement('input');
				css_input.setAttribute('type', 'text');
				css_input.setAttribute('name', 'css');
				css_input.setAttribute('value', window.css_editor.getValue());
				
				let js_input = document.createElement('input');
				js_input.setAttribute('type', 'text');
				js_input.setAttribute('name', 'js');
				js_input.setAttribute('value', window.js_editor.getValue());
				
				form.appendChild(token_input);
				form.appendChild(html_input);
				form.appendChild(css_input);
				form.appendChild(js_input);
				
				window.document.body.appendChild(form);
				
				form.submit();
				*/
				
				axios.post(document.querySelector("#runUrl").getAttribute('data'), {
					html: window.html_editor.getValue(),
					css: window.css_editor.getValue(),
					js: window.js_editor.getValue(),
					_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
				}).then(result => {
					document.getElementById("realod_area").src = "data:text/html;charset=utf-8," + escape(result.data);
				});
			});			
		},
		view(id) {
			
		},
		randomStringGenerator(length) {
			const characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
			let result = '';
			const charactersLength = characters.length;
			for(let i = 0; i < length; i++) {
				result += characters.charAt(Math.floor(Math.random() * charactersLength));
			}			
			return result;
		},
	}
}
</script>