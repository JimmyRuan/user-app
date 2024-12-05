// resources/js/app.js
import '../css/app.css'
import { createApp } from 'vue';
import router from "./router.js";
import store from "./stores/index.js";
import App from "./pages/App.vue";
import '@fortawesome/fontawesome-free/css/all.min.css';

const app = createApp(App);

app.use(router);
app.use(store);
app.mount('#app');
