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
					<div id="html_area" class="code_block" ref="htmlEditorReference">
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
					<div id="css_area" class="code_block" ref="cssEditorReference">
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
					<div id="js_area" class="code_block" ref="jsEditorReference">
					</div>
				</div>
			</div>				
		</div>
		<div class="frame_area" id="frame_data">
			<iframe id="realod_area" name="reload"></iframe>
		</div>
	</div>
	
	<div id="codeLayer" style="display: none;">
		<LoadModalComponent 
			v-model:codeLists="codeLists" 
			v-model:pagination="pagination"
			@view="testView" 
			@close="closeModal"
			@delete="deleteCode"
			@load="load"
		></LoadModalComponent>
	</div>
</template>

<script setup>
import { csrfReset } from '../../../js/csrfReset.js';
import Swal from 'sweetalert2';
import MultiModalControl from '../../../js/multiModalControl.js';
import * as cookieModule from '../../../js/cookie.js';
import LoadModalComponent from './testload.vue';
import { ref, onMounted, toRaw } from 'vue';

import * as monaco from 'monaco-editor';
import editorWorker from 'monaco-editor/esm/vs/editor/editor.worker?worker'
import jsonWorker from 'monaco-editor/esm/vs/language/json/json.worker?worker'
import cssWorker from 'monaco-editor/esm/vs/language/css/css.worker?worker'
import htmlWorker from 'monaco-editor/esm/vs/language/html/html.worker?worker'
import tsWorker from 'monaco-editor/esm/vs/language/typescript/ts.worker?worker'

self.MonacoEnvironment = {
  getWorker(_, label) {
    if (label === 'json') {
      return new jsonWorker()
    }
    if (label === 'css' || label === 'scss' || label === 'less') {
      return new cssWorker()
    }
    if (label === 'html' || label === 'handlebars' || label === 'razor') {
      return new htmlWorker()
    }
    if (label === 'typescript' || label === 'javascript') {
      return new tsWorker()
    }
    return new editorWorker()
  }
}

const htmlEditorInstance = ref(null);
const htmlEditorReference = ref(null);
const cssEditorInstance = ref(null);
const cssEditorReference = ref(null);
const jsEditorInstance = ref(null);
const jsEditorReference = ref(null);

const codeLists = ref([]);
const pagination = ref([]);
const page = ref(1);

const modalControl = new MultiModalControl();

async function save() {
	let testcode = '';
	let expire = '';
	let old_cookie = cookieModule.getCookie('testcode');
	
	if(old_cookie == undefined) {
		testcode = await randomStringGenerator(24);
		expire = new Date(Date.now() + 3600000 * 24 * 7).toUTCString();
	} else {
		testcode = old_cookie;
	}
	
	csrfReset().then(resolve => {
		Swal.fire({
			input: 'text',
			inputPlaceholder: '저장할 이름...',
			title: '코드 저장',
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: '저장',
			cancelButtonText: '취소',
			showCancelButton: true,
		}).then(result => {
			if(result.isConfirmed) {
				axios.post(document.querySelector("#saveUrl").getAttribute('data'), {
					html: toRaw(htmlEditorInstance.value).getValue(),
					css: toRaw(cssEditorInstance.value).getValue(),
					js: toRaw(jsEditorInstance.value).getValue(),
					_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
					name: result.value,
					cookie: testcode,
				}).then(complete => {
					cookieModule.setCookie('testcode', testcode, {expires: expire, path: '/jstester'});
				
					Swal.fire({
						title: complete.data.msg,
						confirmButtonColor: '#3085d6',
						confirmButtonText: '확인'
					});
					
					//toRaw(htmlEditorInstance.value).setValue('');
					//toRaw(cssEditorInstance.value).setValue('');
					//toRaw(jsEditorInstance.value).setValue('');
				});
			}
		});
	});
}

