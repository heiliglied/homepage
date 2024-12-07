import { createApp } from 'vue';
import { CkeditorPlugin } from '@ckeditor/ckeditor5-vue';
import login from '../../../components/vue/board/write.vue';

createApp(login).use(CkeditorPlugin).mount("#board");
