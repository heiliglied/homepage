import { createApp } from 'vue';
import { CkeditorPlugin } from '@ckeditor/ckeditor5-vue';
import login from '../../../components/vue/board/view.vue';

createApp(login).use(CkeditorPlugin).mount("#board");
