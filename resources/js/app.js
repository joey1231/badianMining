/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue');

require('./bootstrap');
import locale from 'element-ui/lib/locale/lang/en'

import Element from 'element-ui'

Vue.use(Element, { locale })

import noAccess from './pages/noaccess.vue'; 
Vue.component('no-access',noAccess);

//Dashboard
import dashboard from './pages/Dashboard/dashboard.vue'; 
Vue.component('v-dashboard',dashboard);
import dashboardShare from './pages/Dashboard/dashboardShare.vue'; 
Vue.component('v-dashboard-share',dashboardShare);
import dashboardShareOwner from './pages/Dashboard/dashboardShareOwner.vue'; 
Vue.component('v-dashboard-share-owner',dashboardShareOwner);



// Sales
import SalesCreate from './pages/Sales/create.vue'; 
import SalesList from './pages/Sales/list.vue'; 
import SalesView from './pages/Sales/view.vue';
import SalesEdit from './pages/Sales/edit.vue';
Vue.component('v-sales-create',SalesCreate);
Vue.component('v-sales-list',SalesList);
Vue.component('v-sales-view',SalesView);
Vue.component('v-sales-edit',SalesEdit);



// Sharing
import SharingCreate from './pages/Sharing/create.vue'; 
import SharingList from './pages/Sharing/list.vue'; 
import SharingView from './pages/Sharing/view.vue';
import SharingEdit from './pages/Sharing/edit.vue';
Vue.component('v-sharing-create',SharingCreate);
Vue.component('v-sharing-list',SharingList);
Vue.component('v-sharing-view',SharingView);
Vue.component('v-sharing-edit',SharingEdit);
import store from './store/index.js';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
	store,
    el: '#revenuePlusAPP',
});

console.log(window.permissions);