async function load(getPage) {
	csrfReset().then(resolve => {
		let viewPage = getPage ?? page.value;
		axios.post(document.querySelector("#loadUrl").getAttribute('data'), {
			_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
			page: viewPage,
			cookie: cookieModule.getCookie('testcode'),
		}).then(result => {		
			codeLists.value = result.data.list;
			pagination.value = result.data.paging.pages;					
			page.value = viewPage;
			
			modalControl.viewModal(1, 'codeLayer', true, true, false, false);
		});
	});
}

async function run() {
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
		html_input.setAttribute('value', toRaw(htmlEditorInstance.value).getValue());
		
		let css_input = document.createElement('input');
		css_input.setAttribute('type', 'text');
		css_input.setAttribute('name', 'css');
		css_input.setAttribute('value', toRaw(cssEditorInstance.value).getValue());
		
		let js_input = document.createElement('input');
		js_input.setAttribute('type', 'text');
		js_input.setAttribute('name', 'js');
		js_input.setAttribute('value', toRaw(jsEditorInstance.value).getValue(),);
		
		form.appendChild(token_input);
		form.appendChild(html_input);
		form.appendChild(css_input);
		form.appendChild(js_input);
		
		window.document.body.appendChild(form);
		
		form.submit();
		*/
		
		axios.post(document.querySelector("#runUrl").getAttribute('data'), {
			html: toRaw(htmlEditorInstance.value).getValue(),
			css: toRaw(cssEditorInstance.value).getValue(),
			js: toRaw(jsEditorInstance.value).getValue(),
			_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
		}).then(result => {
			document.getElementById("realod_area").src = "data:text/html;charset=utf-8," + escape(result.data);
		});
	});			
}

async function testView(id) {
	csrfReset().then(resolve => {
		axios.post(document.querySelector("#viewUrl").getAttribute('data'), {
			id: id,
			_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
		}).then(result => {
			toRaw(htmlEditorInstance.value).setValue(result.data.html);
			toRaw(cssEditorInstance.value).setValue(result.data.css);
			toRaw(jsEditorInstance.value).setValue(result.data.js);
			document.getElementById("realod_area").src = "data:text/html;charset=utf-8," + escape(result.data.view);
			
			modalControl.disableVail();
			modalControl.hideModal(document.getElementById('codeLayer'), 0);
		});
	});
}

async function deleteCode(id) {
	csrfReset().then(resolve => {
		axios.post(document.querySelector("#deleteUrl").getAttribute('data'), {
			id: id,
			_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
		}).then(result => {
			let msg = '';
			if(result.data == 'success') {
				msg = '삭제 되었습니다';
			} else {
				msg = '삭제에 실패 하였습니다';
			}
			
			Swal.fire({
				title: msg,
				confirmButtonColor: '#3085d6',
				confirmButtonText: '확인',
			}).then(result => {
				if(result.isConfirmed) {
					modalControl.disableVail();
					modalControl.hideModal(document.getElementById('codeLayer'), 0);
				}
			});
		});
	});
}

async function closeModal() {
	modalControl.disableVail();
	modalControl.hideModal(document.getElementById('codeLayer'), 0);
}

async function randomStringGenerator(length) {
	const characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	let result = '';
	const charactersLength = characters.length;
	for(let i = 0; i < length; i++) {
		result += characters.charAt(Math.floor(Math.random() * charactersLength));
	}			
	return result;
}

onMounted(() => {
	htmlEditorInstance.value = monaco.editor.create(htmlEditorReference.value, {
		value: '',
		language: 'html',
		theme: 'vs-dark',
	});
	cssEditorInstance.value = monaco.editor.create(cssEditorReference.value, {
		value: '',
		language: 'css',
		theme: 'vs-dark',
	});
	jsEditorInstance.value = monaco.editor.create(jsEditorReference.value, {
		value: '',
		language: 'javascript',
		theme: 'vs-dark',
	});
});

</script>
