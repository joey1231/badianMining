import Vue from 'vue';
import Vuex from 'vuex';

import * as actions from './actions.js';
import * as getters from './getters.js';

import uploader from './uploader/uploader.js';


Vue.use(Vuex);


export default new Vuex.Store({
	actions,
	getters,

	modules: {
		uploader,
	}
});