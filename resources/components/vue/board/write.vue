<template>
	<section class="section">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						글 작성
					</div>
					<div class="card-body">
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" id="basic-addon1"> 제목 </span>
							</div>
							<input type="text" class="form-control" placeholder="글 제목을 입력해 주세요" name="subject" v-model="title" required>
						</div>
						<div class="form-group">
							<ckeditor :editor="editor" v-model="editorData" :config="editorConfig" />
						</div>
						<div v-if="uploadedFiles.length > 0" class="form-group">
							<ul>
								<li v-for="(files, index) in uploadedFiles">
									<a href="#" @click="file_get(files.id)">{{files.original_name}}</a> <button type="button" @click="delFile(files.id)">삭제</button>
								</li>
							</ul>
						</div>
						<div class="input-group mb-3">
							<div class="custom-file">
								<input type="file" name="files[]" class="custom-file-input" id="files" aria-describedby="fileUploadAddon" multiple lang="ko" @change="changeFile()">
								<label class="custom-file-label" for="fileUploadAddon">파일을 선택하세요.</label>
							</div>
						</div>
					</div>
					<div class="card-footer text-right">
						<button type="button" @click="scriptSubmit()" class="btn btn-success">등록</button>
						&nbsp;
						<button type="button" @click="goList()" class="btn btn-primary">목록</button>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
import { csrfReset } from '../../../js/csrfReset.js';
import Swal from 'sweetalert2';
import { ref, onMounted } from 'vue';
import { 
	ClassicEditor, Bold, Essentials, Italic, Mention, Paragraph, Undo, 
	BlockQuote, Link, Indent, List, MediaEmbed, Table, TableColumnResize, 
	TableToolbar, TextTransformation, Alignment, Font, CodeBlock, 
	Image, ImageCaption, ImageStyle, ImageToolbar, ImageUpload, ImageResize,
	
} from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';
import CustomUploadAdapter from '../../../js/CustomUploadAdapter.js';

const title = ref('');
const editor = ClassicEditor;
const editorData = ref();
const uploadedFiles = ref([]);

function CustomUploadPlugin(editor) {
	editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
		return new CustomUploadAdapter(loader, document.getElementById('uploadUrl').getAttribute('data'));
	}
}

const editorConfig = {
	resize_enabled: true, 
	extraPlugins: [CustomUploadPlugin],
    plugins: [		
        Essentials,
        Bold,
        Italic,
        Link,
        Paragraph,
        BlockQuote,
        Indent,
        List,
        Table,
        TableToolbar,
        TableColumnResize,
        TextTransformation,
        Alignment,
        Image,
        ImageCaption,
        ImageStyle,
        ImageToolbar,
        ImageUpload,
        ImageResize,
        MediaEmbed,
		Font,
		CodeBlock,
    ],
    toolbar: {
        items: [
			'fontSize',
			'fontColor',
            'bold',
            'italic',
            'link',
            'imageUpload',
            'indent',
            'outdent',
            'numberedList',
            'bulletedList',
            'alignment',
            'blockQuote',
            'mediaEmbed',
            'undo',
            'redo',
            'insertTable',
			'codeBlock',
        ],
    },
    image: {
        toolbar: [
            'imageTextAlternative',
            'toggleImageCaption',
            'imageStyle:inline',
            'imageStyle:block',
            'imageStyle:side',
        ],
    },
    table: {
        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties'],
    },
	fontSize: {
		options: [8, 9, 10, 11, 12, 13, 14, 15, 20, 30],
		unit: 'px',
	},
};

onMounted(() => {
	if(document.getElementById('uploadType').getAttribute('data') == 'update') {
		axios.get(document.getElementById('getUrl').getAttribute('data'), {
			params: {
				id: document.getElementById('boardID').getAttribute('data')
			}
		}).then(response => {
			title.value = response.data.board.subject;
			editorData.value = response.data.board.contents;
			uploadedFiles.value = response.data.files;
		});
	}
});

function changeFile() {
	let trigger = document.getElementById("files");

	let fileName = trigger.files;
	let fileList = '';
	Array.prototype.forEach.call(fileName, function(f, i) {
		if(i != 0) {
			fileList += ', ';
		}
		fileList += f.name;
	});
	
	let nextSibling = trigger.nextElementSibling;
	nextSibling.innerText = fileList;
}

function scriptSubmit() {
	if(title == '') {
		Swal.fire({
			title: '제목을 입력 해 주세요.',
			confirmButtonColor: '#3085d6',
			confirmButtonText: '확인',
		});
	}
	
	csrfReset().then(resolve => {
		const data = new FormData();
		data.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
		data.append('type', document.getElementById('uploadType').getAttribute('data'));
		data.append('boardID', document.getElementById('boardID').getAttribute('data'));
		data.append('subject', title.value);
		data.append('contents', editorData.value);
		
		let files = document.getElementById('files').files;
		for(let i = 0; i < files.length; i++) {
			data.append('files[]', files[i]);
		}
		
		axios.post(document.getElementById('boardRegist').getAttribute('data'), data, {
			headers: {
				"Content-Type": "multipart/form-data",
			},
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
						location.href = document.getElementById('listUrl').getAttribute('data');
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

async function delFile(id) {
	let delUrl = document.getElementById('deleteFileUrl').getAttribute('data');
	delUrl = delUrl.replace(':id', id);
	
	Swal.fire({
		title: '파일을 삭제 하시겠습니까?',
		confirmButtonColor: '#3085d6',
		confirmButtonText: '확인',
		allowOutsideClick: false,
		allowEscapeKey: false,
		showCancelButton: true,
		cancelButtonText: '취소',
	}).then(result => {
		if(result.isConfirmed) {
			csrfReset().then(resolve => {
				axios.delete(delUrl, {
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					}
				}).then((response) => {
					if(response.data.status == 'success') {
						uploadedFiles.value = response.data.files;						
					}
					
					Swal.fire({
						title: response.data.msg,
						confirmButtonColor: '#3085d6',
						confirmButtonText: '확인',
					});
				});
			});
		}
	});
}

function file_get(id) {
	let downUrl = document.getElementById('downloadFileUrl').getAttribute('data');
	downUrl = downUrl.replace(':id', id);
	
	location.href=downUrl;
}

function goList() {
	location.href = document.getElementById('listUrl').getAttribute('data');
}
</script>