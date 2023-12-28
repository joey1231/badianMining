window._ = require('lodash');

import Vue from 'vue';
/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    //require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');



if(token){
	window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
	
}else{
	console.error('CSRF token not found');
}

var permissions = document.head.querySelector('meta[name="permissions"]');

window.permissions = JSON.parse(permissions.content);
Vue.$http = window.axios;

Vue.filter('capitalize', function (value) {
  if (!value) return ''
  value = value.toString().toLowerCase()
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
  return value;
})

Vue.filter('money', function(value) {
	if(isNaN(value)) {
		return '₱0.00';
	}
	value = parseFloat(value).toFixed(2);  
	if(value >= 0) {
		return '₱'+value.replace(/\d(?=(\d{3})+\.)/g, '$&,');
	}else{
		return "-₱" +(value * -1).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');  
	}
	
	
});

Vue.filter('NumberFormat', function(num) {
    if(isNaN(num)) {
      return '0.00';
    }
    return parseFloat(Math.round(num + "e+2")  + "e-2").toLocaleString(undefined,{minimumFractionDigits:2, maximumFractionDigits:2});
});
Vue.filter('DateFormat', function(num) {
    if (!num) return '';

    return moment(num).format('YYYY-MM-DD');
});
Vue.mixin({
  methods: {
    checkRoles: function (name) {
        for(let i =0 ;i < window.permissions.length; i++){
           if(window.permissions[i].name == name){
                console.log(window.permissions[i].name,name);
              return true;
           }
        }
        return false;
    },
  },
})
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });
