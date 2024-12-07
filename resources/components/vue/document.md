# Vue3 참고사항.

부모 컴포넌트와 하위 컴포넌트간의 데이터 교환.

ref로 선언된 반응형 변수의 경우 
하위 컴포넌트에 v-model:보낼 변수명="받을 변수명" 형식으로 추가함
<Child v-model:send="receive"></Child>

자식 컴포넌트에서는 
3.4 이후에는 defineModel로 받는다.
const use = defineModel('receive');

해당 use 변수로 하위 컴포넌트에서 사용 가능.

하위 컴포넌트에서 상위함수를 사용하는 방법.

하위 컴포넌트에 사용할 함수명으로 해당 함수를 전달해야함.
<Child @사용할함수명="선언된함수명"></Child>
형식으로 전달하면 하위 컴포넌트에서 
인라인일경우 : $emit('함수명', 파라미터 있을 시 파라미터) 형식으로 바로 사용할 수 있음.

하위 컴포넌트 스크립트에서 사용하고 싶을 경우, 
<template @click="call()"></template>
<script>
function call() {
	this.$emit(emit('함수명', 파라미터 있을 시 파라미터);
}
</script>
과 같은 형태로 호출할 수 있음.