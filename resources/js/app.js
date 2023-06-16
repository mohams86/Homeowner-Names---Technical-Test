/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import { createApp } from 'vue';

const Vue = createApp({});

import FileUploadComponent from './components/FileUploadComponent.vue';
Vue.component('file-upload-component', FileUploadComponent);
Vue.mount('#app');