<template>
	<section class="section">
		<div class="row">
			<div class="col-12">
				<div class="input-group mb-3">
					<input type="text" v-model="search" class="form-control" placeholder="검색어를 입력하세요." aria-label="Recipient" aria-describedby="button-search">
					<div class="input-group-append">
						<button class="btn btn-outline-secondary" type="button" id="button-search" @click="getBoardList(1, true)"><i class="fas fa-search active"></i></button>
					</div>
				</div>
				<table class="table table-striped">
					<colgroup>
						<col width="8%"/>
						<col width=""/>
						<col width="10%"/>
						<col width="15%"/>
						<col width="15%"/>
					</colgroup>
					<thead>
						<tr>
							<th align="center">번호</th>
							<th align="center">제목</th>
							<th align="center">조회수</th>
							<th align="center">작성자</th>
							<th align="center">작성일</th>
						</tr>
					</thead>
					<tbody>
						<template v-if="lists.length > 0">
						<tr v-for="(list, index) in lists" :key="index" v-on:click="goView(list.id)">
							<td align="center">{{ list.id }}</td>
							<td style="cursor: pointer;">{{ list.subject }}</td>
							<td align="center">{{ list.view }}</td>
							<td align="center">{{ list.writer }}</td>
							<td align="center">{{ list.created_at }}</td>
						</tr>
						</template>
						<template v-else>
						<tr>
							<td colspan="5" align="center" style="font-size: 18px; height: 100px; vertical-align: middle; background-color: white;">해당 데이터가 없습니다.</td>
						</tr>
						</template>
					</tbody>
				</table>
				<nav aria-label="..." class="float-right">
					<template v-if="totalPage != 0">
						<ul class="pagination">
							<li v-if="page > 1" class="page-item active" style="cursor: pointer;">
								<span class="page-link" tabindex="-1" @click="getBoardList(page - 1, true)">이전</span>
							</li>
							<template v-for="(pages, index) in pagination">
								<li v-if="pages == page" class="page-item active">
									<span class="page-link" >{{ pages }} <span class="sr-only">(current)</span></span>
								</li>
								<li v-else class="page-item" style="cursor: pointer;">
									<span class="page-link" @click="getBoardList(pages, true)">{{ pages }}</span>
								</li>
							</template>
							<li v-if="page < totalPage" class="page-item active" style="cursor: pointer;">
								<span class="page-link" tabindex="-1" @click="getBoardList(page + 1, true)">다음</span>
							</li>
						</ul>
					</template>
				</nav>
			</div>
			<div class="col-12 text-right">
				<button class="btn btn-primary" type="button" @click="goWrite()">글쓰기</button>
			</div>
		</div>
	</section>
</template>

<script setup>
import { csrfReset } from '../../../js/csrfReset.js';
import Swal from 'sweetalert2';
import { ref, onMounted } from 'vue';

const lists = ref([]);
const pagination = ref([]);
const totalPage = ref();
const page = ref(1);
const search = ref('');

function goWrite() {
	location.href=document.getElementById('writeUrl').getAttribute('data');
}

async function getBoardList(togglePage, push) {
	page.value = togglePage;
	axios.get(document.getElementById('listUrl').getAttribute('data'), {
		params: {
			page: page.value,
			search: search.value,
		}
	}).then((response) => {
		if(push == true) {
			history.pushState({}, null, '/board' + '?page=' + togglePage + '&search=' + search.value);
		}
		lists.value = response.data.filteredList;
		pagination.value = response.data.pageList.pages;
		totalPage.value = response.data.pageList.totalPage;
	});
}

onMounted(() => {
	getBoardList(1, false);
});

window.onpopstate = function (event){
	let getPage = getParam('page');
	let getSearch = getParam('search');
	getSearch = decodeURI(getSearch);
	
	if(getPage == '') {
		getPage = 1;
	} else {
		page.value = getPage;
	}
	
	search.value = getSearch;
	
	getBoardList(getPage, false);
}

function getParam(sname) {
    let params = location.search.substr(location.search.indexOf("?") + 1);
	let sval = "";
	let temp = [];
	params = params.split("&");
	for (let i = 0; i < params.length; i++) {
		temp = params[i].split("=");
		if ([temp[0]] == sname) { sval = temp[1]; }
	}
    return sval;
}

function goView(id) {
	let url = document.getElementById('viewUrl').getAttribute('data');
	url = url.replace(':id', id);
	location.href=url;
}
</script>