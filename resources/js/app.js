
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue'; // Import Vue 3's createApp function

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// You don't need to use Vue.component here, just use app.component
const app = createApp({}); // Create a new Vue app instance

// Register components globally if needed
app.component('example-component', require('./components/ExampleComponent.vue').default);

// Mount Vue to a DOM element (e.g., #app)
app.mount('#app'); // Mount the app to the element with id="app"
