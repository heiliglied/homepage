<template>
	<section class="section">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						{{ permission == 'Y' ? board.subject : (board.censorship == 'N' ? board.subject : '검열되었습니다.') }}
						<span style="float: right;">{{ board.writer }}</span>
					</div>
					<div class="card-body" v-html="permission == 'Y' ? board.contents : (board.censorship == 'N' ? board.contents : '검열되었습니다.')"></div>
					<div class="card-footer text-right">
						<div class="file_list" v-if="files.length > 0 && permission == 'Y'">
							<ul>
								<li v-for="(file, index) in files" @click="file_get(file.id)">{{ file.original_name }}</li>
							</ul>
						</div>
						
						<button type="button" class="btn btn-success" v-if="permission == 'Y'" @click="goModify()">수정</button>&nbsp;
						<button type="button" class="btn btn-primary" v-if="admin == 'Y'" @click="censorship()">검열</button>&nbsp;
						<button type="button" class="btn btn-danger" v-if="permission == 'Y'" @click="delBoard()">삭제</button>&nbsp;
						<button type="button" @click="goList()" class="btn btn-primary dis_check">목록</button>
					</div>					
				</div>
				<div class="col-12">
					<br/>
					<div class="form-group">
						<ckeditor :editor="editor" v-model="editorData" :config="editorConfig"  />
					</div>
					<div class="form-group text-right" style="border-bottom: solid 1px red;">
						<button type="button" @click="write_reply()" class="btn btn-primary">덧글 작성</button>
					</div>
				</div>
			</div>
			<div class="col-12">
				<br/>
				<section id="replys">
					<ul>
						<li v-for="(r, i) in reply">
							<div class="reply_form">
								<div class="reply_personal">
									<div class="replyer">{{ r.writer }}</div>
									<div class="reply_date">{{ r.updated_at }}</div>
								</div>
								<div class="reply_text" v-html="(admin == 'Y' && r.user_id == user_id) ? r.reply : (r.censorship == 'N' ? r.reply : '검열되었습니다.')"></div>
								<div class="reply_option text-right">
									<button type="button" class="btn btn-sm btn-info dis_check" @click="setReplyCensor(r.id, r.censorship)" v-if="admin == 'Y' && r.censorship == 'N'">검열</button>
									<button type="button" class="btn btn-sm btn-info dis_check" @click="setReplyCensor(r.id, r.censorship)" v-else-if="admin == 'Y' && r.censorship == 'Y'">검열해제</button>
									<button type="button" class="btn btn-sm btn-danger dis_check" v-if="admin == 'Y' && r.user_id == user_id" @click="replyDelete(r.id)">삭제</button>
								</div>
							</div>
						</li>
					</ul>
					<nav aria-label="..." class="float-right">
						<template v-if="reply_total != 0">
						<ul class="pagination">
							<li v-if="reply_page > 1" class="page-item active" style="cursor: pointer;">
								<span class="page-link" tabindex="-1" @click="getReplyList(reply_page - 1)">이전</span>
							</li>
							<template v-for="(pages, index) in reply_paging">
								<li v-if="pages == reply_page" class="page-item active">
									<span class="page-link" >{{ pages }} <span class="sr-only">(current)</span></span>
								</li>
								<li v-else class="page-item" style="cursor: pointer;">
									<span class="page-link" @click="getReplyList(pages)">{{ pages }}</span>
								</li>
							</template>
							<li v-if="reply_page < reply_total" class="page-item active" style="cursor: pointer;">
								<span class="page-link" tabindex="-1" @click="getReplyList(reply_page + 1)">다음</span>
							</li>
						</ul>
					</template>
					</nav>
				</section>
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

const editor = ClassicEditor;
const editorData = ref();

const board = ref([]);
const permission = ref();
const files = ref([]);
const admin = ref();
const reply = ref([]);
const reply_total = ref();
const reply_paging = ref([]);
const reply_page = ref(1);
const reply_permission = ref();
const user_id = ref();

const editorConfig = {
	resize_enabled: true, 
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
	axios.get(document.getElementById('viewUrl').getAttribute('data'), {
		params: {
			id: document.getElementById('boardID').getAttribute('data')
		}
	}).then(response => {
		board.value = response.data.board;
		permission.value = response.data.permission;
		files.value = response.data.files;
		admin.value = response.data.admin;
		reply.value = response.data.reply;
		reply_total.value = response.data.reply_total;
		reply_paging.value = response.data.reply_paging;
		user_id.value = response.data.user_id;
	});
});

function goList() {
	location.href=document.getElementById('listUrl').getAttribute('data');
}

function goModify() {
	let modifyUrl = document.getElementById('modifyUrl').getAttribute('data');
	modifyUrl = modifyUrl.replace(':id', board.value.id);
	location.href=modifyUrl;
}

async function delBoard() {
	let delUrl = document.getElementById('deleteUrl').getAttribute('data');
	delUrl = delUrl.replace(':id', board.value.id);
	
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
					if(response.data.status == 'false') {
						Swal.fire({
							title: response.data.msg,
							confirmButtonColor: '#3085d6',
							confirmButtonText: '확인',
						});
					}
					goList();
				});
			});
		}
	});
}

async function censorship() {
	let censorUrl = document.getElementById('censorUrl').getAttribute('data');
	censorUrl = censorUrl.replace(':id', board.value.id);

	Swal.fire({
		title: '게시글을 검열하시겠습니까?',
		confirmButtonColor: '#3085d6',
		confirmButtonText: '확인',
		allowOutsideClick: false,
		allowEscapeKey: false,
		showCancelButton: true,
		cancelButtonText: '취소',
	}).then(result => {
		if(result.isConfirmed) {
			//csrfReset().then(resolve => {
				axios.patch(censorUrl).then((response) => {
					Swal.fire({
						title: response.data.msg,
						confirmButtonColor: '#3085d6',
						confirmButtonText: '확인',
					});
				});
			//});
		}
	});
}

function file_get() {
	let downUrl = document.getElementById('downloadFileUrl').getAttribute('data');
	downUrl = downUrl.replace(':id', id);
	location.href=downUrl;
}

function write_reply() {
	Swal.fire({
		title: '덧글을 작성하시겠습니까?',
		confirmButtonColor: '#3085d6',
		confirmButtonText: '확인',
		allowOutsideClick: false,
		allowEscapeKey: false,
		showCancelButton: true,
		cancelButtonText: '취소',
	}).then(result => {
		if(result.isConfirmed) {
			if(editorData.value == '') {
				return false;
			}
			getReplyList(1);
			csrfReset().then(resolve => {
				axios.post(document.getElementById('commentUrl').getAttribute('data'), {
					boardID: board.value.id,
					contents: editorData.value,
					_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
				}).then((response) => {
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

function setReplyCensor(reply_id, censorship) {
	let reCensorUrl = document.getElementById('reCensorUrl').getAttribute('data');
	reCensorUrl = reCensorUrl.replace(':id', reply_id);
	
	let title = '';
	if(censorship == 'N') {
		title = '덧글을 검열 하시겠습니까?';
	} else {
		title = '덧글의 검열을 해지 하시겠습니까?';
	}

	Swal.fire({
		title: title,
		confirmButtonColor: '#3085d6',
		confirmButtonText: '확인',
		allowOutsideClick: false,
		allowEscapeKey: false,
		showCancelButton: true,
		cancelButtonText: '취소',
	}).then(result => {
		if(result.isConfirmed) {
			getReplyList(1);
			//csrfReset().then(resolve => {
				axios.patch(reCensorUrl).then((response) => {
					Swal.fire({
						title: response.data.msg,
						confirmButtonColor: '#3085d6',
						confirmButtonText: '확인',
					});
				});
			//});
		}
	});
}

function replyDelete(reply_id) {
	let delReplyUrl = document.getElementById('delReplyUrl').getAttribute('data');
	delReplyUrl = delReplyUrl.replace(':id', reply_id);
	
	Swal.fire({
		title: '덧글을 삭제 하시겠습니까?',
		confirmButtonColor: '#3085d6',
		confirmButtonText: '확인',
		allowOutsideClick: false,
		allowEscapeKey: false,
		showCancelButton: true,
		cancelButtonText: '취소',
	}).then(result => {
		if(result.isConfirmed) {
			csrfReset().then(resolve => {
				axios.delete(delReplyUrl, {
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					}
				}).then((response) => {
					getReplyList(1);
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

function getReplyList(page) {
	reply_page.value = page;
	axios.get(document.getElementById('replyUrl').getAttribute('data'), {
		params: {
			page: reply_page.value,
			boardID: board.value.id,
		}
	}).then((response) => {
		reply.value = response.data.filteredList;
		reply_paging.value = response.data.pageList.pages;
		reply_total.value = response.data.pageList.totalPage;
	});
}
</script>
